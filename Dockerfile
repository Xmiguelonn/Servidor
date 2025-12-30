FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    zip unzip git \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql
