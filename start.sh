#!/bin/sh

# Run migrations
php artisan migrate --force

# Start Apache
apache2-foreground
