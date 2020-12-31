FROM php:7.4-fpm

RUN apt-get update && apt-get install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

RUN apt-get update
RUN apt-get install -y nginx

WORKDIR /var/www/xmas-project

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony



RUN groupadd -g 1000 www && \
    useradd -u 1000 -g 1000 www

RUN rm -rf /etc/nginx/conf.d/*

RUN chgrp -R www /etc/nginx

COPY config/default.conf /etc/nginx/conf.d/default.conf

RUN mkdir /run/php-fpm

COPY docker-entrypoint.sh /docker-entrypoint.sh

CMD ["/docker-entrypoint.sh"]
