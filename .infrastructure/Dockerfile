FROM composer:2 AS cli

COPY composer.json ./

RUN composer install --dev --no-interaction

COPY . .

RUN mkdir -p /reports
ENTRYPOINT [ "composer", "run" ]
