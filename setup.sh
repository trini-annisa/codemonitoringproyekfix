#!/bin/bash

echo "=== SETUP EVA MONITORING ==="

# BUAT FOLDER WAJIB
mkdir -p app/Http/Controllers/Admin
mkdir -p app/Http/Controllers/PM

echo "✔ Folder controller siap"

# INSTALL AUTH (WAJIB)
echo "Install auth..."
composer require laravel/ui
php artisan ui bootstrap --auth

# INSTALL NODE
npm install
npm run dev

echo "✔ Auth siap"

# MIGRATE & SEED
php artisan migrate:fresh --seed

echo "✔ Database siap"

echo "=== SELESAI ==="

