FROM php:8.4-fpm-trixie

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libzip-dev zlib1g-dev locales locales-all \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install zip pdo pdo_mysql

RUN sed -i 's/^pm.max_children.*/pm.max_children = 60/' /usr/local/etc/php-fpm.d/www.conf \
   && echo "request_slowlog_timeout = 5s" >> /usr/local/etc/php-fpm.d/www.conf \
   && echo "slowlog = /usr/local/var/log/php-fpm/slow.log" >> /usr/local/etc/php-fpm.d/www.conf \
   && mkdir -p /usr/local/var/log/php-fpm /var/lib/php/sessions \
   && chown www-data:www-data /usr/local/var/log/php-fpm /var/lib/php/sessions

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Session files live on a dedicated Docker volume instead of the RAM-backed
# /tmp. Garbage collection is performed deterministically by session-cleaner.
RUN printf '%s\n' \
        'session.upload_progress.enabled = 0' \
        'session.save_path = "/var/lib/php/sessions"' \
        'session.gc_probability = 0' \
        'session.gc_maxlifetime = 1440' \
        > "$PHP_INI_DIR/conf.d/aqi-session.ini"
