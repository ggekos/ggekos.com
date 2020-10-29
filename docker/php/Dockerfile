FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip

# Install COMPOSER
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer --version

WORKDIR /app

COPY conf/php-fpm.conf /etc/php-fpm.conf
COPY conf/php.ini /usr/local/etc/php/conf.d/100-php.ini