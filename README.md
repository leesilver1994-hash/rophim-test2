# Lasotuvi Laravel 11 Base

Laravel 11 monolith base for porting Tinix-Bazi with:
- Sanctum authentication
- MariaDB support
- Queue driver: database
- CyberPanel/OpenLiteSpeed deployment notes

## Included

- Laravel runtime skeleton (`artisan`, `public/index.php`, `bootstrap/*`, `config/*`).
- API modules:
  - Auth: register/login/me/logout
  - BaZi: calculate + history
  - Consultation AI: create/list (async via queue job)
  - Articles: public list + admin create
- Validation via FormRequest classes.
- Admin middleware alias `admin`.
- Migrations for auth, jobs, cache, sessions, domain tables.
- Seeder with default admin and sample article.
- Basic web landing page (`/`) and API health test.

## Option A: Hydrate full official Laravel files first (recommended)

Run one command after download:

```bash
COMPOSER_ALLOW_SUPERUSER=1 bash hydrate_laravel.sh
```

This will:
1. Download a clean Laravel 11 skeleton.
2. Copy any missing core Laravel files into this project.
3. Install Composer/NPM dependencies.

Then run:

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan optimize
npm run build
```

## Option B: Direct deploy (if your project already has full Laravel files)

```bash
COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan optimize
php artisan queue:work --queue=default --sleep=3 --tries=3
```

## Security note

After first deployment, immediately change/remove seeded admin credentials in `database/seeders/DatabaseSeeder.php`.
