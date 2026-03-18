FROM php:8.4-fpm-alpine AS base

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

COPY . /var/www/html

RUN composer install --no-dev --no-interaction --prefer-dist --classmap-authoritative || true

RUN mkdir -p /var/www/html/storage/{logs,framework/{sessions,views,cache},app/public} /var/www/html/bootstrap/cache && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

CMD ["sh", "-c", "sleep 5 && php artisan schedule:work"]
