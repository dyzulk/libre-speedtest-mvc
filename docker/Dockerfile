FROM openresty/openresty:alpine

# Install PHP, PHP-FPM, extensions, composer, and utilities
RUN apk add --no-cache \
    bash \
    ca-certificates \
    curl \
    php \
    php-fpm \
    php-ctype \
    php-phar \
    php-gd \
    php-openssl \
    php-pdo \
    php-pdo_mysql \
    php-pdo_pgsql \
    php-pdo_sqlite \
    php-pgsql \
    php-session \
    php-sqlite3 \
    php-mbstring \
    php-json \
    composer

# Configure PHP-FPM to listen on 127.0.0.1:9000 (standard FastCGI port)
RUN set -eu; \
    fpm_conf=$(find /etc/php* -name www.conf | head -n 1); \
    if [ -z "$fpm_conf" ]; then \
      echo "ERROR: Could not find www.conf for php-fpm" >&2; \
      exit 1; \
    fi; \
    sed -i 's/listen = .*/listen = 127.0.0.1:9000/g' "$fpm_conf"

# Install custom PHP ini (upload limits + disable output buffering)
COPY docker/php.ini /tmp/speedtest-php.ini
RUN set -eu; \
    scan_dir="$(php -r 'echo rtrim(PHP_CONFIG_FILE_SCAN_DIR);')"; \
    if [ -z "$scan_dir" ]; then \
      echo "ERROR: PHP_CONFIG_FILE_SCAN_DIR is empty" >&2; \
      exit 1; \
    fi; \
    install -m 0644 /tmp/speedtest-php.ini "$scan_dir/99-speedtest.ini"; \
    rm /tmp/speedtest-php.ini

# Prepare web directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html

# Run composer installation
RUN composer install --no-dev --optimize-autoloader

# Copy Nginx config
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# Setup entrypoint script
COPY docker/entrypoint.sh /entrypoint.sh
RUN sed -i 's/\r$//' /entrypoint.sh && chmod +x /entrypoint.sh

# Expose port
EXPOSE 8080

# Run services via entrypoint
CMD ["/entrypoint.sh"]
