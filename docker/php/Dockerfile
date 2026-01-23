FROM php:8.4-fpm-alpine

ARG UID=1000
ARG GID=1000

RUN apk add --no-cache \
        bash \
        git \
        icu-dev \
        libzip-dev \
        oniguruma-dev \
        zlib-dev \
    && docker-php-ext-install -j$(nproc) intl opcache zip \
    && addgroup -g "${GID}" app \
    && adduser -D -G app -u "${UID}" app \
    && sed -i 's/user = www-data/user = app/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/group = www-data/group = app/' /usr/local/etc/php-fpm.d/www.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
