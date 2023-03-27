FROM php:7.4-fpm

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN apt-get update
RUN apt-get install -y git zip zlib1g-dev libzip-dev zip && docker-php-ext-install zip pdo pdo_mysql

RUN pecl install xdebug-3.1.2 && docker-php-ext-enable xdebug

RUN \
    echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.log=\"/app/var/log/xdebug.log\"" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini;


RUN set -eux; \
    addgroup --gid 1000 --system user; \
    adduser --uid 1000 --gid 1000 --disabled-login --system --shell /bin/bash user; \
    adduser user www-data && \
    mkdir -p /app/var/log && \
    chown -R user:www-data /app/var/log && \
    chmod -R 775 /app/var/log

USER user
