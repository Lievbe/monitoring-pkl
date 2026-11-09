@php
    $siswa = $pklSiswa->siswa?->siswaProfile;
    $guruNama = $pklSiswa->guru_nama ?: ($pklSiswa->guru?->guruProfile?->nama_lengkap ?? $pklSiswa->guru?->name);
    $perusahaan = $pklSiswa->nama_perusahaan ?: $pklSiswa->industri?->industriProfile?->nama_perusahaan;
    $pembimbing = $pklSiswa->nama_pembimbing ?: $pklSiswa->industri?->industriProfile?->nama_pembimbing;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sertifikat {{ $sertifikat->nomor_sertifikat }}</title>
    <style>
        @page { size: A4 landscape; margin: 0; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; padding: 32px; background: #e8edf5; color: #172554; font-family: Arial, Helvetica, sans-serif; }
        .toolbar { display: flex; justify-content: center; gap: 12px; margin: 0 auto 24px; }
        .toolbar a, .toolbar button { border: 0; border-radius: 12px; padding: 13px 18px; font-size: 14px; font-weight: 700; text-decoration: none; cursor: pointer; }
        .print-button { background: #1d4ed8; color: #fff; }
        .back-button { background: #fff; color: #1e3a8a; }
        .sheet { position: relative; display: flex; min-height: 660px; max-width: 1050px; margin: 0 auto; align-items: center; justify-content: center; overflow: hidden; border: 1px solid #d5b66a; background: #fff; padding: 28px; box-shadow: 0 24px 70px rgba(30, 58, 138, .14); }
        .frame { position: relative; display: flex; width: 100%; min-height: 600px; flex-direction: column; align-items: center; justify-content: center; border: 3px double #c49a3a; padding: 46px 58px; text-align: center; }
        .frame::before, .frame::after { position: absolute; width: 74px; height: 74px; border-color: #c49a3a; content: ""; }
        .frame::before { top: 14px; left: 14px; border-top: 2px solid; border-left: 2px solid; }
        .frame::after { right: 14px; bottom: 14px; border-right: 2px solid; border-bottom: 2px solid; }
        .seal { display: grid; width: 52px; height: 52px; place-items: center; border: 1px solid #c49a3a; border-radius: 50%; color: #b3872f; font-size: 22px; }
        .school { margin-top: 16px; color: #64748b; font-size: 12px; font-weight: 700; letter-spacing: .24em; text-transform: uppercase; }
        h1 { margin: 20px 0 4px; color: #172554; font-family: Georgia, "Times New Roman", serif; font-size: 38px; letter-spacing: .14em; }
        .subtitle { color: #b3872f; font-size: 14px; font-weight: 700; letter-spacing: .18em; text-transform: uppercase; }
        .intro { margin: 26px 0 8px; color: #64748b; font-size: 14px; }
        .student-name { margin: 3px 0 8px; color: #1d4ed8; font-family: Georgia, "Times New Roman", serif; font-size: 31px; font-weight: 700; }
        .student-meta { color: #64748b; font-size: 13px; }
        .description { max-width: 700px; margin: 22px auto 0; color: #475569; font-size: 14px; line-height: 1.8; }
        .score { display: flex; gap: 12px; margin-top: 22px; }
        .score span { border: 1px solid #e2e8f0; border-radius: 10px; padding: 8px 13px; color: #475569; font-size: 12px; }
        .score strong { color: #1e3a8a; }
        .signatures { display: grid; width: min(680px, 100%); grid-template-columns: repeat(3, 1fr); gap: 30px; margin-top: auto; padding-top: 34px; }
        .signature { color: #475569; font-size: 12px; }
        .signature strong { display: block; min-height: 18px; margin-top: 40px; border-top: 1px solid #94a3b8; padding-top: 8px; color: #172554; font-size: 12px; }
        .signature small { display: block; margin-top: 4px; color: #64748b; font-size: 10px; }
        .number { position: absolute; right: 54px; bottom: 34px; color: #64748b; font-size: 10px; letter-spacing: .06em; }
        @media (max-width: 640px) {
            body { padding: 12px; }
            .sheet { min-height: auto; padding: 12px; }
            .frame { min-height: 620px; padding: 32px 18px; }
            h1 { font-size: 28px; }
            .student-name { font-size: 24px; }
            .signatures { gap: 12px; }
        }
        @media print {
            body { min-height: 0; padding: 0; background: #fff; print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .toolbar { display: none; }
            .sheet { width: 297mm; height: 210mm; max-width: none; min-height: 0; border: 0; padding: 9mm; box-shadow: none; }
            .frame { min-height: 0; height: 100%; padding: 12mm; }
        }
    </style>
</head>
<body>
    <nav class="toolbar" aria-label="Aksi sertifikat">
        <button class="print-button" type="button" onclick="window.print()">Cetak / Simpan PDF</button>
        <a class="back-button" href="{{ route('dashboard') }}">Kembali ke dashboard</a>
    </nav>

    <main class="sheet">
        <section class="frame" aria-label="Sertifikat praktik kerja lapangan">
            <div class="seal" aria-hidden="true">✦</div>
            <p class="school">{{ config('app.name') }}</p>
            <h1>SERTIFIKAT</h1>
            <p class="subtitle">Praktik Kerja Lapangan</p>
            <p class="intro">Diberikan dengan bangga kepada</p>
            <p class="student-name">{{ $siswa?->nama_lengkap ?? $pklSiswa->siswa?->name }}</p>
            <p class="student-meta">NISN {{ $siswa?->nisn ?: '—' }} <span aria-hidden="true">·</span> {{ $siswa?->jurusan ?: 'Peserta didik' }} <span aria-hidden="true">·</span> {{ $siswa?->kelas ?: '—' }}</p>
            <p class="description">
                Telah menyelesaikan kegiatan Praktik Kerja Lapangan di <strong>{{ $perusahaan ?: '—' }}</strong>
                pada tanggal {{ $pklSiswa->tanggal_mulai->translatedFormat('d F Y') }} sampai dengan {{ $pklSiswa->tanggal_selesai->translatedFormat('d F Y') }}.
                Semoga pengalaman dan kompetensi yang diperoleh menjadi bekal untuk berkarya.
            </p>
            @if ($pklSiswa->penilaian)
                <div class="score">
                    <span>Nilai akhir <strong>{{ $pklSiswa->penilaian->nilai_akhir ?? '—' }}</strong></span>
                    <span>Predikat <strong>{{ $pklSiswa->penilaian->predikat ?? '—' }}</strong></span>
                </div>
            @endif
            <div class="signatures">
                <div class="signature">Guru Pembimbing<strong>{{ $guruNama ?: ' ' }}</strong><small>{{ $pklSiswa->guru_nip ?: ' ' }}</small></div>
                <div class="signature">Pembimbing Mitra<strong>{{ $pembimbing ?: ' ' }}</strong><small>{{ $perusahaan ?: ' ' }}</small></div>
                <div class="signature">Pihak Sekolah<strong>Kepala Sekolah</strong><small>{{ config('app.name') }}</small></div>
            </div>
            <p class="number">Nomor {{ $sertifikat->nomor_sertifikat }} <span aria-hidden="true">·</span> Diterbitkan {{ $sertifikat->tanggal_terbit->translatedFormat('d F Y') }}</p>
        </section>
    </main>
</body>
</html>
