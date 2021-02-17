FROM php:7.4-cli-buster

WORKDIR /app

ARG proxy_server_version=0.5

RUN apt-get update \
    && apt-get install -y libzip-dev nano zip \
    && docker-php-ext-install pcntl zip > /dev/null

RUN apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Copying compiler
COPY bin/compiler /app/bin/compiler
RUN chmod +x /app/bin/compiler
RUN ln -s /app/bin/compiler /app/compiler

COPY src /app/src

# Checking compiler platform requirements
COPY composer.json /app
COPY composer.lock /app
RUN composer check-platform-reqs --ansi

# Installing dependencies
RUN composer install --no-dev
RUN rm composer.json
RUN rm composer.lock

# Checking proxy server platform requirements
RUN curl https://raw.githubusercontent.com/webignition/docker-tcp-cli-proxy/${proxy_server_version}/composer.json --output composer.json
RUN curl https://raw.githubusercontent.com/webignition/docker-tcp-cli-proxy/${proxy_server_version}/composer.lock --output composer.lock
RUN composer check-platform-reqs --ansi
RUN rm composer.json
RUN rm composer.lock
RUN rm /usr/bin/composer

# Fetching proxy server
RUN curl -L https://github.com/webignition/docker-tcp-cli-proxy/releases/download/${proxy_server_version}/server.phar --output ./server
RUN chmod +x ./server

CMD ./server
