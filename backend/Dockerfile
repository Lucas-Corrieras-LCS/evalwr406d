FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first for caching
COPY composer.json composer.lock* ./

# Install dependencies
RUN composer install --no-scripts --no-autoloader 2>/dev/null || true

# Copy rest of application
COPY . .

# Generate autoloader
RUN composer dump-autoload --optimize 2>/dev/null || true

# Set permissions
RUN chown -R www-data:www-data /var/www/html/var 2>/dev/null || true

EXPOSE 9000
CMD ["php-fpm"]
