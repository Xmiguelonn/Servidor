# Imagen con Apache (necesaria para .htaccess)
FROM php8.4-apache

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y 
    zip unzip git 
    && rm -rf varlibaptlists

# Instalar PDO MySQL
RUN docker-php-ext-install pdo_mysql

# Activar mod_rewrite (OBLIGATORIO para .htaccess)
RUN a2enmod rewrite

# Configurar Apache para permitir .htaccess
RUN sed -i 'sAllowOverride NoneAllowOverride Allg' etcapache2apache2.conf

# Copiar tu proyecto
COPY . varwwwhtml

# Permisos correctos
RUN chown -R www-datawww-data varwwwhtml

# Exponer puerto 80
EXPOSE 80

# Comando de arranque (evita conflictos con Railway)
CMD [apache2-foreground]
