#!/bin/bash

# Salir inmediatamente si ocurre un error
set -e

echo "Running Laravel migrations..."
php artisan migrate --force

echo "Running specific DatabaseSeeder..."
php artisan db:seed --class=DatabaseSeeder --force

echo "Linking storage directory..."
php artisan storage:link

echo "Clearing and caching configurations..."
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache

# Verifica si Node.js está instalado
if command -v node &> /dev/null
then
    echo "Node.js detected. Installing dependencies and building assets..."

    # Instalar dependencias de JavaScript
    npm install

    # Compilar assets (producción)
    npm run build
else
    echo "Node.js not detected. Skipping asset compilation."
fi

echo "Deployment tasks completed!"
