# Tinix-Bazi -> Laravel 11 Porting Plan

## Target stack
- Laravel 11 monolith
- Sanctum authentication
- MariaDB
- Queue driver: database
- Frontend integrated in Laravel deployment

## Module mapping

1. Express routes -> `routes/api.php` + route groups.
2. Controllers -> `app/Http/Controllers/Api/*Controller.php`.
3. Business logic -> `app/Services/*`.
4. Data layer -> Eloquent models + migrations.
5. JWT middleware -> Sanctum tokens and guards.
6. Background tasks -> Jobs + database queue.

## Delivery stages

1. Foundation:
   - Laravel install + baseline env.
   - Sanctum + auth endpoints.
   - core migrations.
2. Feature port:
   - BaZi calculation services.
   - AI consultation integration (OpenRouter).
   - articles/admin and user flows.
3. Production hardening:
   - feature tests.
   - rate limiting and logging.
   - deployment validation in CyberPanel.
