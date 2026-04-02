# Deploy Laravel 11 on CyberPanel + OpenLiteSpeed (PHP 8.2)

## 1) Prepare web app path

Recommended:
- Keep Laravel app outside webroot if possible.
- Point document root to `<project>/public`.

If forced to use `public_html`, ensure `index.php` and assets resolve correctly and sensitive files (`.env`, `storage`, `vendor`) are not web-accessible.

## 2) Required PHP extensions

Enable these for PHP 8.2:
- bcmath
- ctype
- curl
- fileinfo
- mbstring
- openssl
- pdo_mysql
- tokenizer
- xml

## 3) One-time setup commands

```bash
cd /home/<USER>/public_html
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan queue:table
php artisan queue:failed-table
php artisan session:table
php artisan migrate --force
php artisan db:seed --force
php artisan optimize
```

## 4) Cron for scheduler

Create cron job in CyberPanel:

```bash
* * * * * cd /home/<USER>/public_html && php artisan schedule:run >> /dev/null 2>&1
```

## 5) Queue worker

Use supervisor or process manager to keep this alive:

```bash
cd /home/<USER>/public_html
php artisan queue:work --queue=default --sleep=3 --tries=3 --max-time=3600
```

## 6) Sanctum notes

In `.env`:

```bash
APP_URL=https://lasotuvi.uk
SANCTUM_STATEFUL_DOMAINS=lasotuvi.uk,www.lasotuvi.uk
SESSION_DOMAIN=.lasotuvi.uk
```

For SPA auth, ensure CORS and cookie/session settings are aligned with HTTPS domain.

## 7) Security hardening checklist

- Set `APP_DEBUG=false` in production.
- Rotate database and API credentials.
- Restrict database user privileges.
- Back up MariaDB daily.
- Ensure HTTPS redirect at web server level.
- Use least-privilege file permissions.
