# Use a base PHP-FPM image
FROM php:8.3-fpm-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    build-base \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    postgresql-dev \
    sqlite-dev \
    git \
    unzip \
    zip \
    curl \
    nodejs \
    npm \
    && docker-php-ext-install \
    pdo_sqlite \
    pdo_mysql \
    pdo_pgsql \
    gd \
    zip \
    intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Clean up previous vendor and composer.lock
RUN rm -rf vendor composer.lock

# Copy application files
COPY . .

# Install Composer dependencies
RUN composer install --no-scripts --optimize-autoloader

# Set permissions for Laravel storage and bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache \
    && chmod -R 775 database

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]

