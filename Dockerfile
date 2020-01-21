FROM php:7.3-fpm-alpine

RUN apk add zlib-dev libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql

RUN sed -i 's/^pm.max_children.*/pm.max_children = 40/' /usr/local/etc/php-fpm.d/www.conf

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"