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

## Deploy (production)

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan optimize
php artisan queue:work --queue=default --sleep=3 --tries=3
```

If using frontend asset build:

```bash
npm install
npm run build
```

## Security note

After first deployment, immediately change/remove seeded admin credentials in `database/seeders/DatabaseSeeder.php`.
