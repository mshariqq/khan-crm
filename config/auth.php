<?php

return [

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'staff'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'staff'),
    ],

    'guards' => [
        'staff' => [
            'driver' => 'session',
            'provider' => 'staff',
        ],
        'client' => [
            'driver' => 'session',
            'provider' => 'clients',
        ],
    ],

    'providers' => [
        'staff' => [
            'driver' => 'eloquent',
            'model' => App\Models\Staff::class,
        ],
        'clients' => [
            'driver' => 'eloquent',
            'model' => App\Models\Contact::class,
        ],
    ],

    'passwords' => [
        'staff' => [
            'provider' => 'staff',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
        'client' => [
            'provider' => 'clients',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
