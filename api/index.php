<?php

// Check for vendor/autoload
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    echo "Composer dependencies are not installed on Vercel!";
    die();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vercel only allows writing to /tmp
$_ENV['VIEW_COMPILED_PATH'] = '/tmp';
$_ENV['APP_CONFIG_CACHE'] = '/tmp/config.php';

// Force use of SSL on Vercel
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

require __DIR__ . '/../public/index.php';
