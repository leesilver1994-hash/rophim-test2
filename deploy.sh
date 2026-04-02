#!/usr/bin/env bash
set -euo pipefail

export COMPOSER_ALLOW_SUPERUSER=1

APP_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$APP_DIR"

echo "[1/8] Installing dependencies (without scripts)"
composer install --no-dev --optimize-autoloader --no-scripts

echo "[2/8] Running Laravel package discovery manually"
php artisan package:discover --ansi

echo "[3/8] Preparing environment"
if [[ ! -f .env ]]; then
  cp .env.example .env
  echo "Created .env from .env.example; please update secrets."
fi

echo "[4/8] App key"
php artisan key:generate --force

echo "[5/8] Storage symlink"
php artisan storage:link || true

echo "[6/8] Database migrations"
php artisan migrate --force

echo "[7/8] Optional seed"
php artisan db:seed --force || true

echo "[8/8] Optimizing"
php artisan optimize

echo "Done. Start queue worker with: php artisan queue:work --queue=default --sleep=3 --tries=3"
