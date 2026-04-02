#!/usr/bin/env bash
set -euo pipefail

export COMPOSER_ALLOW_SUPERUSER=1

APP_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$APP_DIR"

echo "[1/9] Installing dependencies (without scripts)"
composer install --no-dev --optimize-autoloader --no-scripts

echo "[2/9] Running Laravel package discovery manually"
php artisan package:discover --ansi

echo "[3/9] Preparing environment"
if [[ ! -f .env ]]; then
  cp .env.example .env
  echo "Created .env from .env.example; please update secrets."
fi

if ! grep -q '^APP_KEY=' .env; then
  echo 'APP_KEY=' >> .env
fi

echo "[4/9] App key"
php artisan key:generate --force

echo "[5/9] Storage symlink"
php artisan storage:link || true

echo "[6/9] Database migrations"
php artisan migrate --force || {
  echo "Migration failed. If this is a stale SQLite DB, run:"
  echo "  rm -f database/database.sqlite && php artisan migrate --force"
  exit 1
}

echo "[7/9] Optional seed"
php artisan db:seed --force || true

echo "[8/9] Optimizing"
php artisan optimize

echo "[9/9] Frontend build (optional)"
if command -v npm >/dev/null 2>&1; then
  npm run build || true
fi

echo "Done. Start queue worker with: php artisan queue:work --queue=default --sleep=3 --tries=3"
