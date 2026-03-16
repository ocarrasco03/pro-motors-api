FROM php:8.4-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev \
    postgresql-dev \
    sqlite-dev \
    unzip \
    git \
    curl \
    autoconf \
    g++ \
    make \
    && docker-php-ext-install -j$(nproc) \
        zip pdo pdo_pgsql pdo_sqlite bcmath intl gd opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/cache/apk/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini

RUN mkdir -p /var/www/html/storage/{logs,framework/{sessions,views,cache}} /var/www/html/bootstrap/cache

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache || true

RUN composer install --no-dev --no-interaction --prefer-dist --classmap-authoritative || true

CMD ["php", "artisan", "queue:work", "redis", "--sleep=3", "--tries=3", "--max-time=3600"]
