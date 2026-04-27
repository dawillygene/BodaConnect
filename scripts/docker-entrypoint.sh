#!/usr/bin/env sh
set -eu

APP_ROOT="/var/www/html"

mkdir -p \
    "$APP_ROOT/bootstrap/cache" \
    "$APP_ROOT/database" \
    "$APP_ROOT/storage/framework/cache" \
    "$APP_ROOT/storage/framework/sessions" \
    "$APP_ROOT/storage/framework/testing" \
    "$APP_ROOT/storage/framework/views" \
    "$APP_ROOT/storage/logs"

touch "$APP_ROOT/database/database.sqlite"

if ! chown -R www-data:www-data "$APP_ROOT/bootstrap/cache" "$APP_ROOT/database" "$APP_ROOT/storage"; then
    echo "WARNING: Could not chown bind-mounted paths. Falling back to permissive write permissions." >&2
    chmod -R a+rwX "$APP_ROOT/bootstrap/cache" "$APP_ROOT/database" "$APP_ROOT/storage"
else
    chmod -R ug+rwX "$APP_ROOT/bootstrap/cache" "$APP_ROOT/database" "$APP_ROOT/storage"
fi

exec apache2-foreground
