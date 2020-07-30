FROM php:7.4-cli-buster

WORKDIR /app

COPY build/compiler.phar /app/compiler
COPY composer.json /app
COPY composer.lock /app

RUN apt-get update \
    && apt-get install -y libzip-dev nano zip \
    && docker-php-ext-install sockets zip > /dev/null

RUN apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer check-platform-reqs --ansi

RUN rm composer.json
RUN rm composer.lock

RUN chmod +x compiler
