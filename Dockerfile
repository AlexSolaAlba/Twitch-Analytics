FROM php:8.1-cli

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /lumen

COPY docker/lumen /lumen

RUN apt-get update && apt-get install -y unzip

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    mv composer.phar /usr/local/bin/composer && \
    php -r "unlink('composer-setup.php');"

RUN composer install --no-interaction --no-dev

CMD ["php"]
