FROM php:8.2-apache

# Установка нужных расширений PHP
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libonig-dev libzip-dev unzip git curl \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

# Копируем конфиг Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Копируем проект (если нужно — можно опустить, т.к. volume монтирует)
# COPY ../../ /var/www/html/
