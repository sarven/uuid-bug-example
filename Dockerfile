FROM php:8.3-fpm-alpine

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN apk add postgresql-dev
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install opcache pdo pdo_pgsql

COPY opcache.ini /usr/local/etc/php/conf.d/opcache.ini

USER www-data
