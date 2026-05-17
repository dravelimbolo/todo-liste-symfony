FROM php:8.4-fpm-alpine

RUN apk add --no-cache git curl libpng-dev libjpeg-turbo-dev zip unzip icu-dev oniguruma-dev

RUN docker-php-ext-install pdo_mysql intl mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
EXPOSE 9000