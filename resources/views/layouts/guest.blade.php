<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#ffffff">

        <title>{{ config('app.name', 'Monitoring PKL') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif !important; }
        </style>
    </head>
    <body class="antialiased text-slate-900 bg-white">

        <div class="grid min-h-screen lg:grid-cols-[1.1fr_0.9fr]">

            {{-- ── LEFT PANEL: Branding (dark blue) ── --}}
            <aside class="relative hidden overflow-hidden bg-gradient-to-br from-slate-900 via-blue-950 to-blue-900 text-white lg:flex lg:flex-col lg:justify-between lg:px-14 lg:py-12 xl:px-20">
                {{-- Decorative blobs --}}
                <div class="absolute -left-24 -top-16 h-72 w-72 rounded-full bg-blue-600/20 blur-3xl pointer-events-none"></div>
                <div class="absolute bottom-0 right-0 h-80 w-80 rounded-full bg-blue-500/15 blur-3xl pointer-events-none"></div>
                <div class="absolute left-1/2 top-1/2 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full bg-blue-800/20 blur-3xl pointer-events-none"></div>

                {{-- Logo --}}
                <a href="{{ url('/') }}" class="relative inline-flex w-fit items-center gap-3 group" aria-label="Monitoring PKL beranda">
                    <span class="grid h-11 w-11 place-items-center rounded-2xl bg-blue-500 text-white shadow-lg shadow-blue-950/40 group-hover:bg-blue-400 transition-colors">
                        <x-application-logo class="h-6 w-6" />
                    </span>
                    <span>
                        <span class="block text-base font-bold tracking-tight">Monitoring PKL</span>
                        <span class="block text-xs text-blue-400">Praktik Kerja Lapangan</span>
                    </span>
                </a>

                {{-- Hero copy --}}
                <div class="relative max-w-sm py-10">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-semibold text-blue-200 backdrop-blur-sm">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-400"></span>
                        PORTAL PKL TERPADU
                    </span>

                    <h2 class="mt-7 text-3xl font-bold leading-tight tracking-tight xl:text-4xl">
                        Langkah awal menuju pengalaman kerja yang lebih terarah.
                    </h2>
                    <p class="mt-4 text-sm leading-7 text-blue-300">
                        Catat kegiatan, pantau bimbingan, dan ikuti perkembangan Praktik Kerja Lapangan dalam satu platform.
                    </p>

                    {{-- Mini dashboard card --}}
                    <div class="mt-10 rounded-2xl border border-white/10 bg-white/[0.06] p-5 shadow-2xl shadow-blue-950/30 backdrop-blur-sm">
                        <div class="flex items-center justify-between gap-4 mb-5">
                            <div>
                                <p class="text-sm font-semibold">Ringkasan PKL</p>
                                <p class="mt-0.5 text-xs text-blue-400">Real-time · Terhubung</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-green-400/15 px-3 py-1 text-xs font-semibold text-green-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-green-400 animate-pulse"></span>
                                AKTIF
                            </span>
                        </div>

                        <div class="grid grid-cols-3 gap-2.5">
                            @foreach([
                                ['icon' => 'M8 3v3m8-3v3M4.5 9h15M6 5h12a1.5 1.5 0 0 1 1.5 1.5v12A1.5 1.5 0 0 1 18 20H6a1.5 1.5 0 0 1-1.5-1.5v-12A1.5 1.5 0 0 1 6 5Z', 'label' => 'Logbook Harian'],
                                ['icon' => 'M12 3 19 6v5c0 4.5-3 7.5-7 10-4-2.5-7-5.5-7-10V6l7-3Zm0 0v0m-3 9l2 2 4-4', 'label' => 'Bimbingan'],
                                ['icon' => 'm12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z', 'label' => 'Evaluasi'],
                            ] as $item)
                                <div class="rounded-xl bg-white/[0.06] p-3">
                                    <span class="grid h-8 w-8 place-items-center rounded-xl bg-blue-400/15 text-blue-200">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                                        </svg>
                                    </span>
                                    <p class="mt-2.5 text-xs font-medium text-blue-200">{{ $item['label'] }}</p>
                                </div>
                            @endforeach
                        </div>

                        {{-- Stats row --}}
                        <div class="mt-4 grid grid-cols-3 gap-2.5 border-t border-white/10 pt-4">
                            @foreach([['24', 'Siswa'], ['96%', 'Hadir'], ['138', 'Logbook']] as [$num, $lbl])
                                <div class="text-center">
                                    <p class="text-lg font-bold text-white">{{ $num }}</p>
                                    <p class="text-xs text-blue-400">{{ $lbl }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <p class="relative text-xs text-blue-500/70">&copy; {{ date('Y') }} Monitoring PKL &middot; Belajar, bertumbuh, dan berkarya.</p>
            </aside>

            {{-- ── RIGHT PANEL: Form ── --}}
            <main class="flex min-h-screen flex-col bg-white px-5 py-8 sm:px-8 lg:items-center lg:justify-center lg:px-12">
                {{-- Mobile logo --}}
                <a href="{{ url('/') }}" class="mb-10 inline-flex items-center gap-3 lg:hidden" aria-label="Monitoring PKL beranda">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-600 text-white shadow-lg shadow-blue-200">
                        <x-application-logo class="h-6 w-6" />
                    </span>
                    <span class="font-bold tracking-tight text-slate-900">Monitoring PKL</span>
                </a>

                <section class="mx-auto w-full max-w-sm">
                    {{ $slot }}
                    <p class="mt-8 text-center text-xs text-slate-400">&copy; {{ date('Y') }} Monitoring PKL &middot; Semua aktivitas PKL dalam satu portal.</p>
                </section>
            </main>

        </div>
    </body>
</html>
