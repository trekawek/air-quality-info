FROM php:8.4-cli-alpine

RUN apk update && \
    apk add bash npm libzip-dev make && \
    docker-php-ext-install zip && \
    curl -s https://getcomposer.org/installer | php  && \
    mv composer.phar /usr/local/bin/composer

WORKDIR /work
