# Deploy Laravel ke Render

Proyek ini menggunakan Laravel 13.17, PHP `^8.3`, MySQL, dan Vite. Image memakai PHP 8.4 karena konfigurasi MySQL proyek menggunakan kelas `Pdo\Mysql` yang tersedia mulai PHP 8.4. Dockerfile membangun aset Vite dan memasang dependensi Composer production, lalu menjalankan Apache dengan `public/` sebagai document root. Endpoint health check Laravel tersedia di `/up`.

## 1. Siapkan repository GitHub

1. Buat repository baru di GitHub.
2. Pastikan `.env` tidak masuk ke Git. File ini sudah tercantum di `.gitignore`; jangan pernah menghapus pengecualian itu atau mengunggah password/API key.
3. Commit dan push kode beserta `Dockerfile`, `.dockerignore`, dan folder `docker/`.
4. Jika membuat repository baru dari folder yang sudah punya Git, tambahkan remote GitHub lalu push branch utama sesuai petunjuk GitHub.

## 2. Buat Web Service di Render

1. Pilih **New + → Web Service**, hubungkan repository GitHub, dan pilih branch yang akan dipakai.
2. Pilih **Docker** sebagai runtime. Render akan membaca `Dockerfile` di root repository.
3. Pilih paket Free jika tersedia untuk akun/region Anda. Service gratis dapat tidur setelah tidak aktif, mengalami cold start, dan filesystem container bersifat sementara. Render Free tidak menyediakan Shell/SSH untuk service web.
4. Tambahkan environment variables dari bagian berikut sebelum deployment.
5. Isi health check path dengan `/up` bila pengaturan tersebut tersedia.
6. Deploy. Aplikasi mendengarkan port dari variabel `PORT` yang disediakan Render.

## 3. Environment variables

Tambahkan di **Render → Web Service → Environment**. Jangan mengisi `APP_KEY` dengan nilai contoh atau menyimpan rahasia di repository.

| Variable | Nilai |
|---|---|
| `APP_NAME` | `Monitoring PKL` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | Buat sekali dengan `php artisan key:generate --show` di komputer lokal, lalu salin hasil `base64:...` |
| `APP_URL` | URL HTTPS Web Service Render, mis. `https://nama-service.onrender.com` |
| `LOG_CHANNEL` | `stderr` |
| `LOG_LEVEL` | `info` |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | Host database MySQL eksternal |
| `DB_PORT` | Port MySQL, biasanya `3306` |
| `DB_DATABASE` | Nama database |
| `DB_USERNAME` | User database |
| `DB_PASSWORD` | Password database (secret di Render) |
| `SESSION_DRIVER` | `database` |
| `CACHE_STORE` | `database` |
| `QUEUE_CONNECTION` | `database` |
| `FILESYSTEM_DISK` | `public` untuk perilaku upload saat ini; lihat catatan storage |
| `SESSION_SECURE_COOKIE` | `true` |

Jangan mengganti `APP_KEY` setelah aplikasi digunakan; data terenkripsi dan sesi yang memakai key lama tidak lagi dapat dibaca. Pertahankan `APP_DEBUG=false` di production.

## 4. Hubungkan MySQL eksternal

Render tidak menyediakan MySQL terkelola gratis; layanan MySQL yang dijalankan di Render memerlukan service dan disk berbayar. Buat database MySQL pada penyedia eksternal yang Anda pilih dan pastikan penyedia mengizinkan koneksi dari Render. Isi `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, serta `DB_PASSWORD` di Environment Render. Gunakan alamat publik/eksternal yang diberikan penyedia database, bukan `127.0.0.1`. Render dapat membatasi service Free yang memulai traffic keluar internet dalam jumlah tinggi, termasuk koneksi ke database eksternal.

Bila penyedia hanya memberi URL koneksi, pecah URL tersebut ke lima variable di atas atau gunakan `DB_URL` sesuai format URL DSN Laravel yang didukung penyedia; jangan mengisi keduanya dengan nilai berbeda. Jangan menaruh kredensial database di file yang di-commit.

## 5. Jalankan migration

Paket Free tidak menyediakan Shell di Render. Jalankan migration dari komputer/CI yang memiliki PHP 8.4+, dependensi Composer, dan akses jaringan ke MySQL. Atur variable database yang sama dengan Render di PowerShell (ganti placeholder; jangan menyimpan nilainya di file yang di-commit), lalu jalankan:

```powershell
$env:APP_ENV = "production"
$env:APP_DEBUG = "false"
$env:DB_CONNECTION = "mysql"
$env:DB_HOST = "<host-mysql-eksternal>"
$env:DB_PORT = "3306"
$env:DB_DATABASE = "<nama-database>"
$env:DB_USERNAME = "<user-database>"
$env:DB_PASSWORD = "<password-database>"
php artisan migrate --force
```

Migration **tidak** dijalankan otomatis saat container start. Perintah di atas aman dijalankan ulang dalam arti Laravel hanya menjalankan migration yang belum tercatat; tinjau migration baru dan buat backup database sebelum perubahan skema penting. Jangan jalankan `migrate:fresh` atau `db:wipe` pada database berisi data. Lepas variable sesi setelah selesai dengan menutup terminal atau menghapus `$env:DB_PASSWORD` dan variable `DB_*`.

## 6. Uji setelah deployment

1. Buka URL HTTPS Web Service dan pastikan halaman utama tampil.
2. Buka `https://nama-service.onrender.com/up`; respons sehat seharusnya HTTP 200.
3. Uji login dan alur aplikasi yang memakai database.
4. Periksa log service di Render jika ada error koneksi atau konfigurasi.

## 7. Perbarui aplikasi

Commit dan push perubahan ke branch yang terhubung. Render akan membangun image Docker baru dan melakukan deployment. Setelah menambah migration, jalankan perintah migration dari komputer/CI yang dapat mengakses database seperti dijelaskan di atas (Free Web Service tidak menyediakan Shell). Jangan mengandalkan file di container untuk bertahan dari deployment/restart.

## Upload, sesi, cache, dan queue

Sesi, cache, dan queue saat ini memakai database agar tidak bergantung pada disk sementara. Aplikasi menyimpan avatar, logo, bukti logbook, dan file sertifikat melalui disk `public` lokal; file lokal dapat hilang saat Render reinisialisasi service. File yang sudah ada tidak dihapus oleh perubahan ini, tetapi file yang terlanjur berada hanya di container tidak dijamin tetap tersedia.

Untuk upload persisten, gunakan object storage S3-compatible (misalnya Cloudflare R2 atau layanan sejenis) lalu sambungkan disk `public` Laravel ke bucket tersebut. `config/filesystems.php` sudah memiliki konfigurasi S3, tetapi repository ini belum memasang adapter Flysystem S3; sebelum menggunakan bucket, tambahkan dependency `league/flysystem-aws-s3-v3` dan atur variable `AWS_*` sesuai penyedia. Migrasikan/unggah salinan file yang sudah ada sebelum beralih disk agar referensi database tetap berfungsi.

Queue database hanya memproses pekerjaan ketika ada worker. Jika nanti aplikasi menambahkan job asynchronous, buat worker service terpisah dengan perintah `php artisan queue:work`; jangan menganggap Web Service otomatis menjalankan worker.
