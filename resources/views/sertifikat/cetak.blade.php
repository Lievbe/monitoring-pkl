@php
    $siswa = $pklSiswa->siswa?->siswaProfile;
    $guruNama = $pklSiswa->guru_nama ?: ($pklSiswa->guru?->guruProfile?->nama_lengkap ?? $pklSiswa->guru?->name);
    $perusahaan = $sertifikat->nama_perusahaan ?: ($pklSiswa->nama_perusahaan ?: $pklSiswa->industri?->industriProfile?->nama_perusahaan);
    $pembimbing = $sertifikat->nama_pembimbing ?: ($pklSiswa->nama_pembimbing ?: $pklSiswa->industri?->industriProfile?->nama_pembimbing);
    $schoolName = $sertifikat->nama_sekolah ?: ($schoolSettings->nama_sekolah ?? 'SMKN 43 Jakarta');
    $schoolLogoPath = $sertifikat->logos_snapshot_at ? $sertifikat->logo_sekolah_path : ($schoolSettings->logo_path ?? null);
    $companyLogoPath = $sertifikat->logos_snapshot_at ? $sertifikat->logo_perusahaan_path : $pklSiswa->industri?->industriProfile?->logo_path;
    $disk = \Illuminate\Support\Facades\Storage::disk('public');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sertifikat {{ $sertifikat->nomor_sertifikat }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        @page { size: A4 landscape; margin: 0; }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 28px; background: #e8edf5; color: #172554; font-family: Arial, Helvetica, sans-serif; }
        .toolbar { display: flex; justify-content: center; gap: 12px; margin: 0 auto 20px; }
        .toolbar a, .toolbar button { border: 0; border-radius: 10px; padding: 12px 18px; font-size: 14px; font-weight: 700; text-decoration: none; cursor: pointer; }
        .print-button { background: #1d4ed8; color: #fff; }.back-button { background: #fff; color: #1e3a8a; }.delete-certificate { background: #dc2626; color: #fff; }
        .sheet { width: 1120px; min-height: 660px; margin: auto; padding: 24px; border: 1px solid #cbd5e1; background: #fff; box-shadow: 0 20px 60px #1e3a8a20; }
        .frame { position: relative; display: flex; min-height: 610px; height: 100%; flex-direction: column; align-items: center; border: 4px double #1e3a8a; padding: 28px 48px 36px; text-align: center; }
        .frame::before, .frame::after { position: absolute; width: 48px; height: 48px; border-color: #2563eb; content: ''; }
        .frame::before { top: 12px; left: 12px; border-top: 2px solid; border-left: 2px solid; }.frame::after { right: 12px; bottom: 12px; border-right: 2px solid; border-bottom: 2px solid; }
        .certificate-header { display: grid; width: 100%; grid-template-columns: 150px 1fr 150px; align-items: center; gap: 20px; }
        .logo-block { display: flex; min-height: 118px; flex-direction: column; align-items: center; justify-content: center; gap: 7px; color: #1e3a8a; font-size: 10px; font-weight: 700; letter-spacing: .04em; text-align: center; text-transform: uppercase; }
        .logo-frame { display: grid; width: 108px; height: 78px; place-items: center; overflow: hidden; border: 1px solid #dbeafe; border-radius: 12px; background: linear-gradient(145deg, #fff, #f8fbff); }
        .logo-frame img { display: block; width: 100%; height: 100%; padding: 7px; object-fit: contain; }
        .logo-fallback { display: grid; width: 44px; height: 44px; place-items: center; border: 2px solid #bfdbfe; border-radius: 50%; background: #eff6ff; color: #1d4ed8; font-family: Georgia, serif; font-size: 18px; font-weight: 700; }
        .heading h1 { margin: 0; color: #172554; font-family: Georgia, serif; font-size: 34px; letter-spacing: .12em; }.subtitle { margin: 7px 0 0; color: #1d4ed8; font-size: 13px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; }
        .intro { margin: 22px 0 4px; color: #64748b; font-size: 14px; }.student-name { margin: 4px 0; color: #1d4ed8; font-family: Georgia, serif; font-size: 30px; font-weight: 700; }
        .student-meta { margin: 4px 0; color: #475569; font-size: 13px; }.description { max-width: 760px; margin: 20px auto 0; color: #334155; font-size: 14px; line-height: 1.8; }
        .score { display: flex; gap: 12px; margin-top: 16px; }.score span { border: 1px solid #dbeafe; border-radius: 8px; padding: 7px 12px; color: #475569; font-size: 12px; }.score strong { color: #1e3a8a; }
        .certificate-footer { width: 100%; margin-top: auto; }
        .signatures { display: grid; width: min(760px, 100%); grid-template-columns: repeat(3, 1fr); gap: 28px; margin: 0 auto; padding-top: 24px; }.signature { color: #475569; font-size: 12px; }.signature strong { display: block; min-height: 18px; margin-top: 34px; border-top: 1px solid #94a3b8; padding-top: 7px; color: #172554; }.signature small { display: block; margin-top: 3px; color: #64748b; font-size: 10px; }
        .number { width: 100%; margin: 18px 0 0; color: #64748b; font-size: 10px; text-align: right; }
        @media (max-width: 700px) { body { padding: 10px; }.sheet { width: 100%; min-height: 0; padding: 10px; }.frame { min-height: 650px; padding: 24px 16px 30px; }.certificate-header { grid-template-columns: 82px 1fr 82px; gap: 5px; }.logo-block { min-height: 92px; font-size: 7px; }.logo-frame { width: 66px; height: 54px; border-radius: 9px; }.logo-frame img { padding: 4px; }.logo-fallback { width: 34px; height: 34px; font-size: 14px; }.heading h1 { font-size: 23px; }.subtitle { font-size: 9px; }.student-name { font-size: 23px; }.signatures { gap: 10px; }.signature { font-size: 10px; }.number { font-size: 8px; } }
        @media print { body { padding: 0; background: #fff; print-color-adjust: exact; -webkit-print-color-adjust: exact; }.toolbar { display: none; }.sheet { width: 297mm; height: 210mm; min-height: 0; padding: 8mm; border: 0; box-shadow: none; }.frame { min-height: 0; padding: 8mm 12mm 10mm; }.certificate-footer { margin-top: auto; }.signatures { padding-top: 10mm; }.number { margin-top: 4mm; } }
    </style>
</head>
<body>
    <nav class="toolbar" aria-label="Aksi sertifikat"><button class="print-button" type="button" onclick="window.print()">Cetak / Simpan PDF</button><a class="back-button" href="{{ route('dashboard') }}">Kembali ke dashboard</a>@if ($isAdmin)<form method="POST" action="{{ route('admin.placements.sertifikat.destroy', $pklSiswa) }}">@csrf @method('DELETE')<button type="button" data-delete-trigger data-delete-name="sertifikat {{ $sertifikat->nomor_sertifikat }}" class="delete-certificate inline-flex items-center gap-2"><i data-lucide="trash-2" class="h-4 w-4"></i>Hapus Sertifikat</button></form>@endif</nav>
    <main class="sheet"><section class="frame" aria-label="Sertifikat praktik kerja lapangan">
        <header class="certificate-header">
            <div class="logo-block"><span class="logo-frame">@if ($schoolLogoPath && $disk->exists($schoolLogoPath))<img src="{{ $disk->url($schoolLogoPath) }}" alt="Logo {{ $schoolName }}">@else<span class="logo-fallback">SMK</span>@endif</span><span>{{ $schoolName }}</span></div>
            <div class="heading"><h1>SERTIFIKAT</h1><p class="subtitle">Praktik Kerja Lapangan</p></div>
            <div class="logo-block"><span class="logo-frame">@if ($companyLogoPath && $disk->exists($companyLogoPath))<img src="{{ $disk->url($companyLogoPath) }}" alt="Logo {{ $perusahaan ?: 'Perusahaan Mitra' }}">@else<span class="logo-fallback">{{ collect(preg_split('/\s+/', trim($perusahaan ?: 'Mitra')))->take(2)->map(fn ($word) => mb_substr($word, 0, 1))->implode('') }}</span>@endif</span><span>{{ $perusahaan ?: 'Perusahaan Mitra' }}</span></div>
        </header>
        <p class="intro">Diberikan dengan bangga kepada</p>
        <p class="student-name">{{ $siswa?->nama_lengkap ?? $pklSiswa->siswa?->name }}</p>
        <p class="student-meta">NIS {{ $siswa?->nis ?: '—' }} <span aria-hidden="true">·</span> NISN {{ $siswa?->nisn ?: '—' }} <span aria-hidden="true">·</span> {{ $siswa?->kelas ?: '—' }} <span aria-hidden="true">·</span> {{ $siswa?->jurusan ?: 'Program keahlian belum tersedia' }}</p>
        <p class="description">Telah menyelesaikan kegiatan Praktik Kerja Lapangan di <strong>{{ $perusahaan ?: '—' }}</strong> pada tanggal {{ $pklSiswa->tanggal_mulai->translatedFormat('d F Y') }} sampai dengan {{ $pklSiswa->tanggal_selesai->translatedFormat('d F Y') }}. Semoga pengalaman dan kompetensi yang diperoleh menjadi bekal untuk berkarya.</p>
        @if ($pklSiswa->penilaian)<div class="score"><span>Nilai akhir <strong>{{ $pklSiswa->penilaian->nilai_akhir ?? '—' }}</strong></span><span>Predikat <strong>{{ $pklSiswa->penilaian->predikat ?? '—' }}</strong></span></div>@endif
        <footer class="certificate-footer"><div class="signatures"><div class="signature">Guru Pembimbing<strong>{{ $guruNama ?: ' ' }}</strong><small>{{ $pklSiswa->guru_nip ?: ' ' }}</small></div><div class="signature">Pembimbing Mitra<strong>{{ $pembimbing ?: ' ' }}</strong><small>{{ $perusahaan ?: ' ' }}</small></div><div class="signature">Pihak Sekolah<strong>Kepala Sekolah</strong><small>{{ $schoolName }}</small></div></div>
        <p class="number">Nomor {{ $sertifikat->nomor_sertifikat }} <span aria-hidden="true">·</span> Diterbitkan {{ $sertifikat->tanggal_terbit->translatedFormat('d F Y') }}</p>
        </footer>
    </section></main>
    <x-delete-confirmation-modal />
    <script>document.addEventListener('DOMContentLoaded', () => window.lucide?.createIcons());</script>
</body>
</html>
