#!/bin/bash
set -e

echo "Running production optimizations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Preparing SQLite database..."
touch /var/www/html/database/database.sqlite
chmod -R 777 /var/www/html/database
chown -R www-data:www-data /var/www/html/database
chmod 666 /var/www/html/database/database.sqlite

echo "Running database migrations..."
php artisan migrate --force

echo "Seeding database..."
php artisan db:seed --force

echo "Starting Apache..."
apache2-foreground
