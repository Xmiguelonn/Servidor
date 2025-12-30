# Imagen con Apache (necesaria para .htaccess)
FROM php:8.4-apache

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    zip unzip git \
    && rm -rf /var/lib/apt/lists/*

# Instalar PDO MySQL
RUN docker-php-ext-install pdo_mysql

# Activar mod_rewrite (OBLIGATORIO para .htaccess)
RUN a2enmod rewrite

# Configurar Apache para permitir .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Copiar tu proyecto
COPY . /var/www/html

# Permisos correctos
RUN chown -R www-data:www-data /var/www/html

# Exponer puerto 80
EXPOSE 80

# Comando de arranque (evita conflictos con Railway)
CMD ["apache2-foreground"]
