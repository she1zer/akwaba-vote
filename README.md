# AKWABA STIC 25 — Application de vote

Application web de vote pour l'événement **AKWABA STIC 25**, thème Red & Black Mafia Family.

## Stack

- Laravel 12
- Blade + SVG natif + JavaScript vanilla
- Tailwind CSS 4
- SQLite (développement) ou MySQL (production)
- QR Code : chillerlan/php-qrcode
- Images : intervention/image-laravel
- PDF : barryvdh/laravel-dompdf

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configurer la base dans `.env` :

```env
DB_CONNECTION=sqlite
# ou MySQL :
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_DATABASE=akwaba
# DB_USERNAME=root
# DB_PASSWORD=
```

Puis :

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

Ouvrir http://127.0.0.1:8000

## Identifiants admin (démo)

| Champ | Valeur |
|-------|--------|
| URL | `/admin/login` |
| Email | `admin@akwaba.local` |
| Mot de passe | `password` |

**À changer impérativement en production.**

## Routes principales

| Route | Description |
|-------|-------------|
| `/` | Sélection des talents |
| `/talent/{id}/vote` | Vote pour un talent |
| `/resultats` | Résultats publics (rafraîchissement API 30s) |
| `/admin` | Dashboard admin |
| `/admin/qrcode` | QR Code stylisé de l'événement |

## Commandes utiles

```bash
php artisan migrate:fresh --seed   # Réinitialiser avec données démo
php artisan cache:clear
php artisan config:clear
```

## Déploiement cPanel (mutualisé)

1. Uploader les fichiers (sans `node_modules`, `vendor` optionnel si Composer sur serveur).
2. Pointer le document root vers `public/`.
3. Configurer `.env` (MySQL, `APP_URL`, `APP_KEY`).
4. Exécuter `composer install --no-dev`, `php artisan migrate --force`, `php artisan storage:link`.
5. Permissions : `storage/` et `bootstrap/cache/` en écriture (755/775).
6. Activer l'extension PHP **GD** pour le redimensionnement des photos et un rendu QR optimal.

## Sécurité

- CSRF sur tous les formulaires
- Rate limiting : 1 vote / talent / IP / 10 minutes
- Session + cookie anti double vote
- Middleware admin + timeout 30 min
- Journalisation des actions admin (`logs_admin`)

## Licence

MIT
