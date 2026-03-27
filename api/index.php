<?php

// Check for vendor/autoload
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    echo "Composer dependencies are not installed on Vercel!";
    die();
}

// Laravel needs a writable folder for storage
// Vercel only provides /tmp
$storagePath = '/tmp/storage';
if (!is_dir($storagePath)) {
    @mkdir($storagePath, 0777, true);
    @mkdir($storagePath . '/framework/views', 0777, true);
    @mkdir($storagePath . '/framework/sessions', 0777, true);
    @mkdir($storagePath . '/framework/cache', 0777, true);
    @mkdir($storagePath . '/app', 0777, true);
    @mkdir($storagePath . '/logs', 0777, true);
}

// Ensure critical folders exist every time
@mkdir($storagePath . '/framework/views', 0777, true);

// Force environment variables for Vercel
putenv('APP_ENV=production');
putenv('APP_DEBUG=true');
putenv('VIEW_COMPILED_PATH=' . $storagePath . '/framework/views');
putenv('SESSION_DRIVER=cookie');
putenv('LOG_CHANNEL=stderr');
$_ENV['APP_STORAGE_PATH'] = $storagePath;

require __DIR__ . '/../public/index.php';
