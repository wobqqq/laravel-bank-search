ARG IMAGE_TAG=latest

FROM laravel-bank-search/php-fpm:${IMAGE_TAG} AS php-fpm

FROM nginx:1.23.3-alpine

WORKDIR /var/www/html

COPY ./conf.d/* /etc/nginx/conf.d/
COPY ./snippets/* /etc/nginx/snippets/

COPY --from=php-fpm /var/www/html .

EXPOSE 80
