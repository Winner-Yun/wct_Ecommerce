<?php

protected $middlewareGroups = [
    'web' => [

    ],

    'api' => [
        'api' => [
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        'throttle:api',
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ],
];
