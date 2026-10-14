#!/bin/sh
set -e

# Port handling for cloud providers (Render, Railway, Fly)
PORT=${PORT:-80}
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf 2>/dev/null || true
sed -i "s/*:80/*:$PORT/g" /etc/apache2/sites-available/*.conf 2>/dev/null || true

# Generate APP_KEY if missing
if [ -z "$APP_KEY" ]; then
    echo "Warning: APP_KEY not set. Generating key..."
    php artisan key:generate --force
fi

# Create SQLite database file if DB_CONNECTION is sqlite
if [ "$DB_CONNECTION" = "sqlite" ]; then
    if [ ! -f "database/database.sqlite" ]; then
        touch database/database.sqlite
        chown www-data:www-data database/database.sqlite
    fi
fi

# Run migrations if RUN_MIGRATIONS is set to true
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force
    if [ "$RUN_SEED" = "true" ]; then
        echo "Running database seeders..."
        php artisan db:seed --force
    fi
fi

# Cache configuration, routes, and views for production performance
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Starting Apache on port $PORT..."
exec apache2-foreground
