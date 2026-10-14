<?php
error_reporting(E_ALL);
sql_autoload_register(); // memicu error jika perlu atau test sederhana

echo "TEST 1: File executed.<br>";

$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
    echo "TEST 2: Autoload loaded.<br>";
} else {
    echo "TEST 2 ERROR: Autoload missing.<br>";
}

$appFile = __DIR__ . '/../bootstrap/app.php';
if (file_exists($appFile)) {
    $app = require_once $appFile;
    echo "TEST 3: Bootstrap app loaded successfully!<br>";
} else {
    echo "TEST 3 ERROR: bootstrap/app.php missing.<br>";
}
exit;