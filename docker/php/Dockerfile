# docker/php/Dockerfile
FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_mysql mbstring xml ctype bcmath fileinfo

WORKDIR /var/www