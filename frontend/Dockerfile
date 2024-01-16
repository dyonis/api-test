FROM php:8.2-fpm

WORKDIR /var/www

RUN apt update && apt install -y \
    git \
    zip \
    zlib1g-dev \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-install \
    intl \
    opcache \
    pdo \
    pdo_mysql \
    && docker-php-ext-configure pcntl --enable-pcntl \
      && docker-php-ext-install pcntl \
    && docker-php-ext-configure zip \
      && docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash -
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

RUN pecl install xdebug && docker-php-ext-enable xdebug
ENV PHP_IDE_CONFIG="serverName=dockerHost"

CMD ["php-fpm"]
