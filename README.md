# Pyramid — Aplikasi Qurban, Aqiqah & Sedekah

Laravel 13 + Inertia + Vue 3 (Breeze), PostgreSQL, Midtrans, Cloudinary.

## Setup dev lokal

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
# Postgres via Docker (port 5433) — atau biarkan sqlite default:
docker compose up -d postgres
php artisan migrate:fresh --seed
npm run dev          # terminal 1
php artisan serve    # terminal 2
```

Akun seed: admin `admin@pyramid.test` / `password`, user `user@pyramid.test` / `password`.

## Kredensial eksternal (isi di .env)

- **Midtrans sandbox**: MIDTRANS_SERVER_KEY, MIDTRANS_CLIENT_KEY (dashboard.sandbox.midtrans.com). Set webhook URL ke `POST /webhooks/midtrans/notification`.
- **Cloudinary**: CLOUDINARY_CLOUD_NAME/API_KEY/API_SECRET. Tanpa ini upload bukti transfer jatuh ke storage lokal (`php artisan storage:link` sudah dijalankan) dan upload dokumentasi admin lewat server-proxy.

## Deploy (Dokploy)

Point Dokploy ke repo ini, build dari `Dockerfile`. Isi env production di panel Dokploy. Post-deploy: `php artisan migrate --force`. Compose profile `app` tersedia untuk uji image lokal: `docker compose --profile app up`.

`Jenkinsfile` adalah cadangan bila jalur CI Jenkins dipakai — deploy stage masih TBD dengan pemilik VPS.
