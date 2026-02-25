#!/bin/bash
set -e

cd /var/www/html

# 🔹 Garantir permissões em diretórios de escrita
chown -R www-data:www-data storage bootstrap/cache

# 🔹 Limpeza de cache para garantir que alterações no .env sejam lidas
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear
php artisan view:clear

echo "🚀 Starting PHP-FPM..."
exec php-fpm
