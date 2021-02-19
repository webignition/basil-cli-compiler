FROM php:8-cli-buster

WORKDIR /app

ARG proxy_server_version=0.5

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY bin/compiler /app/bin/compiler
COPY src /app/src
COPY composer.json composer.lock /app/

RUN apt-get update \
    && apt-get install -y libzip-dev nano zip \
    && docker-php-ext-install pcntl zip > /dev/null \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && chmod +x /app/bin/compiler \
    && ln -s /app/bin/compiler /app/compiler \
    && composer check-platform-reqs --ansi \
    && composer install --prefer-dist --no-dev \
    && rm composer.json \
    && rm composer.lock \
    && curl https://raw.githubusercontent.com/webignition/docker-tcp-cli-proxy/${proxy_server_version}/composer.json --output composer.json \
    && curl https://raw.githubusercontent.com/webignition/docker-tcp-cli-proxy/${proxy_server_version}/composer.lock --output composer.lock \
    && composer check-platform-reqs --ansi \
    && rm composer.json \
    && rm composer.lock \
    && rm /usr/bin/composer \
    && curl -L https://github.com/webignition/docker-tcp-cli-proxy/releases/download/${proxy_server_version}/server.phar --output ./server \
    && chmod +x ./server

CMD ./server
