FROM php:8.3-fpm
RUN apt-get update && apt-get install -y git unzip libzip-dev libicu-dev libonig-dev libxml2-dev
RUN docker-php-ext-install pdo pdo_mysql intl opcache
WORKDIR /var/www/html
COPY . .
RUN php -r "copy('https://getcomposer.org/installer','composer-setup.php');" && php composer-setup.php --install-dir=/usr/local/bin --filename=composer && php -r "unlink('composer-setup.php');"
RUN composer install --optimize-autoloader
RUN php artisan optimize