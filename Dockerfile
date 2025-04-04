FROM php:8.1-apache

# Установка расширений PHP
RUN docker-php-ext-install pdo pdo_mysql

# Копируем конфиг Apache
COPY .docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Включаем mod_rewrite
RUN a2enmod rewrite

# Копируем код
COPY . /var/www/html/

# Устанавливаем права
RUN chown -R www-data:www-data /var/www/html

# Устанавливаем Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
RUN composer install
