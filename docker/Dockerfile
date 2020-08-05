FROM php:7.4-cli-buster

WORKDIR /app

RUN apt-get update \
    && apt-get install -y libzip-dev nano zip \
    && docker-php-ext-install pcntl sockets zip > /dev/null

RUN apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN echo "Checking compiler platform requirements"
COPY composer.json /app
COPY composer.lock /app
RUN composer check-platform-reqs --ansi
RUN rm composer.json
RUN rm composer.lock

RUN echo "Checking proxy server platform requirements"
RUN curl https://raw.githubusercontent.com/webignition/docker-tcp-cli-proxy/master/composer.json --output composer.json
RUN curl https://raw.githubusercontent.com/webignition/docker-tcp-cli-proxy/master/composer.lock --output composer.lock
RUN composer check-platform-reqs --ansi
RUN rm composer.json
RUN rm composer.lock

RUN echo "Fetching proxy server"
RUN curl -L https://github.com/webignition/docker-tcp-cli-proxy/releases/latest/download/server.phar --output ./server
RUN chmod +x ./server

COPY build/compiler.phar /app/compiler
RUN chmod +x compiler

CMD ./server
