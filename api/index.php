<?php

// Check for vendor/autoload
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    echo "Composer dependencies are not installed on Vercel!";
    die();
}

// Laravel needs a writable folder for storage
// Vercel only provides /tmp
putenv('VIEW_COMPILED_PATH=/tmp');
putenv('SESSION_DRIVER=cookie');
putenv('LOG_CHANNEL=stderr');

require __DIR__ . '/../public/index.php';
