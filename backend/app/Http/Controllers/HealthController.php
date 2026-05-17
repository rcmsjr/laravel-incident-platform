<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Throwable;

class HealthController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): JsonResponse
    {
        $services = [
            'database' => $this->checkDatabase(),
            'redis' => $this->checkRedis(),
            'storage' => $this->checkStorage(),
        ];

        $overall = in_array('down', $services, true) ? 'degraded' : 'ok';

        return response()->json([
            'status' => $overall,
            'services' => $services,
            'timestamp' => now(),
            'env' => app()->environment(),
        ], $overall === 'ok' ? 200 : 503);
    }

    private function checkDatabase(): string
    {
        try {
            DB::connection()->getPdo();
            DB::select('select 1');

            return 'up';
        } catch (Throwable $e) {
            Log::error('Health check: database failed', [
                'message' => $e->getMessage(),
            ]);

            return 'down';
        }
    }

    private function checkRedis(): string
    {
        try {
            Redis::ping();
            Redis::llen('queues:default');

            return 'up';
        } catch (Throwable $e) {
            Log::error('Health check: redis failed', [
                'message'  => $e->getMessage(),
            ]);

            return 'down';
        }
    }

    private function checkStorage(): string
    {
        try {
            Storage::disk('s3')->put('health-check.txt', 'ok');
            Storage::disk('s3')->exists('health-check.txt');
            Storage::disk('s3')->delete('health-check.txt');

            return 'up';
        } catch (Throwable $e) {
            Log::error('Health check: storage failed', [
                'message'  => $e->getMessage(),
                'endpoint' => config('filesystems.disks.s3.endpoint'),
                'bucket'   => config('filesystems.disks.s3.bucket'),
                'region'   => config('filesystems.disks.s3.region'),
                'key'      => config('filesystems.disks.s3.key'),
            ]);

            return 'down';
        }
    }
}
