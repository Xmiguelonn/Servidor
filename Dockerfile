FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    zip unzip git \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /app
WORKDIR /app
