<?php

return [
    'dsn' => env('SENTRY_LARAVEL_DSN'),

    'traces_sample_rate' => (float) env('SENTRY_TRACES_SAMPLE_RATE', 0.1),

    'environment' => env('SENTRY_ENVIRONMENT', env('APP_ENV', 'production')),

    'release' => env('SENTRY_RELEASE'),

    'send_default_pii' => (bool) env('SENTRY_SEND_DEFAULT_PII', false),
];
