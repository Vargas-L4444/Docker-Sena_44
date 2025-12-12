# Usa la imagen base que ya estabas utilizando
FROM php:8.2-apache

# Instalar las dependencias de PDO para MySQL
# La herramienta 'docker-php-ext-install' se usa 
# para instalar extensiones de PHP.
RUN docker-php-ext-install pdo_mysql





