# Arsitektur Aplikasi Web Monitoring PKL (SMK)

Dokumen ini mencakup skema database, struktur folder, logika generate PDF sertifikat, serta contoh UI dashboard dan form penilaian untuk peran **Guru Pembimbing**.

---

## 1. Skema Database & Relasi

Proyek menggunakan **4 role utama** yang tersimpan dalam satu tabel `users` dengan kolom `role`:
- `admin_hubin`
- `siswa_pkl`
- `guru_pembimbing`
- `pembimbing_industri`

Setiap role memiliki profil terpisah (`siswa_profiles`, `guru_profiles`, `industri_profiles`) yang berelasi satu-ke-satu dengan `users`.

### Tabel Utama & Relasi

| Tabel | Keterangan | Relasi Utama |
|-------|------------|--------------|
| `users` | Akun semua user (multi-role) | `hasOne` profil sesuai role |
| `siswa_profiles` | Data siswa (NISN, NIS, jurusan, kelas, dll) | `users` → `siswa_profiles` (1:1) |
| `guru_profiles` | Data guru pembimbing (NIP, mapel, dll) | `users` → `guru_profiles` (1:1) |
| `industri_profiles` | Data perusahaan & pembimbing industri | `users` → `industri_profiles` (1:1) |
| `pkl_siswa` | Penempatan siswa PKL (siswa + guru + industri) | `users` (siswa, guru, industri) |
| `logbook_harian` | Logbook & absensi harian + foto bukti | `pkl_siswa` (1:N) |
| `penilaian_siswa` | Hard skill, soft skill, nilai akhir, predikat | `pkl_siswa` + `users` (guru) |
| `sertifikat` | File PDF, nomor sertifikat, QR code, URL verifikasi | `pkl_siswa` (1:1) |

### Contoh Migration (Laravel)

Semua migration tersedia di folder:
`database/migrations/`

Contoh relational key pada `pkl_siswa`:
```php
$table->foreignId('siswa_id')->constrained('users')->onDelete('cascade');
$table->foreignId('guru_id')->constrained('users')->onDelete('cascade');
$table->foreignId('industri_id')->constrained('users')->onDelete('cascade');
```

`logbook_harian` menyimpan `status_kehadiran` (hadir/sakit/izin/alfa), `foto_bukti`, dan `verifikasi_status` (belum/disetujui/ditolak) untuk alur verifikasi guru.

---

## 2. Struktur Folder Proyek

Berikut struktur folder yang direkomendasikan (berbasis Laravel):

```
pkl-monitoring/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── GuruDashboardController.php   # Dashboard & verifikasi logbook
│   │   │   ├── PenilaianController.php       # Input & simpan penilaian
│   │   │   └── SertifikatController.php      # Generate PDF + QR Code
│   │   └── ...
│   ├── Models/                               # User, PKLSiswa, LogbookHarian, PenilaianSiswa, Sertifikat
│   └── ...
├── database/
│   └── migrations/                           # 8 file migration (users, profiles, pkl_siswa, logbook, penilaian, sertifikat)
├── resources/
│   └── views/
│       ├── guru/
│       │   ├── dashboard.blade.php           # Dashboard Guru Pembimbing (Tailwind)
│       │   └── penilaian/
│       │       └── create.blade.php          # Form Penilaian (Tailwind + JS)
│       └── sertifikat/
│           └── cetak.blade.php               # Template PDF sertifikat (A4)
├── routes/
│   └── web.php                               # Routing dengan middleware role
└── public/
    ├── uploads/                              # Foto bukti logbook
    └── storage/                              # Symlink hasil php artisan storage:link
```

---

## 3. Generate PDF Sertifikat dengan QR Code

Digunakan paket:
- `barryvdh/laravel-dompdf` untuk rendering PDF.
- `simplesoftwareio/simple-qrcode` untuk membuat QR Code verifikasi.

### Controller: `SertifikatController@generate`

```php
public function generate(PKLSiswa $pklSiswa)
{
    $penilaian = \App\Models\PenilaianSiswa::where('pkl_siswa_id', $pklSiswa->id)->first();

    $qrContent = url('/verifikasi-sertifikat/' . $pklSiswa->id);
    $qrCodePng = QrCode::size(200)->format('png')->generate($qrContent);
    $qrDataUri = 'data:image/png;base64,' . base64_encode($qrCodePng);

    $pdf = Pdf::loadView('sertifikat.cetak', compact('pklSiswa', 'penilaian', 'qrDataUri'))
        ->setPaper('a4', 'portrait');

    $fileName = 'sertifikat-' . $pklSiswa->siswa->nisn . '.pdf';
    Storage::disk('public')->put('sertifikat/' . $fileName, $pdf->output());

    Sertifikat::create([
        'pkl_siswa_id' => $pklSiswa->id,
        'nomor_sertifikat' => 'PKL-' . now()->year . '-' . str_pad($pklSiswa->id, 6, '0', STR_PAD_LEFT),
        'tanggal_terbit' => now(),
        'file_path' => 'sertifikat/' . $fileName,
        'verifikasi_url' => $qrContent,
        'is_valid' => true,
    ]);

    return response()->download(storage_path('app/public/sertifikat/' . $fileName));
}
```

### Template PDF: `resources/views/sertifikat/cetak.blade.php`

Berisi layout kertas A4 dengan:
- Header sekolah & judul sertifikat
- Data siswa (nama, NISN/NIS, jurusan, kelas, perusahaan, periode PKL)
- Nilai Hard Skills, Soft Skills, Nilai Akhir & Predikat
- Area tanda tangan (Guru Pembimbing, Pembimbing Industri, Kepala Sekolah)
- QR Code di pojok kanan bawah yang berisi URL verifikasi

Catatan: Jalankan `php artisan storage:link` agar file PDF dapat diakses/download secara publik.

---

## 4. UI / Layout Guru Pembimbing (Tailwind CSS)

### 4.1 Dashboard Guru (`resources/views/guru/dashboard.blade.php`)

Tampilan utama guru setelah login dengan komponen:
- **Navbar** dengan nama guru dan tombol keluar.
- **3 Statistik Cards**: Total Siswa Bimbingan, Logbook Belum Diverifikasi, Logbook Disetujui.
- **Tabel Logbook Baru**: menampilkan tanggal, nama siswa, kegiatan, status kehadiran (badge warna), dan dropdown verifikasi (Setujui / Tolak) + catatan guru.
- Setiap baris logbook memiliki form verifikasi langsung yang mengupdate `verifikasi_status`, `verified_by`, dan `verified_at`.

### 4.2 Form Penilaian (`resources/views/guru/penilaian/create.blade.php`)

Form input penilaian dengan fitur:
- Input **Hard Skills** dan **Soft Skills** (skala 0–100).
- JavaScript menghitung otomatis **Nilai Akhir** (rumus: `0.6 × Hard + 0.4 × Soft`) dan **Predikat**:
  - A ≥ 85, B ≥ 75, C ≥ 65, D ≥ 55, E < 55.
- Input catatan tambahan dan tanggal penilaian.
- Tombol **Simpan Nilai** yang menyimpan ke tabel `penilaian_siswa`.

---

## 5. Langkah Implementasi Cepat

1. Clone/buat proyek Laravel baru.
2. Salin semua file migration ke `database/migrations/` dan jalankan:
   ```bash
   php artisan migrate
   ```
3. Salin controller ke `app/Http/Controllers/`.
4. Salin blade views ke `resources/views/`.
5. Tambahkan route di `routes/web.php` (disertakan di proyek ini).
6. Install dependensi PDF & QR Code:
   ```bash
   composer require barryvdh/laravel-dompdf simplesoftwareio/simple-qrcode
   php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
   php artisan storage:link
   ```
7. Atur middleware `role` untuk memfilter akses berdasarkan kolom `role` pada tabel `users`.

Semua berkas lengkap telah disimpan di workspace folder `pkl-monitoring/`.
