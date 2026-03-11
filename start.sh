#!/bin/bash

# Créer les répertoires nécessaires s'ils n'existent pas
mkdir -p /app/storage/framework/cache/data
mkdir -p /app/storage/framework/sessions
mkdir -p /app/storage/framework/views
mkdir -p /app/storage/logs
mkdir -p /app/public/avatars
mkdir -p /app/public/fiches
mkdir -p /app/public/patients

# Fixer les permissions
chmod -R 775 /app/storage
chmod -R 775 /app/bootstrap/cache
chmod -R 777 /app/public/avatars
chmod -R 777 /app/public/fiches
chmod -R 777 /app/public/patients

# Lancer le serveur (commande nixpacks originale)
node /assets/scripts/prestart.mjs /app/nginx.template.conf /nginx.conf && (php-fpm -y /assets/php-fpm.conf & nginx -c /nginx.conf)
