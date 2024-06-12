FROM composer:2.7 AS composer

FROM php:8.3.6-fpm-alpine3.19

COPY ./conf.d/* /usr/local/etc/php/conf.d/

RUN apk update \
    && apk add --no-cache \
        libpng-dev \
        icu-dev \
        libzip-dev \
        libcurl \
        curl-dev \
        openssl-dev \
        libxml2-dev \
        oniguruma-dev \
    && docker-php-source extract \
    && cp /usr/src/php/ext/openssl/config0.m4 /usr/src/php/ext/openssl/config.m4 \
    && docker-php-ext-install  \
        openssl \
        mbstring \
        gd \
        curl \
        intl \
        pdo_mysql \
        zip \
        simplexml \
        bcmath \
    && rm -rf  \
        /var/lib/apt/lists/* \
        /tmp/*  \
        /var/tmp/* \
        /usr/share/doc/* \
        /var/cache/apk/*

ARG APP_ENV=production

RUN if [ "$APP_ENV" = "local" ] ; then \
    apk add --no-cache linux-headers \
    	&& apk add --update --no-cache --virtual .build-dependencies $PHPIZE_DEPS\
    	&& pecl install xdebug-3.3.1 \
    	&& docker-php-ext-enable xdebug \
    	&& pecl clear-cache \
    	&& apk del .build-dependencies \
        && echo "xdebug.mode=develop,debug,coverage" >> /usr/local/etc/php/conf.d/xdebug.ini \
        && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini \
        && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini \
        && echo "xdebug.discover_client_host=0" >> /usr/local/etc/php/conf.d/xdebug.ini \
;fi

ARG UID=1000
ARG GID=1000

RUN deluser --remove-home www-data \
    && addgroup -g $GID www-data \
    && adduser -S -G www-data -u $UID www-data

COPY --chown=www-data:www-data . .

USER www-data

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY --from=composer /tmp/keys.dev.pub /home/www-data/.composer/keys.dev.pub
COPY --from=composer /tmp/keys.tags.pub /home/www-data/.composer/keys.tags.pub

RUN if [ -f composer.json ] && [ "$APP_ENV" = "production" ]; then \
    composer install --no-ansi --no-dev --no-interaction --no-plugins --no-progress --no-scripts --optimize-autoloader \
;fi

EXPOSE 9000

CMD ["php-fpm"]
