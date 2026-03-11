#!/bin/bash
set -e

chmod -R 777 /app/public/avatars /app/public/fiches /app/public/patients
chmod -R 775 /app/storage /app/bootstrap/cache

(php-fpm -y /assets/php-fpm.conf &)
nginx -c /assets/nginx.conf
