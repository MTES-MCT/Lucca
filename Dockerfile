# PHP
FROM php:8.2-fpm AS php-fpm

WORKDIR /srv/app

RUN apt-get update && apt-get install -y \
    unzip libpq-dev libonig-dev libzip-dev libicu-dev libpng-dev \
    xvfb libfontconfig wkhtmltopdf \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql zip intl gd \
    && apt-get clean

ENV WKHTMLTOPDF_PATH=/usr/bin/wkhtmltopdf

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

## Copy projet files
COPY . ./

EXPOSE 9000

# Caddy
FROM caddy:2-alpine AS caddy

ENV SERVER_NAME=http://

WORKDIR /srv/app

COPY ./docker/Caddyfile /etc/caddy/Caddyfile
