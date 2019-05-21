FROM php:7.2-apache

RUN rm -rf /var/www/html && ln -s /opt/air-quality-info/htdocs /var/www/html

RUN apt-get update && apt-get install -y \
    mysql-client libzip-dev \
    && docker-php-ext-install mysqli zip \
    && docker-php-ext-enable mysqli zip \
    && a2enmod rewrite