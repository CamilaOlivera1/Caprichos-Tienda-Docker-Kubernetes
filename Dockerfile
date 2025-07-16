# Imagen base con Apache y PHP 
FROM php:8.2-apache

# Instalar extensiones necesarias de PHP y habilitarlas
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable mysqli pdo_mysql

# Copiar el código del proyecto al contenedor
COPY . /var/www/html/

# Dar permisos correctos
RUN chown -R www-data:www-data /var/www/html/ && chmod -R 755 /var/www/html/

# Activar mod_rewrite de Apache si lo necesitás (opcional)
RUN a2enmod rewrite

# Exponer el puerto por el que Apache escucha
EXPOSE 80
