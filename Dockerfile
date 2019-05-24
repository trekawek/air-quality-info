FROM php:7.2-apache

RUN apt-get update && apt-get install -y \
    mysql-client libzip-dev \
    && docker-php-ext-install mysqli zip \
    && docker-php-ext-enable mysqli zip \
    && a2enmod rewrite

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY src /opt/air-quality-info
RUN rm -rf /var/www/html && ln -s /opt/air-quality-info/htdocs /var/www/html
