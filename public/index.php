<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Detect Laravel base path by checking common layouts
$candidateBasePaths = [
    __DIR__,                 // vendor in ./vendor (app root is public_html)
    __DIR__ . '/..',         // vendor in ../vendor (app root one level up)
    __DIR__ . '/../backend', // vendor in ../backend/vendor (app root in ../backend)
];

$laravelBasePath = null;
foreach ($candidateBasePaths as $base) {
    if (is_file($base . '/vendor/autoload.php')) {
        $laravelBasePath = $base;
        break;
    }
}

if (!$laravelBasePath) {
    http_response_code(500);
    echo 'Laravel base path not found. Ensure vendor/autoload.php exists.';
    exit(1);
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $laravelBasePath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $laravelBasePath . '/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $laravelBasePath . '/bootstrap/app.php';

$app->handleRequest(Request::capture());