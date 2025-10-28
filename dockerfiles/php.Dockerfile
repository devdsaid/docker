FROM php:8.2-fpm-alpine

WORKDIR /var/www/laravel

RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql