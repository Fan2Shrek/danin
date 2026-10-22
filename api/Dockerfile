FROM dunglas/frankenphp:1-php8.4-alpine AS base

ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN install-php-extensions \
    intl \
    sockets \
    redis \
    pdo_mysql

COPY ./.devops/Caddyfile /etc/frankenphp/Caddyfile

COPY . /app
WORKDIR /app


FROM base AS dev

RUN composer install

RUN chmod -R 777 var


FROM base AS test

ENV APP_ENV=test
ENV APP_DEBUG=0

RUN composer install

RUN chmod -R 777 var


FROM base AS prod

ENV APP_ENV=prod
ENV APP_DEBUG=0

# Clean up
RUN rm -rf tests/

RUN composer install --no-dev --optimize-autoloader
RUN chmod -R 777 var
