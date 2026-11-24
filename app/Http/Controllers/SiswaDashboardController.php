<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SiswaDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $placement = $user->placementsAsStudent()
            ->with(['guru.guruProfile', 'industri.industriProfile', 'penilaian', 'sertifikat'])
            ->latest()
            ->first();

        $statusFilter = $request->query('status');
        $allowedFilters = ['semua', 'belum', 'disetujui', 'ditolak'];
        if (! in_array($statusFilter, $allowedFilters, true)) {
            $statusFilter = 'semua';
        }

        $logbooks = collect();
        $todayLogbook = null;
        $editingLogbook = null;
        $stats = [
            'total' => 0,
            'belum' => 0,
            'disetujui' => 0,
            'ditolak' => 0,
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alfa' => 0,
        ];
        $progress = [
            'percent' => null,
            'days_total' => null,
            'days_elapsed' => null,
            'days_left' => null,
        ];

        if ($placement) {
            $baseQuery = $placement->logbooks();

            $stats['total'] = (clone $baseQuery)->count();
            $stats['belum'] = (clone $baseQuery)->where('verifikasi_status', 'belum')->count();
            $stats['disetujui'] = (clone $baseQuery)->where('verifikasi_status', 'disetujui')->count();
            $stats['ditolak'] = (clone $baseQuery)->where('verifikasi_status', 'ditolak')->count();
            $stats['hadir'] = (clone $baseQuery)->where('status_kehadiran', 'hadir')->count();
            $stats['sakit'] = (clone $baseQuery)->where('status_kehadiran', 'sakit')->count();
            $stats['izin'] = (clone $baseQuery)->where('status_kehadiran', 'izin')->count();
            $stats['alfa'] = (clone $baseQuery)->where('status_kehadiran', 'alfa')->count();

            $historyQuery = $placement->logbooks()->latest('tanggal')->latest('id');
            if ($statusFilter !== 'semua') {
                $historyQuery->where('verifikasi_status', $statusFilter);
            }
            $logbooks = $historyQuery->paginate(10)->withQueryString();

            $todayLogbook = $placement->logbooks()->whereDate('tanggal', today())->first();

            $editingLogbook = $request->query('edit_logbook')
                ? $placement->logbooks()
                    ->whereKey($request->integer('edit_logbook'))
                    ->where('verifikasi_status', 'ditolak')
                    ->first()
                : null;

            if ($placement->tanggal_mulai && $placement->tanggal_selesai) {
                $start = $placement->tanggal_mulai->startOfDay();
                $end = $placement->tanggal_selesai->startOfDay();
                $today = today()->startOfDay();

                $daysTotal = max(1, $start->diffInDays($end) + 1);
                $daysElapsed = $today->lt($start) ? 0 : min($daysTotal, $start->diffInDays($today) + 1);
                $daysLeft = $today->gt($end) ? 0 : max(0, $today->diffInDays($end));
                $percent = (int) round(($daysElapsed / $daysTotal) * 100);
                $percent = max(0, min(100, $percent));

                $progress = [
                    'percent' => $percent,
                    'days_total' => $daysTotal,
                    'days_elapsed' => $daysElapsed,
                    'days_left' => $daysLeft,
                ];
            }
        }

        return view('siswa.dashboard', compact(
            'placement',
            'logbooks',
            'todayLogbook',
            'editingLogbook',
            'stats',
            'progress',
            'statusFilter'
        ));
    }

    public function logbook(Request $request): View
    {
        $user = $request->user();
        $placement = $user->placementsAsStudent()
            ->with(['guru.guruProfile', 'industri.industriProfile'])
            ->latest()
            ->first();

        $request->validate([
            'tanggal' => ['sometimes', 'date_format:Y-m-d'],
            'bulan' => ['sometimes', 'integer', 'between:1,12'],
            'tahun' => ['sometimes', 'integer', 'between:2020,2100'],
        ]);

        $requestedDate = $request->query('tanggal');
        $viewMonth = $request->integer('bulan', $requestedDate ? (int) substr($requestedDate, 5, 2) : (int) now()->format('n'));
        $viewYear = $request->integer('tahun', $requestedDate ? (int) substr($requestedDate, 0, 4) : (int) now()->format('Y'));
        $viewMonth = max(1, min(12, $viewMonth));
        $viewYear = max(2020, min(2100, $viewYear));
        $defaultDate = $viewYear === (int) now()->format('Y') && $viewMonth === (int) now()->format('n')
            ? today()->toDateString()
            : now()->setDate($viewYear, $viewMonth, 1)->toDateString();

        $logbooks = collect();
        if ($placement) {
            $logbooks = $placement->logbooks()
                ->whereYear('tanggal', $viewYear)
                ->whereMonth('tanggal', $viewMonth)
                ->orderBy('tanggal')
                ->get()
                ->keyBy(fn ($logbook) => $logbook->tanggal->toDateString());
        }

        $selectedDate = $request->query('tanggal', $defaultDate);

        return view('siswa.logbook', compact(
            'placement',
            'logbooks',
            'selectedDate',
            'viewMonth',
            'viewYear'
        ));
    }

    public function history(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['sometimes', Rule::in(['semua', 'belum', 'disetujui', 'ditolak'])],
            'q' => ['nullable', 'string', 'max:100'],
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
        ]);

        $placement = $request->user()->placementsAsStudent()
            ->with(['guru.guruProfile', 'industri.industriProfile'])
            ->latest()
            ->first();

        $logbooks = $placement?->logbooks()->latest('tanggal')->latest('id');

        if ($logbooks && ($filters['status'] ?? 'semua') !== 'semua') {
            $logbooks->where('verifikasi_status', $filters['status']);
        }

        if ($logbooks && filled($filters['q'] ?? null)) {
            $search = $filters['q'];
            $logbooks->where(fn ($query) => $query
                ->where('kegiatan', 'like', "%{$search}%")
                ->orWhere('keterangan', 'like', "%{$search}%"));
        }

        if ($logbooks && filled($filters['dari'] ?? null)) {
            $logbooks->whereDate('tanggal', '>=', $filters['dari']);
        }

        if ($logbooks && filled($filters['sampai'] ?? null)) {
            $logbooks->whereDate('tanggal', '<=', $filters['sampai']);
        }

        $logbooks = $logbooks?->paginate(10)->withQueryString() ?? collect();

        return view('siswa.history', compact('placement', 'logbooks', 'filters'));
    }

    public function profile(Request $request): View
    {
        $user = $request->user()->load('siswaProfile');
        $placement = $user->placementsAsStudent()
            ->with(['guru.guruProfile', 'industri.industriProfile'])
            ->latest()
            ->first();

        return view('siswa.profile', compact('user', 'placement'));
    }

    public function storeLogbook(Request $request): RedirectResponse
    {
        $placement = $request->user()->placementsAsStudent()
            ->where('status', 'aktif')
            ->latest()
            ->first();

        if ($placement === null) {
            return redirect()->route('siswa.dashboard')
                ->with('error', 'Akun ini belum memiliki penempatan PKL aktif. Hubungi admin sekolah.');
        }

        $validated = $request->validate([
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'kegiatan' => ['required', 'string', 'max:5000'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
            'status_kehadiran' => ['required', 'in:hadir,sakit,izin,alfa'],
            'jam_masuk' => ['nullable', 'date_format:H:i'],
            'jam_keluar' => ['nullable', 'date_format:H:i', 'after_or_equal:jam_masuk'],
            'foto_bukti' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($placement->logbooks()->whereDate('tanggal', $validated['tanggal'])->exists()) {
            throw ValidationException::withMessages([
                'tanggal' => 'Logbook untuk tanggal tersebut sudah dikirim. Jika ditolak, perbaiki laporan yang sama di riwayat.',
            ]);
        }

        $data = $validated;
        if ($request->hasFile('foto_bukti')) {
            $data['foto_bukti'] = $request->file('foto_bukti')->store('logbook-bukti', 'public');
        } else {
            unset($data['foto_bukti']);
        }

        $placement->logbooks()->create($data);

        return redirect()->route('siswa.dashboard')
            ->with('success', 'Logbook berhasil dikirim dan menunggu verifikasi guru.');
    }

    public function updateLogbook(Request $request, int $logbook): RedirectResponse
    {
        $record = $request->user()->placementsAsStudent()
            ->where('status', 'aktif')
            ->whereHas('logbooks', fn ($query) => $query
                ->whereKey($logbook)
                ->where('verifikasi_status', 'ditolak'))
            ->firstOrFail()
            ->logbooks()
            ->whereKey($logbook)
            ->firstOrFail();

        $validated = $request->validate([
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'kegiatan' => ['required', 'string', 'max:5000'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
            'status_kehadiran' => ['required', 'in:hadir,sakit,izin,alfa'],
            'jam_masuk' => ['nullable', 'date_format:H:i'],
            'jam_keluar' => ['nullable', 'date_format:H:i', 'after_or_equal:jam_masuk'],
            'foto_bukti' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($record->pklSiswa->logbooks()
            ->whereDate('tanggal', $validated['tanggal'])
            ->where('id', '!=', $record->id)
            ->exists()) {
            throw ValidationException::withMessages([
                'tanggal' => 'Sudah ada logbook lain pada tanggal tersebut.',
            ]);
        }

        $data = [
            ...$validated,
            'verifikasi_status' => 'belum',
            'catatan_guru' => null,
            'verified_by' => null,
            'verified_at' => null,
        ];

        if ($request->hasFile('foto_bukti')) {
            if ($record->foto_bukti) {
                Storage::disk('public')->delete($record->foto_bukti);
            }
            $data['foto_bukti'] = $request->file('foto_bukti')->store('logbook-bukti', 'public');
        } else {
            unset($data['foto_bukti']);
        }

        $record->update($data);

        return redirect()->route('siswa.dashboard')
            ->with('success', 'Perbaikan logbook berhasil dikirim ulang untuk diperiksa guru.');
    }

    public function destroyLogbook(Request $request, int $logbook): RedirectResponse
    {
        $record = $request->user()->placementsAsStudent()
            ->where('status', 'aktif')
            ->whereHas('logbooks', fn ($query) => $query
                ->whereKey($logbook)
                ->whereIn('verifikasi_status', ['belum', 'ditolak']))
            ->firstOrFail()
            ->logbooks()
            ->whereKey($logbook)
            ->firstOrFail();

        $photoPath = $record->foto_bukti;
        $record->delete();
        if ($photoPath) {
            Storage::disk('public')->delete($photoPath);
        }

        return redirect()->route('siswa.history')->with('success', 'Logbook berhasil dihapus.');
    }
}
