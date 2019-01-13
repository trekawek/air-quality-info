FROM php:7.2-apache

RUN rm -rf /var/www/html && ln -s /opt/air-quality-info/htdocs /var/www/html

RUN apt-get update && apt-get install -y \
    librrd-dev \
    && pecl install rrd \
    && docker-php-ext-enable rrd \
    && a2enmod rewrite
