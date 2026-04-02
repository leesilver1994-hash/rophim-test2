#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")" && pwd)"
BASE_DIR="$ROOT_DIR/.laravel-base"

cd "$ROOT_DIR"

echo "[1/6] Creating clean Laravel 11 base in $BASE_DIR"
rm -rf "$BASE_DIR"
composer create-project laravel/laravel:^11.0 "$BASE_DIR" --no-interaction --prefer-dist

echo "[2/6] Copying missing Laravel framework skeleton files"
rsync -a --ignore-existing \
  --exclude='.git' \
  --exclude='vendor' \
  --exclude='node_modules' \
  "$BASE_DIR"/ "$ROOT_DIR"/

echo "[3/6] Cleaning temporary base"
rm -rf "$BASE_DIR"

echo "[4/6] Installing PHP dependencies"
composer install --no-interaction --prefer-dist

echo "[5/6] Installing JS dependencies"
if command -v npm >/dev/null 2>&1; then
  npm install
else
  echo "npm not found, skipping npm install"
fi

echo "[6/6] Ready. Next steps:"
echo "  cp .env.example .env"
echo "  php artisan key:generate"
echo "  php artisan migrate --force"
echo "  php artisan db:seed --force"
echo "  npm run build"
