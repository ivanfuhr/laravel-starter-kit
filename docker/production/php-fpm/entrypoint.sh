#!/bin/sh
set -e

if [ ! "$(ls -A /var/www/storage 2>/dev/null)" ]; then
  cp -R /var/www/storage-init/. /var/www/storage
fi

rm -rf /var/www/storage-init
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# php-fpm master must start as root to drop privileges for the pool
if [ "$1" = "php-fpm" ]; then
  exec "$@"
fi

exec gosu www-data "$@"
