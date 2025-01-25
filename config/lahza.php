<?php

return [
    'api_key' => env('LAHZA_API_KEY'),
    'base_url' => env('LAHZA_BASE_URL', 'https://api.lahza.io/'),
    'documentation_base_url' => env('LAHZA_DOCS_URL', 'https://api-docs.lahza.io/errors/'),

    'timeout' => env('LAHZA_TIMEOUT', 15),
    'retries' => env('LAHZA_RETRIES', 3),
    'retry_delay' => env('LAHZA_RETRY_DELAY', 100),
    'webhook' => [
        'secret' => env('LAHZA_WEBHOOK_SECRET'),
        'middleware' => ['api'],
    ],
    'currencies' => explode(',', env('LAHZA_CURRENCIES', 'USD,ILS,JOD')),
];
