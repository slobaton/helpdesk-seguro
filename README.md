# Helpdesk Secure

> Plataforma Helpdesk en Laravel con control de roles/permisos (Spatie) y auditoría (Activitylog).

## Requisitos

- **PHP** ≥ 8.2 con `openssl`, `mbstring`, `pdo`, `curl`, `json`, `zip`
- **Composer** ≥ 2.x
- **Node.js** ≥ 18 y **npm**
- **Git**
- **Base de datos**: SQLite (simple) o MySQL/PostgreSQL
- **Extensiones** para desarrollo front: `vite` (incluido vía npm scripts)

## Instalación rápida

```bash
git clone git@github.com:slobaton/helpdesk-seguro.git helpdesk-secure
cd helpdesk-secure

composer install
cp .env.example .env
php artisan key:generate

# Publicacion de migraciones de spatie (roles/permisos) y activitylog
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="permission-migrations"
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider"

# Opción rápida Usar SQLite
touch database/database.sqlite
# Edita .env:
# DB_CONNECTION=sqlite
# DB_DATABASE=database/database.sqlite

php artisan migrate --seed

npm install
npm run build

php artisan serve
```
