<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

echo "STEP 1: Start public/index.php<br>";

$_ENV['APP_STORAGE'] = '/tmp/storage';

if (!is_dir('/tmp/storage')) {
    @mkdir('/tmp/storage', 0777, true);
    @mkdir('/tmp/storage/framework', 0777, true);
    @mkdir('/tmp/storage/framework/views', 0777, true);
    @mkdir('/tmp/storage/framework/cache', 0777, true);
    @mkdir('/tmp/storage/framework/sessions', 0777, true);
    @mkdir('/tmp/storage/logs', 0777, true);
}

echo "STEP 2: Storage ready<br>";

require __DIR__ . '/../vendor/autoload.php';
echo "STEP 3: Autoload loaded<br>";

$app = require_once __DIR__ . '/../bootstrap/app.php';
echo "STEP 4: Bootstrap app created<br>";

use Illuminate\Http\Request;
$request = Request::capture();
echo "STEP 5: Request captured<br>";

try {
    $response = $app->handleRequest($request);
    echo "STEP 6: Request handled successfully!<br>";
    $response->send();
} catch (\Throwable $e) {
    echo "<br><b>HANDLE ERROR:</b> " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// use Illuminate\Foundation\Application;
// use Illuminate\Http\Request;

// define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
// if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
//     require $maintenance;
// }

// Register the Composer autoloader...
// require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
// /** @var Application $app */
// $app = require_once __DIR__.'/../bootstrap/app.php';

// $app->handleRequest(Request::capture());
