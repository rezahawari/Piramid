#!/bin/sh
set -e

echo "========================================================"
echo " 🚀 Starting Laravel Application (Apache Web Server)    "
echo " 🌐 Internal Container Port : 80                        "
echo " 🔗 External Access URL     : ${APP_URL:-http://localhost:4001}"
echo " 🗄️ Database Host          : ${DB_HOST:-pgpiramid}"
echo " 📦 Database Name          : ${DB_DATABASE:-qurban_pyramid}"
echo "========================================================"

# Jalankan migrasi database otomatis & seeder saat deploy
echo "🔄 Running database migrations and seeders..."
php artisan migrate --force || echo "⚠️ Migration failed or database not ready yet."
php artisan db:seed --force || echo "⚠️ Seeding failed or already seeded."

# Jalankan Apache foreground
exec apache2-foreground "$@"

