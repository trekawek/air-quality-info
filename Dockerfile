FROM php:7.2-apache

RUN apt-get update && apt-get install -y \
    librrd-dev \
    && pecl install rrd \
    && docker-php-ext-enable rrd
