# Panduan Deployment & Dokumentasi Aplikasi Bendahara

> Aplikasi kas & komite kelas — Laravel 12 + PostgreSQL, berjalan dalam Docker (Nginx + PHP-FPM + PostgreSQL).

Dokumen ini menjelaskan cara **menyiapkan, meng-upload, mengonfigurasi, dan menjalankan** aplikasi untuk **production**, termasuk langkah-langkah keamanan yang**wajib** dilakukan sebelum go-live.

---

## 1. Ringkasan Arsitektur

```
                            ┌───────────────────────────────┐
  Pengguna/Browser          │  NGINX (port 8001)            │
  ── 192.168.101.202:8001 ──► │  container: web              │
                            │  serving /var/www/public      │
                            └──────────────┬────────────────┘
                                           │ php-fpm :9000
                            ┌──────────────▼────────────────┐
                            │  PHP-FPM  (container: app)    │
                            │  Laravel 12  (dir: /var/www)   │
                            └──────────────┬────────────────┘
                                           │ pgsql :5432
                            ┌──────────────▼────────────────┐
                            │  PostgreSQL (container: pg15)  │
                            │  db: project_bendahara         │
                            └───────────────────────────────┘
```

| Container | Nama | Peran | Port |
|---|---|---|---|
| Nginx | `project-bendahara-web` | Web server | `8001` (host) → `80` |
| PHP-FPM | `project-bendahara-app` | Aplikasi Laravel | `9000` (internal) |
| PostgreSQL | `pg15` | Database | `5432` |

---

## 2. Struktur Direktori

```
project-bendahara/
├── docker-compose.yml      # Definisi service (app, web, node)
├── Dockerfile              # Image PHP-FPM + ekstensi pgsql/gd/zip
├── nginx.conf              # Konfigurasi Nginx (blok server :80)
├── scripts/
│   ├── create-user.sh      # Membuat akun pengguna/aplikasi
│   ├── clean-data.sh       # Menghapus data (transaksi/siswa) utk reset
│   └── link-git.txt        # Catatan git (opsional)
└── www/                    # Kode sumber Laravel (volume mount)
```

---

## 3. Prasyarat di Server Tujuan

- **Docker** + **Docker Compose** terpasang.
- **PostgreSQL** container `pg15` sudah berjalan (network `pg15_default` — external).
- Port `8001` tersedia / terbuka untuk akses publik.
- Ruang disk cukup (aplikasi + DB + assets).

---

## 4. Meng-upload Kode ke Server Production

### 4.1 Salin seluruh folder proyek

Pindahkan folder `project-bendahara` ke server tujuan, misalnya di `/home/uinfra/docker/project-bendahara`:

```bash
# dari mesin dev (contoh RECOMMENDED: pakai git)
git clone <url-repo> /home/uinfra/docker/project-bendahara
cd /home/uinfra/docker/project-bendahara

# ATAU pakai scp/rsync (tanpa git)
rsync -az --exclude='storage/framework/cache' --exclude='storage/logs' \
  ./project-bendahara/ user@server:/home/uinfra/docker/project-bendahara/
```

### 4.2 Pastikan `vendor/` dan `public/build/` ada

Kode yang di-upload **sudah menyertakan** folder `vendor/` dan `public/build/` (assets ter-build). Jika tidak, jalankan di mesin dev / CI sebelum upload:

```bash
# install dependency (butuh PHP + Composer)
composer install --no-dev --optimize-autoloader

# build frontend (butuh Node.js)
npm ci && npm run build
```

---

## 5. Konfigurasi Environment (`www/.env`)

Salin dari `.env.example` dan sesuaikan. **Kunci untuk production:**

```ini
APP_NAME="Dashboard Admin"
APP_ENV=production          # ♨️ WAJIB: ubah dari local → production
APP_KEY=base64:...          # ♨️ WAJIB: regenerate — jangan pakai key dev!
APP_DEBUG=false             # ♨️ WAJIB: false (jangan bocorkan stack trace)
APP_URL=http://192.168.101.202:8001

# ... sesuaikan domain/URL bila pakai HTTPS

DB_CONNECTION=pgsql
DB_HOST=pg15
DB_PORT=5432
DB_DATABASE=project_bendahara
DB_USERNAME=laravel
DB_PASSWORD=***GANTI_PASSWORD_KUAT***   # ♨️ ganti dari default laravel123

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

# Telegram Notifikasi Login (opsional)
TELEGRAM_NOTIFY_ENABLED=false
TELEGRAM_BOT_TOKEN=
TELEGRAM_CHAT_ID=
```

> **Catatan tele:**
> - `APP_KEY` harus di-generate, jangan pakai key milik lingkungan development.
> - `APP_DEBUG=false` mencegah kebocoran informasi pada halaman error.
> - Ganti `DB_PASSWORD` dengan password kuat pada **kedua** tempat: `.env` **dan** `docker-compose.yml`.

---

## 6. Membangun & Menjalankan Kontainer

Sejajarkan (build image + jalankan service):

```bash
cd /home/uinfra/docker/project-bendahara
docker compose up -d --build
```

Periksa status:

```bash
docker ps
# pastikan: project-bendahara-web (Up), project-bendahara-app (Up)
```

Akses aplikasi:

```
http://192.168.101.202:8001
```

> Jika `pg15` belum ada / network `pg15_default` belum dibuat, buat dulu:
> ```bash
> docker network create pg15_default
> docker run -d --name pg15 --network pg15_default -e POSTGRES_DB=project_bendahara \
>   -e POSTGRES_USER=laravel -e POSTGRES_PASSWORD=<kuat> postgres:15
> ```

---

## 7. Langkah Inisialisasi Aplikasi (sekali, setelah jalan)

Jalankan di dalam container **app**:

```bash
# 1. Migrasi database (buat tabel)
docker exec project-bendahara-app php artisan migrate --force

# 2. Cache config/routes/views (production)
docker exec project-bendahara-app php artisan config:cache
docker exec project-bendahara-app php artisan route:cache
docker exec project-bendahara-app php artisan view:cache

# 3. Buat akun admin (interaktif)
bash scripts/create-user.sh
#    → tanya Nama, Email, Password
```

> `--force` dibutuhkan saat `APP_ENV=production` agar migrate tidak meminta konfirmasi.

---

## 8. Pengaturan Aplikasi (via Web)

Setelah login, isi melalui menu **Pengaturan**:

| Atribut | Contoh |
|---|---|
| Nama Sekolah | SDIT GIIS |
| Nama Kelas | Grade 6 Jannatul Firdaus |
| Iuran Kas | 15000 |
| Iuran Komite | 75000 |
| Nama Bank | Blu BCA |
| No. Rekening | 1234567890 |
| Nama Pemilik | Gema Putri Hayatunufus |

Data transaksi, bukti, dan siswa diisi oleh bendahara lewat menu aplikasi.

---

## 9. Pembuatan Akun Pengguna

Gunakan skrip `create-user.sh` (otomatis deteksi container):

```bash
# interaktif
bash scripts/clean-data.sh # (bukan ini)
bash scripts/create-user.sh

# atau langsung dengan argumen
bash scripts/create-user.sh "Nama Admin" "admin@sekolah.id" "PasswordKuat!"
```

---

## 10. Membersihkan Data (Restart ke Kondisi Bersih)

> **Penting:** Penghapusan hanya terjadi **saat kamu menjalankan skrip ini**. Data input tidak akan hilang otomatis.

```bash
# konfirmasi interaktif (disarankan)
bash scripts/clean-data.sh

# langsung tanpa konfirmasi (hati-hati)
bash scripts/clean-data.sh --force
```

**Yang dihapus:**
- Semua transaksi (pemasukan & pengeluaran)
- File bukti pembayaran (folder `bukti`)
- Rekap kas & komite
- File laporan (folder `laporan`/`rekap` bila ada)
- Data siswa

**Yang tetap (TIDAK dihapus):**
- Akun pengguna
- Pengaturan (nama sekolah, iuran, bank, dll.)

---

## 11. Backup Database

Unduh backup dari menu **Pengaturan → Backup** (menghasilkan file `.dmp` via `pg_dump`), atau manual:

```bash
docker exec -e PGPASSWORD=... pg15 pg_dump -U laravel -d project_bendahara -F c -f /tmp/backup.dmp
docker cp pg15:/tmp/backup.dmp .
```

---

## 12. ⚠️ Checklist Keamanan WAJIB (dari hasil pentest)

Sebelum go-live publik, pastikan hal-hal berikut:

| No | Item | Status |
|---|---|---|
| 1 | `APP_DEBUG=false` dan `APP_ENV=production` | 🔴 harus |
| 2 | Hapus **route debug** `/diag` & `/slow` (lewat `routes/web.php`) | 🔴 harus |
| 3 | Tambah **rate limit** pada route login (anti brute-force) | 🔴 harus |
| 4 | Gunakan **HTTPS** (redirect HTTP→HTTPS) — data keuangan | 🔴 harus |
| 5 | Regenerate `APP_KEY` & ganti `DB_PASSWORD` dari default | 🔴 harus |
| 6 | Password login kuat (min. 8 karakter, campuran) | 🟠 sebaiknya |
| 7 | Pertimbangkan **privasi halaman `/publik`** (nama+nominal terpampang) | 🟠 sebaiknya |
| 8 | Tambah CSP / HSTS / Referrer-Policy di nginx | 🟡 opsional |

> Rincian temuan lengkap ada di bagian "Hasil Penting dari Pengujian Keamanan" di bawah.

---

## 13. Hasil Penting dari Pengujian Keamanan (Pentest)

Pengujian dilakukan terhadap layanan port 8001. Ringkasan:

### Aman (sudah benar)
- **SQL Injection** — query ter-parameterisasi, param `bulan` di-sanitize.
- **XSS** — input dirender via `{{ }}` (auto-escape); `{!! !!}` hanya ikon SVG statis.
- **Upload bukti** — ekstensi/MIME dibatasi (`jpg,jpeg,png,pdf`, maks 5MB); file `.php` ditolak.
- **CSRF** — semua POST dilindungi token; tanpa token → `419/405`.
- **Route-model binding** — resource tidak valid → 404 bersih.
- **`.env`** — diblokir nginx (403).
- **Session** — `HttpOnly` + `SameSite=Lax` + terenkripsi.

### Perlu diperbaiki (temuan)
1. **Info disclosure** — `APP_DEBUG=true` dapat menampilkan stack trace + env di halaman error 500.
2. **Route debug publik** — `/diag` (log injection) & `/slow` (DoS) tanpa auth.
3. **Tidak ada rate-limit** login → rawan brute-force.
4. **HTTP polos** (tanpa TLS) — data keuangan dapat disadap.
5. **Cookie tanpa `Secure`** (akibat tidak HTTPS).
6. **Header keamanan kurang** — CSP, HSTS, Referrer-Policy.
7. **Password DB lemah** (`laravel123`) & hardcoded di compose.

---

## 14. Perintah Harian yang Berguna

```bash
# Melihat log aplikasi
docker logs -f project-bendahara-app

# Menjalankan maintenance mode
docker exec project-bendahara-app php artisan down
docker exec project-bendahara-app php artisan up

# Menu artisan dalam container
docker exec project-bendahara-app php artisan list
```

---

*Dokumen disusun otomatis untuk kebutuhan deployment ke production. Pastikan kembali catatan di bagian 12 (keamanan) sebelum membuka akses publik.*
