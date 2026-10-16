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

# Configure PHP upload and memory limits for large GIS / GeoJSON uploads
mkdir -p /usr/local/etc/php/conf.d 2>/dev/null || true
echo "upload_max_filesize = 500M" > /usr/local/etc/php/conf.d/uploads.ini
echo "post_max_size = 500M" >> /usr/local/etc/php/conf.d/uploads.ini
echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/uploads.ini
echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini
echo "max_input_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini

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

# Set dynamic APP_URL with HTTPS for Railway/Cloud
if [ -n "$RAILWAY_PUBLIC_DOMAIN" ]; then
    export APP_URL="https://$RAILWAY_PUBLIC_DOMAIN"
elif [ -n "$RAILWAY_STATIC_URL" ]; then
    export APP_URL="https://$RAILWAY_STATIC_URL"
fi

if [ -n "$APP_URL" ]; then
    if grep -q "^APP_URL=" /var/www/html/.env 2>/dev/null; then
        sed -i "s|^APP_URL=.*|APP_URL=$APP_URL|" /var/www/html/.env
    else
        echo "APP_URL=$APP_URL" >> /var/www/html/.env
    fi
fi

# Gunakan FILE session driver - reliable di cloud container
# File session disimpan di storage/framework/sessions (sudah dibuat di bawah)
# Cookie driver punya batas 4KB yang sering menyebabkan 419 error
if grep -q "^SESSION_DRIVER=" /var/www/html/.env 2>/dev/null; then
    sed -i "s|^SESSION_DRIVER=.*|SESSION_DRIVER=file|" /var/www/html/.env
else
    echo "SESSION_DRIVER=file" >> /var/www/html/.env
fi

# SESSION_SECURE_COOKIE=false - config/session.php sudah hardcode secure=>false
# Railway proxy handle HTTPS, internal connection HTTP, jadi secure harus false
if grep -q "^SESSION_SECURE_COOKIE=" /var/www/html/.env 2>/dev/null; then
    sed -i "s|^SESSION_SECURE_COOKIE=.*|SESSION_SECURE_COOKIE=false|" /var/www/html/.env
else
    echo "SESSION_SECURE_COOKIE=false" >> /var/www/html/.env
fi

# Set APP_ENV=production
if grep -q "^APP_ENV=" /var/www/html/.env 2>/dev/null; then
    sed -i "s|^APP_ENV=.*|APP_ENV=production|" /var/www/html/.env
else
    echo "APP_ENV=production" >> /var/www/html/.env
fi

# Set APP_DEBUG=false di production
if grep -q "^APP_DEBUG=" /var/www/html/.env 2>/dev/null; then
    sed -i "s|^APP_DEBUG=.*|APP_DEBUG=false|" /var/www/html/.env
else
    echo "APP_DEBUG=false" >> /var/www/html/.env
fi

# Ensure storage, cache, database, and public/geojson folders exist
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache \
         /var/www/html/database \
         /var/www/html/public/geojson

# Create SQLite database file if DB_CONNECTION is sqlite
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    if [ ! -f "/var/www/html/database/database.sqlite" ]; then
        touch /var/www/html/database/database.sqlite
    fi
fi

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database /var/www/html/public/geojson /var/www/html/.env
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 777 /var/www/html/public/geojson
chmod 664 /var/www/html/.env

# Run database migrations automatically so tables (sessions, users, etc.) always exist
echo "Running database migrations..."
php artisan migrate --force || echo "Migrations notice: Database not ready or already up to date."

# Pastikan akun admin/user selalu ada dan password ter-hash dengan benar
echo "Seeding default admin users..."
php artisan db:seed --class=UserSeeder --force || echo "UserSeeder notice: Already seeded or skipped."

if [ "$RUN_SEED" = "true" ]; then
    echo "Running database seeders..."
    php artisan db:seed --force || echo "Seeder notice: Already seeded or skipped."
fi

# PENTING: Gunakan config:clear TANPA config:cache agar env vars dari Railway
# bisa override .env tanpa tertimpa cached config yang stale
echo "Clearing caches..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan cache:clear || true

echo "Starting Apache on port $PORT..."
exec apache2-foreground
