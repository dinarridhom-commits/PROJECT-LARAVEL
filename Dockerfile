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

# Nonaktifkan modul MPM ganda dan aktifkan mpm_prefork + rewrite
RUN a2dismod -f mpm_event mpm_worker 2>/dev/null || true \
    && a2enmod mpm_prefork rewrite

# Konfigurasi batas upload PHP untuk file GIS / GeoJSON besar
RUN echo "upload_max_filesize = 500M\npost_max_size = 500M\nmemory_limit = 512M\nmax_execution_time = 300\nmax_input_time = 300" > /usr/local/etc/php/conf.d/uploads.ini

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

# Atur hak akses permission folder storage, bootstrap/cache, database, dan public/geojson
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache database public/geojson \
    && chown -R www-data:www-data storage bootstrap/cache database public/geojson \
    && chmod -R 775 storage bootstrap/cache database \
    && chmod -R 777 public/geojson

# Setup script entrypoint
COPY docker/run.sh /usr/local/bin/run.sh
RUN sed -i 's/\r$//' /usr/local/bin/run.sh && chmod +x /usr/local/bin/run.sh

EXPOSE 80

CMD ["/usr/local/bin/run.sh"]
