<?php

$config['app'] = [
    'services' => [
        HtmlHelper::class
    ],
    'routeMiddleware' => [
        'san-pham' => AuthMiddleware::class,
    ],
    'globalMiddleware' => [
        ParamsMiddleware::class,
    ],
    'boot' => [
        AppServiceProvider::class,
    ],
];
