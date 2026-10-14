#!/bin/sh
set -e

# Port handling for cloud providers (Railway, Render, Fly)
PORT=${PORT:-80}
echo "Configuring Apache for PORT: $PORT..."
echo "Listen $PORT" > /etc/apache2/ports.conf
grep -q "ServerName localhost" /etc/apache2/apache2.conf 2>/dev/null || echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Generate APP_KEY if missing
if [ -z "$APP_KEY" ]; then
    echo "Warning: APP_KEY not set. Generating key..."
    php artisan key:generate --force
fi

# Ensure storage, cache, and database folders exist
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache \
         /var/www/html/database

# Create SQLite database file if DB_CONNECTION is sqlite
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    if [ ! -f "/var/www/html/database/database.sqlite" ]; then
        touch /var/www/html/database/database.sqlite
    fi
fi

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Run database migrations automatically so tables (sessions, users, etc.) always exist
echo "Running database migrations..."
php artisan migrate --force || echo "Migrations notice: Database not ready or already up to date."

if [ "$RUN_SEED" = "true" ]; then
    echo "Running database seeders..."
    php artisan db:seed --force || echo "Seeder notice: Already seeded or skipped."
fi

# Optimization caches
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Starting Apache on port $PORT..."
exec apache2-foreground
