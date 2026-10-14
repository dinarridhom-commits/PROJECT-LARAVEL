<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

echo "1. API Index Loaded Successfully.<br>";

$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require $autoloadPath;
    echo "2. Autoload Loaded Successfully.<br>";
} else {
    die("2. ERROR: vendor/autoload.php not found!");
}

$appPath = __DIR__ . '/../bootstrap/app.php';
if (file_exists($appPath)) {
    $app = require_once $appPath;
    echo "3. Bootstrap App Loaded Successfully.<br>";
} else {
    die("3. ERROR: bootstrap/app.php not found!");
}

if (is_object($app)) {
    echo "4. Laravel Application Object Created Successfully!";
} else {
    echo "4. ERROR: Application object is invalid.";
}