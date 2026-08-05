#!/bin/sh
set -e

# Ensure nobody user owns the html directory for sqlite writing
chown -R nobody:nobody /var/www/html

# Start PHP-FPM in background
echo "Starting PHP-FPM..."
FPM_BIN=$(find /usr/sbin -name "php-fpm*" | head -n 1)
if [ -z "$FPM_BIN" ]; then
  FPM_BIN=$(command -v php-fpm 2>/dev/null || true)
fi

if [ ! -z "$FPM_BIN" ]; then
  echo "Found PHP-FPM: $FPM_BIN. Starting..."
  $FPM_BIN -D
else
  echo "ERROR: PHP-FPM not found!" >&2
  exit 1
fi

# Start OpenResty in foreground
echo "Starting OpenResty..."
exec /usr/local/openresty/bin/openresty -g "daemon off;"
