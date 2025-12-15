FROM php:8.2-apache

ENV TZ=Europe/Warsaw
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Wymagane rozszerzenia
RUN docker-php-ext-install mysqli

# Włącz mod_rewrite (ważne – Twój router opiera się na index.php)
RUN a2enmod rewrite

# Skopiuj całą aplikację
COPY . /var/www/html/

# Upewnij się, że sesje i ewentualne uploady będą działać
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html
