# Use official PHP image with extensions
FROM php:8.3-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    zip unzip curl git libpng-dev libonig-dev libxml2-dev default-mysql-client \
    libzip-dev \
 && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    zip unzip curl git libpng-dev libonig-dev libxml2-dev default-mysql-client \
    libzip-dev libmagickwand-dev --no-install-recommends \
 && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip \
 && pecl install imagick \
 && docker-php-ext-enable imagick \

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory inside container
WORKDIR /var/www/html

# Copy Laravel project files
COPY . .

# Add current path as a safe Git directory
RUN git config --global --add safe.directory /var/www/html

# Install Laravel dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html /var/www/html/storage /var/www/html/bootstrap/cache

# Expose PHP port
EXPOSE 9000

CMD ["php-fpm"]

