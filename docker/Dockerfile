FROM php:7.4-cli-buster

WORKDIR /app

COPY bin /app/bin
COPY src /app/src
COPY composer.json /app
COPY composer.lock /app

RUN apt-get update \
    && apt-get install -y libzip-dev nano zip \
    && docker-php-ext-install zip > /dev/null

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --no-suggest --no-progress --prefer-dist

RUN apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
