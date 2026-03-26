<?php
// Vercel only allows writing to /tmp
$_ENV['VIEW_COMPILED_PATH'] = '/tmp';

// Force use of SSL on Vercel
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

require __DIR__ . '/../public/index.php';
