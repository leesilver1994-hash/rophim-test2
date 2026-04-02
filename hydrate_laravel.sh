#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")" && pwd)"
BASE_DIR="$ROOT_DIR/.laravel-base"

cd "$ROOT_DIR"

echo "[1/8] Creating clean Laravel 11 base in $BASE_DIR"
rm -rf "$BASE_DIR"
composer create-project laravel/laravel:^11.0 "$BASE_DIR" --no-interaction --prefer-dist

echo "[2/8] Copying missing Laravel framework skeleton files"
rsync -a --ignore-existing \
  --exclude='.git' \
  --exclude='vendor' \
  --exclude='node_modules' \
  --exclude='composer.lock' \
  --exclude='package-lock.json' \
  "$BASE_DIR"/ "$ROOT_DIR"/

echo "[3/8] Removing local/dev-only providers that may break --no-dev installs"
if [[ -f "$ROOT_DIR/bootstrap/providers.php" ]]; then
  sed -i '/Laravel\\Pail\\PailServiceProvider::class/d' "$ROOT_DIR/bootstrap/providers.php"
  sed -i '/Laravel\\Telescope\\TelescopeServiceProvider::class/d' "$ROOT_DIR/bootstrap/providers.php"
fi
if [[ -f "$ROOT_DIR/config/app.php" ]]; then
  sed -i '/Laravel\\Pail\\PailServiceProvider::class/d' "$ROOT_DIR/config/app.php"
  sed -i '/Laravel\\Telescope\\TelescopeServiceProvider::class/d' "$ROOT_DIR/config/app.php"
fi

echo "[4/8] Ensuring lock files are generated from THIS project's composer.json/package.json"
rm -f "$ROOT_DIR/composer.lock" "$ROOT_DIR/package-lock.json"

echo "[5/8] Cleaning temporary base"
rm -rf "$BASE_DIR"

echo "[6/8] Installing PHP dependencies"
composer install --no-interaction --prefer-dist

echo "[7/8] Installing JS dependencies"
if command -v npm >/dev/null 2>&1; then
  npm install
else
  echo "npm not found, skipping npm install"
fi

echo "[8/8] Ready. Next steps:"
echo "  cp .env.example .env"
echo "  php artisan key:generate"
echo "  php artisan migrate --force"
echo "  php artisan db:seed --force"
echo "  npm run build"
