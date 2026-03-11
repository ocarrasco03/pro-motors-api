FROM php:8.5-fpm-alpine AS base

WORKDIR /var/www/html

RUN apk add --no-cache \
    nginx \
    supervisor \
    shadow \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        zip \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        pdo_sqlite \
        bcmath \
        intl \
        gd \
        opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/cache/apk/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini

RUN mkdir -p /var/www/html/storage/logs \
    /var/www/html/storage/framework/{sessions,views,cache} \
    /run/nginx \
    /var/log/supervisor

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

FROM base AS producer

RUN composer config --global --no-interaction "preferred-dist" "zip"

COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-interaction --prefer-dist --classmap-authoritative

FROM base AS development

RUN composer config --global --no-interaction "preferred-dist" "zip"

COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-interaction --prefer-dist --classmap-authoritative

COPY . .

RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    || true

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
