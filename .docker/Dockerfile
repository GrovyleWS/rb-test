# escape=\
FROM php:8.2-fpm
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN apt-get update \
    && apt-get install -y \
    libzip-dev \
    libicu-dev
RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_mysql zip intl
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug