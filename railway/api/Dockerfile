FROM php:8.4-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    nginx \
    supervisor \
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
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        zip pdo pdo_pgsql pdo_sqlite bcmath intl gd opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/cache/apk/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.railway.conf /etc/supervisor/conf.d/supervisord.conf

RUN mkdir -p /var/www/html/storage/{logs,framework/{sessions,views,cache}} /run/nginx /var/log/supervisor /var/www/html/bootstrap/cache

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache || true

RUN composer install --no-dev --no-interaction --prefer-dist --classmap-authoritative || true

EXPOSE 8080
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
