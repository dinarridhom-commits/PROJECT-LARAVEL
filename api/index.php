<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function ($exception) {
    header('Content-Type: text/plain; charset=utf-8', true, 500);
    echo "FATAL EXCEPTION: " . $exception->getMessage() . "\n";
    echo "File: " . $exception->getFile() . ":" . $exception->getLine() . "\n\n";
    echo "Trace:\n" . $exception->getTraceAsString();
    exit(1);
});

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        header('Content-Type: text/plain; charset=utf-8', true, 500);
        echo "SHUTDOWN ERROR: {$error['message']} in {$error['file']} on line {$error['line']}";
    }
});

$_ENV['APP_STORAGE'] = '/tmp/storage';

if (!is_dir('/tmp/storage')) {
    @mkdir('/tmp/storage', 0777, true);
    @mkdir('/tmp/storage/framework', 0777, true);
    @mkdir('/tmp/storage/framework/views', 0777, true);
    @mkdir('/tmp/storage/framework/cache', 0777, true);
    @mkdir('/tmp/storage/framework/sessions', 0777, true);
    @mkdir('/tmp/storage/logs', 0777, true);
}

require __DIR__ . '/../public/index.php';