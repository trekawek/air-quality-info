FROM php:7.2-apache

RUN rm -rf /var/www/html && ln -s /opt/air-quality-info/htdocs /var/www/html

RUN apt-get update && apt-get install -y \
    librrd-dev mysql-client libzip-dev \
    && pecl install rrd \
    && docker-php-ext-install mysqli zip \
    && docker-php-ext-enable rrd mysqli zip \
    && a2enmod rewrite