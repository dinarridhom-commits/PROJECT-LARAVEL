# Stage 1: Build frontend assets dengan Node
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: Production PHP 8.2 + Apache
FROM php:8.2-apache

# Install dependencies sistem dan ekstensi PHP yang dibutuhkan Laravel & milon/barcode (gd)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Aktifkan mod_rewrite Apache untuk routing Laravel
RUN a2enmod rewrite

# Salin Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Direktori kerja
WORKDIR /var/www/html

# Salin source code project
COPY . .

# Salin hasil build frontend dari Stage 1
COPY --from=frontend /app/public/build ./public/build

# Install dependencies PHP via Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Konfigurasi Apache VirtualHost ke folder /public
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Atur hak akses permission folder storage dan bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Setup script entrypoint
COPY docker/run.sh /usr/local/bin/run.sh
RUN sed -i 's/\r$//' /usr/local/bin/run.sh && chmod +x /usr/local/bin/run.sh

EXPOSE 80

CMD ["/usr/local/bin/run.sh"]
