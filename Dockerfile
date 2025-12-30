FROM php:8.5-apache

# Etapa 1: obtener Composer desde la imagen oficial
FROM composer:latest AS composer_stage

# Etapa 2: construir tu contenedor web
FROM php:8.5-apache

RUN apt-get update && apt-get install -y zip unzip

RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-enable pdo_mysql
RUN a2enmod rewrite

# Copiar Composer desde la etapa anterior
COPY --from=composer_stage /usr/bin/composer /usr/bin/composer
