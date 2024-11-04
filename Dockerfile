# syntax=docker/dockerfile:1
FROM php:8.3.12-apache

COPY . /var/www/html

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install -j$(nproc) mysqli pdo pdo_mysql

RUN a2enmod rewrite
RUN a2enmod headers

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

USER www-data
