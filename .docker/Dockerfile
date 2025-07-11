ARG PCOV_VERSION=1.0.12
ARG XDEBUG_VERSION=3.4.4
ARG NGINX_IMAGE_VERSION=1.26.2-alpine
ARG CONTAINER_PROXY=''

FROM php:8.4-fpm-alpine AS php-base

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/var/www/.composer
ENV APCU_VERSION=5.1.23

COPY --from=composer:2.2.7 /usr/bin/composer /usr/bin/composer

RUN apk add --no-cache \
    autoconf g++ gcc libc-dev make pkgconf re2c \
    icu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
        opcache \
        pdo_mysql \
        intl \
        pcntl \
        calendar \
    && pecl install apcu \
    && docker-php-ext-enable apcu pcntl calendar \
    && apk del --no-cache \
        autoconf g++ gcc libc-dev make pkgconf re2c \
    && rm -rf /tmp/*

WORKDIR /var/www/html

COPY .docker/php/ini/www.conf /usr/local/etc/php-fpm.d/www.conf

FROM php-base AS php-dev

ARG PCOV_VERSION
ENV PCOV_VERSION=$PCOV_VERSION
ARG XDEBUG_VERSION
ENV XDEBUG_VERSION=$XDEBUG_VERSION

# ręcznie definiujemy build dependencies (bo $PHPIZE_DEPS nie działa automatycznie w nowym stage)
ENV BUILD_DEPS="autoconf g++ gcc libc-dev make pkgconf re2c"

COPY .docker/php/ini/development.ini /usr/local/etc/php/conf.d/development.ini

ADD https://github.com/fabpot/local-php-security-checker/releases/download/v1.0.0/local-php-security-checker_1.0.0_linux_amd64 /usr/bin/local-php-security-checker

RUN chmod +x /usr/bin/local-php-security-checker \
    && apk add --no-cache --virtual .build-deps libxml2-dev libzip-dev linux-headers $BUILD_DEPS \
    && pecl install xdebug-${XDEBUG_VERSION} \
    && docker-php-ext-enable xdebug \
    && pecl install pcov-${PCOV_VERSION} \
    && docker-php-ext-enable pcov \
    && apk del --no-cache .build-deps \
    && rm -rf /tmp/*

FROM php-base AS php-prod

COPY bin bin
COPY src src
COPY config config
COPY migrations migrations
COPY public public
COPY .env .env
COPY composer.json composer.lock .docker/php/ini/production.ini ./

RUN export APP_ENV=prod && \
    composer install --no-dev --no-progress -anq && \
    composer dump-env prod && \
    mkdir -p var/cache/prod/pools && \
    chmod 777 var/cache/prod/pools -R && \
    mv production.ini /usr/local/etc/php/conf.d/zz-php.ini && \
    sed -e "s/;opcache./opcache./g" -i /usr/local/etc/php/conf.d/zz-php.ini && \
    php -v

RUN chown -R www-data var/

FROM ${CONTAINER_PROXY}nginx:${NGINX_IMAGE_VERSION} AS nginx-generic

COPY .docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf
COPY .docker/nginx/conf.d/healthcheck.conf /etc/nginx/conf.d/healthcheck.conf
COPY .docker/nginx/bin/* /usr/local/bin/

HEALTHCHECK --interval=10s --timeout=1s --retries=3 CMD [ "healthcheck" ]

WORKDIR /var/www/html

FROM nginx-generic AS nginx-local

COPY .docker/nginx/conf.d/local.conf /etc/nginx/conf.d/default.conf

HEALTHCHECK NONE

FROM nginx-generic AS nginx-dev

COPY public /var/www/html/public

ARG APP_VERSION
ARG APP_REVISION
RUN echo "$APP_VERSION-$APP_REVISION" > /var/www/html/.version

FROM nginx-generic AS nginx-prod

COPY public /var/www/html/public

ARG APP_VERSION
ARG APP_REVISION
RUN echo "$APP_VERSION-$APP_REVISION" > /var/www/html/.version
