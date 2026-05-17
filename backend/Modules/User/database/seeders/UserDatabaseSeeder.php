<?php

declare(strict_types=1);

namespace Modules\User\Database\Seeders;

use Modules\User\Models\User;
use Modules\User\Enums\UserRole;
use Illuminate\Database\Seeder;

class UserDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'super.admin@example.com')->first();

        if (!$user) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'super.admin@example.com',
                'password' => bcrypt('secret'),
                'email_verified_at' => now(),
                'role' => UserRole::Admin,
                'is_active' => true,
            ]);
        }
    }
}
