#!/bin/bash
set -e

echo "Menunggu koneksi database MariaDB siap..."
until php -r "try { new PDO('mysql:host='.getenv('DB_HOST').';port='.getenv('DB_PORT').';dbname='.getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); echo 'OK'; } catch (Exception \$e) { exit(1); }" > /dev/null 2>&1; do
    echo "MariaDB belum siap, mencoba lagi dalam 2 detik..."
    sleep 2
done

echo "Koneksi database MariaDB SIAP!"
echo "Menjalankan migrasi dan seeder data 467 CSV..."
php artisan migrate:fresh --seed --force

echo "Memulai Web Server SIAKERS..."
exec php artisan serve --host=0.0.0.0 --port=8000
