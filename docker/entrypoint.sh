#!/bin/sh
set -e

# Start PHP-FPM in background
echo "Starting PHP-FPM..."
FPM_BIN=$(which php-fpm || find /usr/sbin -name "php-fpm*" | head -n 1)
if [ -n "$FPM_BIN" ]; then
  $FPM_BIN -D
else
  echo "ERROR: PHP-FPM not found!" >&2
  exit 1
fi

# Start OpenResty in foreground
echo "Starting OpenResty..."
exec /usr/local/openresty/bin/openresty -g "daemon off;"
