<?php

return [
    'fallback_enabled' => env('DB_FALLBACK_ENABLED', true),

    'storage_path' => storage_path('app/database_fallback'),

    'cache_enabled' => true,
    'cache_duration' => 3600, // 1 soat

    'demo_mode' => env('DEMO_MODE', false),

    'static_data' => [
        'users' => [
            [
                'id' => 1,
                'name' => 'Demo User',
                'email' => 'demo@example.com',
                'role' => 'admin'
            ]
        ],
        'courses' => [
            [
                'id' => 1,
                'title' => 'Demo Course',
                'description' => 'This is a demo course',
                'price' => 0
            ]
        ]
    ]
];