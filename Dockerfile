FROM php:8.2-apache

ENV TZ=Europe/Warsaw
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN docker-php-ext-install mysqli
RUN a2enmod rewrite

COPY . /var/www/html/

# Ustaw podstawowe uprawnienia
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html

# Nadaj rozszerzone uprawnienia tylko jeśli katalog public/ istnieje
RUN if [ -d /var/www/html/public ]; then \
        chmod -R 775 /var/www/html/public; \
    fi
