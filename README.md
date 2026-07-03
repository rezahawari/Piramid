# Piramid — Aplikasi Qurban, Aqiqah & Sedekah

Platform web untuk memfasilitasi ibadah Qurban, Aqiqah, dan Sedekah: user memilih hewan,
menentukan skema penyaluran, membayar via Midtrans atau transfer manual, lalu memantau
status pengerjaan lengkap dengan bukti foto/video yang diunggah admin di tiap tahapan.

**Stack**: Laravel 13 · Inertia.js · Vue 3 · Tailwind CSS · PostgreSQL · Midtrans · Cloudinary

**Live**: https://piramid.afinzaki.my.id (Dokploy, branch `pyramid`)

---

## Fitur

### Sisi User
| ID | Fitur |
|----|-------|
| USR-01 | Pilihan layanan dinamis (Qurban/Aqiqah/Sedekah), dikontrol admin |
| USR-02 | Katalog hewan terfilter per layanan (pointing many-to-many) |
| USR-03 | Checkout: skema penyaluran PT/Yayasan atau alamat sendiri (form alamat kondisional) |
| USR-04 | Pembayaran Midtrans (VA/E-Wallet/Retail via Snap) atau transfer manual + unggah bukti |
| USR-05 | Pelacakan: invoice, timeline 5 tahap status, galeri dokumentasi |

### Sisi Admin (`/admin`)
| ID | Fitur |
|----|-------|
| ADM-01 | CRUD layanan master + toggle aktif/nonaktif |
| ADM-02 | CRUD produk hewan + pemetaan ke banyak layanan |
| ADM-03 | Verifikasi transaksi: setujui/tolak transfer manual; settlement Midtrans otomatis via webhook ber-signature |
| ADM-04 | Naikkan status `Menunggu → Dibayar → Hewan Disiapkan → Tersembelih → Didistribusikan`; wajib unggah dokumentasi per tahap; hanya maju satu tahap; terkunci sebelum lunas |

Aturan bisnis penting: `payment_status` (pending/paid/rejected/expired/cancelled) sengaja
dipisah dari `status` operasional 5 tahap; stok dikembalikan otomatis saat pembayaran
ditolak/kedaluwarsa/dibatalkan, dengan guard anti-restock ganda dan anti-oversell.

---

## Setup dev lokal

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
# Postgres via Docker (host port 5433) — atau biarkan sqlite default:
docker compose up -d postgres
php artisan migrate:fresh --seed
npm run dev          # terminal 1
php artisan serve    # terminal 2
```

Akun seed (dev only — jangan dipakai produksi):

| Role  | Email                | Password   |
|-------|----------------------|------------|
| Admin | `admin@pyramid.test` | `password` |
| User  | `user@pyramid.test`  | `password` |

Di server, buat admin dari akun asli: `php artisan user:make-admin {email}`.

## Kredensial eksternal (isi di `.env`)

- **Midtrans sandbox** — `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`
  (dashboard.sandbox.midtrans.com). Arahkan Payment Notification URL ke
  `POST https://<domain>/webhooks/midtrans/notification`.
- **Cloudinary** — `CLOUDINARY_CLOUD_NAME`, `CLOUDINARY_API_KEY`, `CLOUDINARY_API_SECRET`.
  Tanpa ini app tetap jalan: bukti transfer jatuh ke storage lokal, dokumentasi admin
  memakai jalur server-proxy. Dengan kredensial, upload memakai signed direct-browser
  upload (file besar tidak melewati PHP).

## Testing

```bash
php artisan test
```

37 test (12 smoke end-to-end + auth Breeze) mencakup seluruh alur PRD: checkout + guard
stok + validasi alamat kondisional, kepemilikan transaksi (403), CRUD admin + pointing,
setujui/tolak + pengembalian stok, aturan kenaikan status, webhook Midtrans (settlement
+ signature palsu 403). Seeder bebas Faker sehingga test & seed produksi memakai jalur
yang sama.

---

## Deploy — Dokploy (jalur utama)

Arsitektur: **2 service terpisah** dalam 1 project — app (build dari `Dockerfile`
multi-stage: composer → node/vite → `php:8.5-apache`) dan database PostgreSQL.
Container app stateless; data hidup di service DB, aman dari rebuild.

### 1. Service database (sekali saja)
Create Service → Database → PostgreSQL. Catat dari halaman **Internal Credentials**:
Internal Host, user, password, database name.

### 2. Service aplikasi
- Provider: GitHub → repo ini → branch **`pyramid`** · Build Path `/`
- Build Type: **Dockerfile** → Docker File `Dockerfile` → Context kosong
  (jangan Nixpacks — PHP bawaannya terlalu tua untuk Laravel 13)
- **Domains**: host domain kamu → container port **80** → HTTPS on

### 3. Environment (isi SEBELUM deploy pertama; env di-inject saat container dibuat)

```env
APP_NAME=Piramid
APP_KEY=            # php artisan key:generate --show
APP_ENV=production
APP_DEBUG=false
APP_URL=https://piramid.afinzaki.my.id   # WAJIB https — http bikin mixed content
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id
LOG_CHANNEL=stderr  # error Laravel muncul di tab Logs Dokploy

DB_CONNECTION=pgsql
DB_HOST=            # Internal Host service Postgres (bukan localhost!)
DB_PORT=5432
DB_DATABASE=qurban_pyramid
DB_USERNAME=qurban_app
DB_PASSWORD=

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false

CLOUDINARY_CLOUD_NAME=
CLOUDINARY_API_KEY=
CLOUDINARY_API_SECRET=
CLOUDINARY_UPLOAD_FOLDER=qurban-pyramid
```

### 4. Deploy → migrate
Deploy, tunggu build hijau, lalu Open Terminal → pilih container app →

```bash
cd /var/www/html && php artisan migrate --force && php artisan db:seed --force
```

Autodeploy on + trigger On Push: tiap push ke `pyramid` = deploy otomatis.

### Troubleshooting (semua pernah kejadian)

| Gejala | Penyebab | Solusi |
|---|---|---|
| `404 page not found` polos | Domain belum ke-route Traefik | Tab Domains: tambah host + port 80 |
| Nixpacks: `syntax error, unexpected token "{"` | PHP builder terlalu tua | Build Type → Dockerfile |
| `cd: can't cd to .../Dockerfile` | Path Dockerfile diisi di field context | `Dockerfile` di field Docker File, context kosong |
| Build: `cp: cannot stat 'modules/*'` di opcache | opcache sudah built-in di php:8.5 | Jangan `docker-php-ext-install opcache` (sudah di-fix di Dockerfile) |
| `Could not open input file: artisan` | Terminal buka di `/` | `cd /var/www/html` dulu |
| Error nyebut sqlite padahal env pgsql | Env belum ke-inject / container lama | Save env → Deploy ulang → pilih container terbaru |
| `could not translate host name` | `DB_HOST` salah/masih placeholder | Isi Internal Host service Postgres |
| Seed: `Call to undefined function fake()` | Faker = dev-dependency, tak ada di image | Seeder sudah bebas factory (fixed) |
| 500 tapi `/up` 200 | Tabel belum ada (`SESSION_DRIVER=database`) | Jalankan migrate |
| Halaman blank, console block asset http | Proxy tak terdeteksi + APP_URL http | `trustProxies` (sudah di-fix) + `APP_URL` https |

### Uji image di lokal

```bash
docker compose --profile app up   # app di :8080 + postgres
```

### Jenkins (cadangan)
`Jenkinsfile` tersedia sebagai wrapper docker build/compose; deploy stage TBD.

---

## Checklist pasca-deploy

- [ ] Ganti akun admin seed: register akun asli → `php artisan user:make-admin <email>` → nonaktifkan `admin@pyramid.test`
- [ ] Isi kredensial Midtrans + set webhook URL di dashboard Midtrans
- [ ] Isi kredensial Cloudinary
- [ ] Ganti nomor rekening di `config/payment.php` dengan rekening asli PT
- [ ] `MIDTRANS_IS_PRODUCTION=true` saat go-live beneran

## Struktur penting

```
app/Services/Midtrans/     # Snap token, verifikasi signature SHA-512, handle notifikasi
app/Services/Cloudinary/   # Signed direct-browser upload + server-proxy fallback
app/Enums/                 # TransactionStatus (5 tahap + pipeline), PaymentStatus, dll.
app/Http/Controllers/      # User: Catalog/Checkout/Transaction/Payment/ManualTransfer
app/Http/Controllers/Admin # Service/Product CRUD, Transaction approve-reject, Status+Dokumentasi
routes/                    # Terpisah per domain: landing, user_catalog, admin_*, webhooks
resources/js/Pages/        # Inertia pages (user + Admin/**)
database/seeders/          # Idempotent, bebas Faker — aman utk produksi
Dockerfile                 # Multi-stage: composer → node → php:8.5-apache
docker-compose.yml         # Dev lokal (postgres) + profile "app" utk uji image
```
