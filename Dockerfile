FROM php:7.4-fpm

RUN apt-get update \
    && apt-get install -y -qq --no-install-recommends \
    libzip-dev \
    unzip \
    git \
    zip

WORKDIR /var/www

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --2 --filename=composer
RUN chmod u+x /usr/bin/composer

COPY . .

RUN /usr/bin/composer install --no-interaction --no-scripts --no-plugins

CMD ["php-fpm"]
