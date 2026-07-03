# Piramid — Aplikasi Qurban, Aqiqah & Sedekah

Platform web untuk memfasilitasi ibadah Qurban, Aqiqah, dan Sedekah: user memilih hewan,
menentukan skema penyaluran, membayar via Midtrans atau transfer manual, lalu memantau
status pengerjaan lengkap dengan bukti foto/video yang diunggah admin di tiap tahapan.

**Stack**: Laravel 13 · Inertia.js · Vue 3 · Tailwind CSS · PostgreSQL · Midtrans · Cloudinary

## Fitur

**Sisi User**
- Pilihan layanan dinamis (Qurban/Aqiqah/Sedekah) yang dikontrol admin (USR-01)
- Katalog hewan terfilter per layanan / pointing (USR-02)
- Checkout dengan skema penyaluran: PT/Yayasan atau kirim ke alamat sendiri —
  form alamat muncul kondisional (USR-03)
- Pembayaran Midtrans (VA/E-Wallet/Retail, Snap popup) atau transfer bank manual
  dengan unggah bukti (USR-04)
- Halaman pelacakan: invoice, timeline 5 tahap status, galeri dokumentasi (USR-05)

**Sisi Admin** (`/admin`)
- CRUD layanan master + toggle aktif/nonaktif (ADM-01)
- CRUD produk hewan + pemetaan many-to-many ke layanan (ADM-02)
- Verifikasi transaksi: setujui/tolak transfer manual, settlement Midtrans otomatis
  via webhook ber-signature (ADM-03)
- Manajemen status: Menunggu → Dibayar → Hewan Disiapkan → Tersembelih → Didistribusikan,
  wajib unggah dokumentasi per tahap sebelum naik (ADM-04)

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

Akun seed:

| Role  | Email                | Password   |
|-------|----------------------|------------|
| Admin | `admin@pyramid.test` | `password` |
| User  | `user@pyramid.test`  | `password` |

Kredensial ini dev-only. Di server, buat admin dengan
`php artisan user:make-admin {email}`.

## Kredensial eksternal (isi di `.env`)

- **Midtrans sandbox** — `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`
  (dashboard.sandbox.midtrans.com). Arahkan notification URL ke
  `POST /webhooks/midtrans/notification`.
- **Cloudinary** — `CLOUDINARY_CLOUD_NAME`, `CLOUDINARY_API_KEY`, `CLOUDINARY_API_SECRET`.
  Tanpa ini app tetap jalan: bukti transfer jatuh ke storage lokal dan upload
  dokumentasi admin memakai jalur server-proxy.

## Testing

```bash
php artisan test
```

37 test (12 smoke end-to-end + auth bawaan Breeze) mencakup seluruh alur PRD:
checkout + guard stok, verifikasi pembayaran, aturan kenaikan status, webhook Midtrans.

## Deploy

**Dokploy (utama)** — point ke repo ini, build dari `Dockerfile` (multi-stage:
composer → node/vite → php8.5-apache). Isi env production di panel Dokploy,
jalankan `php artisan migrate --force` sebagai post-deploy. Uji image lokal:

```bash
docker compose --profile app up
```

**Jenkins (cadangan)** — `Jenkinsfile` tersedia sebagai wrapper docker build/compose;
deploy stage menunggu detail VPS.

## Struktur penting

```
app/Services/Midtrans/     # Snap token, verifikasi signature, handle notifikasi
app/Services/Cloudinary/   # Signed direct-browser upload + server-proxy fallback
app/Enums/                 # TransactionStatus (5 tahap), PaymentStatus, dll.
routes/                    # Terpisah per domain: landing, user_catalog, admin_*, webhooks
resources/js/Pages/        # Inertia pages (user + Admin/**)
```
