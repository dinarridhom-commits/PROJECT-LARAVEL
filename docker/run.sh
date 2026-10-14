#!/bin/sh
set -e

# Port handling for cloud providers (Railway, Render, Fly)
PORT=${PORT:-80}
echo "Configuring Apache for PORT: $PORT..."
echo "Listen $PORT" > /etc/apache2/ports.conf
grep -q "ServerName localhost" /etc/apache2/apache2.conf 2>/dev/null || echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Ensure only one MPM module is active
a2dismod -f mpm_event mpm_worker 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

# Ensure APP_KEY has a value (with reliable fallback)
export APP_KEY="${APP_KEY:-base64:QybaPyZsjwoS8Xb33ZtE3md0QLwdtwkyX0Qm8bFow5s=}"

# Ensure .env exists in container for artisan commands
if [ ! -f "/var/www/html/.env" ]; then
    if [ -f "/var/www/html/.env.example" ]; then
        cp /var/www/html/.env.example /var/www/html/.env
    else
        touch /var/www/html/.env
    fi
fi

# Ensure APP_KEY is written in .env
if grep -q "^APP_KEY=" /var/www/html/.env 2>/dev/null; then
    sed -i "s|^APP_KEY=.*|APP_KEY=$APP_KEY|" /var/www/html/.env
else
    echo "APP_KEY=$APP_KEY" >> /var/www/html/.env
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

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database /var/www/html/.env
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod 664 /var/www/html/.env

# Run database migrations automatically so tables (sessions, users, etc.) always exist
echo "Running database migrations..."
php artisan migrate --force || echo "Migrations notice: Database not ready or already up to date."

if [ "$RUN_SEED" = "true" ]; then
    echo "Running database seeders..."
    php artisan db:seed --force || echo "Seeder notice: Already seeded or skipped."
fi

# Clear and re-cache configuration with active APP_KEY
php artisan config:clear || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Starting Apache on port $PORT..."
exec apache2-foreground
