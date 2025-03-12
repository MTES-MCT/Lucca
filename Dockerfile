# PHP
FROM php:8.2-fpm AS php-fpm

# Workdir during installation
WORKDIR /tmp

RUN apt-get update && apt-get install -y \
    libzip-dev libicu-dev libpng-dev \
    # Install minimal dependencies for wkhtmltopdf
    libxrender1 \
    libxext6 \
	libfontconfig1 \
	libx11-6 \
	fontconfig \
	fonts-crosextra-carlito \
	xfonts-100dpi \
	xfonts-75dpi \
	xfonts-base \
	libjpeg62-turbo \
	libpng16-16 \
	# Configure PHP extensions
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql zip intl gd \
    # Clean up
    && apt-get clean -y \
	apt-get autoclean -y \
	apt-get autoremove -y

RUN curl -L https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6.1-3/wkhtmltox_0.12.6.1-3.bookworm_amd64.deb \
		-o wkhtmltox_0.12.6.1-3.bookworm_amd64.deb; \
        dpkg -i wkhtmltox_0.12.6.1-3.bookworm_amd64.deb;

# Workdir after installation
WORKDIR /srv/app

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
