#!/usr/bin/env bash
set -euo pipefail

APP_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$APP_DIR"

exec php artisan queue:work --queue=default --sleep=3 --tries=3 --max-time=3600
