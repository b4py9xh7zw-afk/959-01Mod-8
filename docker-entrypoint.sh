#!/bin/sh
set -e

echo "Waiting for database to be ready..."
until php -r "try { \$pdo = new PDO('mysql:host=db;port=3306;dbname=license_platform', 'license_user', 'license_pass'); exit(0); } catch (Exception \$e) { exit(1); }" 2>/dev/null; do
  echo "Database is unavailable - sleeping"
  sleep 1
done

echo "Database is ready!"

# Initialize database and seed users
echo "Initializing database..."
php app/scripts/init_db.php || true

echo "Seeding users..."
php app/scripts/seed_users.php || true

echo "Seeding sample licenses..."
php app/scripts/seed_licenses.php || true

echo "Starting PHP-FPM..."
exec php-fpm
