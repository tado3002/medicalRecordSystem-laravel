# Gunakan base image PHP + dependencies Laravel
FROM php:8.2-fpm

# Install dependencies sistem
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    libzip-dev zip libpq-dev libjpeg-dev libfreetype6-dev libmcrypt-dev \
    libssl-dev && \
    docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Salin semua file ke dalam container
COPY . .

# Install dependency Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Beri izin folder storage dan bootstrap
RUN chown -R www-data:www-data storage bootstrap/cache

# Jalankan php-fpm
CMD ["php-fpm"]
