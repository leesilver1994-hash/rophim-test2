#!/usr/bin/env bash
set -euo pipefail

APP_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$APP_DIR"

echo "[1/7] Installing dependencies"
composer install --no-dev --optimize-autoloader

echo "[2/7] Preparing environment"
if [[ ! -f .env ]]; then
  cp .env.example .env
  echo "Created .env from .env.example; please update secrets."
fi

echo "[3/7] App key"
php artisan key:generate --force

echo "[4/7] Storage symlink"
php artisan storage:link || true

echo "[5/7] Database migrations"
php artisan migrate --force

echo "[6/7] Optional seed"
php artisan db:seed --force || true

echo "[7/7] Optimizing"
php artisan optimize

echo "Done. Start queue worker with: php artisan queue:work --queue=default --sleep=3 --tries=3"
