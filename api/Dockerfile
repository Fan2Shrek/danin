FROM php:8.4-fpm-alpine AS base


FROM base AS main

ENV COMPOSER_ALLOW_SUPERUSER 1
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
    intl \
    sockets \
    redis \
    pdo_mysql

RUN apk update && apk upgrade

RUN apk add --no-cache nginx multirun

COPY ./.devops/default.conf /etc/nginx/http.d/default.conf

COPY . /app
WORKDIR /app

CMD ["multirun", "php-fpm -R -F", "nginx -g 'daemon off;'"]


FROM main AS dev

RUN composer install

RUN chmod -R 777 var

FROM main AS prod
