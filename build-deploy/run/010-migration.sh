#!/usr/bin/env bash

# Migrations
echo "Run migrations..."

php artisan migrate
php artisan db:seed
