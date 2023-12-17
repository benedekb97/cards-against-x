FROM php:8.2-apache

RUN apt-get update

RUN apt-get install -y wget zip libcurl4-openssl-dev libpq-dev libzip-dev libjpeg62-turbo-dev libpng-dev libfreetype6-dev libonig-dev libapache2-mod-xsendfile jpegoptim optipng pngquant webp npm gifsicle pkg-config imagemagick libmemcached-dev
RUN apt-get update && apt-get install -y libmagickwand-dev --no-install-recommends && rm -rf /var/lib/apt/lists/*

COPY config/000-default.conf /etc/apache2/sites-available

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN pecl install redis
RUN pecl install memcached

RUN docker-php-ext-install pdo_mysql mbstring zip exif intl soap xml gd curl mysqli calendar

RUN a2enmod rewrite