ARG PHP_VERSION=8

FROM composer:2 AS builder

FROM php:$PHP_VERSION AS cli

RUN apt-get update \
    && apt-get install -y git zlib1g-dev libzip-dev \
    && docker-php-ext-install zip
WORKDIR /app
COPY --from=builder /usr/bin/composer /usr/bin/composer
COPY composer.json .
RUN /usr/bin/composer install --no-interaction
COPY . .

RUN mkdir -p /reports
ENTRYPOINT [ "composer", "run" ]
