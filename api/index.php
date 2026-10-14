<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

echo "DEBUG A: Starting script.<br>";

$_ENV['APP_STORAGE'] = '/tmp/storage';

if (!is_dir('/tmp/storage')) {
    @mkdir('/tmp/storage', 0777, true);
    @mkdir('/tmp/storage/framework', 0777, true);
    @mkdir('/tmp/storage/framework/views', 0777, true);
    @mkdir('/tmp/storage/framework/cache', 0777, true);
    @mkdir('/tmp/storage/framework/sessions', 0777, true);
    @mkdir('/tmp/storage/logs', 0777, true);
}

echo "DEBUG B: Storage prepared.<br>";

$publicIndex = __DIR__ . '/../public/index.php';
if (file_exists($publicIndex)) {
    echo "DEBUG C: public/index.php found, requiring now...<br>";
    try {
        require_once $publicIndex;
        echo "DEBUG D: public/index.php executed successfully.<br>";
    } catch (\Throwable $e) {
        echo "<br><b>LARRUNTIME ERROR:</b> " . $e->getMessage() . "<br>";
        echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
} else {
    echo "DEBUG C ERROR: public/index.php not found!<br>";
}