# Dockerfile untuk deploy Laravel ke Render.com

FROM php:8.3-cli

# Install dependency sistem yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy seluruh project
COPY . .

# Install dependency PHP (tanpa paket dev supaya lebih ringan & cepat)
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Pastikan folder storage & cache bisa ditulis
RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Render otomatis kasih env variable PORT, aplikasi harus dengar di situ
EXPOSE 10000

# Jalankan migration lalu start server saat container dinyalakan
CMD php artisan config:clear \
    && php artisan migrate --force \
    && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
