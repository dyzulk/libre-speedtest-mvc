#!/bin/sh
set -e

# Start PHP-FPM in background
echo "Starting PHP-FPM..."
php-fpm -D || php-fpm83 -D || php-fpm82 -D || php-fpm81 -D || php-fpm74 -D || php-fpm -D

# Start OpenResty in foreground
echo "Starting OpenResty..."
exec /usr/local/openresty/bin/openresty -g "daemon off;"
