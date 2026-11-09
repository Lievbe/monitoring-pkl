<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffffff">
    <title>Monitoring PKL - Praktik Kerja Lapangan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        /* ── Reset & Base ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --blue:       #2563eb;
            --blue-dark:  #1d4ed8;
            --blue-light: #eff6ff;
            --blue-mid:   #bfdbfe;
            --text:       #0f172a;
            --muted:      #64748b;
            --border:     #e2e8f0;
            --white:      #ffffff;
            --bg:         #f8fafc;
        }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            background: var(--white);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }
        a { color: inherit; text-decoration: none; }

        /* ── Layout ── */
        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }

        /* ── Navbar ── */
        .navbar {
            position: sticky; top: 0; z-index: 100;
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            border-bottom: 1px solid rgba(226,232,240,0.7);
            height: 72px; display: flex; align-items: center;
        }
        .navbar-inner { display: flex; align-items: center; justify-content: space-between; width: 100%; }
        .brand { display: inline-flex; align-items: center; gap: 10px; font-size: 16px; font-weight: 800; letter-spacing: -0.03em; color: var(--text); }
        .brand-icon {
            width: 38px; height: 38px; border-radius: 10px;
            background: var(--blue); color: #fff;
            display: grid; place-items: center;
            box-shadow: 0 4px 12px rgba(37,99,235,0.35);
        }
        .brand-icon svg { width: 20px; height: 20px; }
        .nav-links { display: flex; align-items: center; gap: 8px; }
        .nav-link {
            padding: 8px 16px; border-radius: 8px;
            font-size: 14px; font-weight: 500; color: var(--muted);
            transition: color .15s, background .15s;
        }
        .nav-link:hover { color: var(--blue); background: var(--blue-light); }
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 10px 20px; border-radius: 10px; font-size: 14px; font-weight: 600;
            transition: transform .18s, box-shadow .18s, background .18s;
            cursor: pointer; border: none;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-primary {
            background: var(--blue); color: #fff;
            box-shadow: 0 4px 14px rgba(37,99,235,0.35);
        }
        .btn-primary:hover {
            background: var(--blue-dark);
            box-shadow: 0 6px 20px rgba(37,99,235,0.45);
        }
        .btn-ghost { background: transparent; color: var(--muted); border: 1px solid var(--border); }
        .btn-ghost:hover { color: var(--blue); background: var(--blue-light); border-color: var(--blue-mid); }
        .btn-lg { padding: 14px 28px; font-size: 15px; border-radius: 12px; }

        /* ── Hero Section ── */
        .hero {
            min-height: calc(100vh - 72px);
            padding: 80px 0 100px;
            position: relative; overflow: hidden;
        }
        .hero-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            align-items: center; gap: 64px;
        }
        .hero::before {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(ellipse 70% 60% at 65% 50%, #eff6ff 0%, transparent 70%);
            pointer-events: none;
        }
        /* Decorative blobs */
        .blob {
            position: absolute; border-radius: 50%;
            filter: blur(60px); pointer-events: none; opacity: 0.45;
        }
        .blob-1 { width: 400px; height: 400px; background: #bfdbfe; top: -100px; right: -80px; }
        .blob-2 { width: 300px; height: 300px; background: #c7d2fe; bottom: -60px; right: 200px; }

        .hero-content { position: relative; z-index: 1; }
        .badge {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 6px 14px; border-radius: 999px;
            background: var(--blue-light); border: 1px solid var(--blue-mid);
            font-size: 11px; font-weight: 700; letter-spacing: .1em; color: var(--blue);
            margin-bottom: 24px;
        }
        .badge-dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,0.2);
        }
        .hero h1 {
            font-size: clamp(38px, 5vw, 60px);
            font-weight: 900; line-height: 1.07; letter-spacing: -0.055em;
            color: var(--text); margin-bottom: 20px;
        }
        .hero h1 .accent { color: var(--blue); }
        .hero-desc {
            font-size: 16px; line-height: 1.8; color: var(--muted);
            max-width: 480px; margin-bottom: 36px;
        }
        .hero-cta { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; margin-bottom: 40px; }
        .hero-note { font-size: 12px; color: #94a3b8; display: flex; align-items: center; gap: 8px; }
        .hero-note svg { flex-shrink: 0; }

        /* ── Floating Cards ── */
        .hero-visual { position: relative; z-index: 1; display: flex; justify-content: center; align-items: center; }
        .cards-scene {
            position: relative; width: 100%; max-width: 440px;
            padding: 20px 10px 20px 20px;
        }

        /* Keyframe Animations */
        @keyframes floatA {
            0%, 100% { transform: translateY(0px) rotate(2deg); }
            50%       { transform: translateY(-16px) rotate(2deg); }
        }
        @keyframes floatB {
            0%, 100% { transform: translateY(0px) rotate(-1.5deg); }
            50%       { transform: translateY(-20px) rotate(-1.5deg); }
        }
        @keyframes floatC {
            0%, 100% { transform: translateY(0px) rotate(1deg); }
            50%       { transform: translateY(-12px) rotate(1deg); }
        }
        @keyframes shimmer {
            0%   { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.6; transform: scale(1.3); }
        }
        @keyframes count-up {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Card Base ── */
        .float-card {
            border-radius: 16px; background: #fff;
            border: 1px solid var(--border);
            box-shadow: 0 20px 60px rgba(15,23,42,0.08), 0 4px 16px rgba(15,23,42,0.05);
        }

        /* Main Dashboard Card */
        .card-main {
            padding: 20px;
            animation: floatA 5.5s ease-in-out infinite;
        }
        .card-main-header {
            display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;
        }
        .card-label { font-size: 12px; font-weight: 700; color: var(--text); }
        .card-sublabel { font-size: 10px; color: var(--muted); margin-top: 2px; }
        .status-pill {
            padding: 4px 10px; border-radius: 999px;
            font-size: 10px; font-weight: 700;
            background: #dcfce7; color: #16a34a;
        }

        /* Stats Row */
        .stats-row { display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; margin-bottom: 14px; }
        .stat-box { padding: 12px 10px; border-radius: 10px; background: var(--bg); border: 1px solid var(--border); text-align: center; }
        .stat-num { font-size: 20px; font-weight: 800; letter-spacing: -0.04em; color: var(--text); }
        .stat-name { font-size: 9px; color: var(--muted); margin-top: 3px; font-weight: 600; text-transform: uppercase; letter-spacing: .07em; }

        /* Attendance Bar Chart */
        .chart-section { margin-bottom: 14px; }
        .chart-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
        .chart-title { font-size: 11px; font-weight: 700; color: var(--text); }
        .chart-pct { font-size: 11px; font-weight: 800; color: var(--blue); }
        .chart-bars { display: flex; align-items: flex-end; gap: 5px; height: 52px; }
        .bar-col { display: flex; flex-direction: column; align-items: center; gap: 3px; flex: 1; }
        .bar-fill { width: 100%; border-radius: 4px 4px 0 0; background: var(--blue); transition: height .3s; }
        .bar-fill.low { background: #bfdbfe; }
        .bar-day { font-size: 8px; color: var(--muted); font-weight: 600; }

        /* Progress Bar */
        .progress-row { margin-bottom: 14px; }
        .progress-label { display: flex; justify-content: space-between; font-size: 10px; font-weight: 600; color: var(--muted); margin-bottom: 6px; }
        .progress-label span:last-child { color: var(--blue); font-weight: 700; }
        .progress-track { height: 6px; border-radius: 999px; background: var(--border); overflow: hidden; }
        .progress-fill { height: 100%; border-radius: inherit; background: linear-gradient(90deg, var(--blue), #60a5fa); width: 72%; }

        /* Activity List */
        .activity-list { display: flex; flex-direction: column; gap: 8px; }
        .activity-item { display: flex; align-items: center; gap: 10px; padding: 8px 10px; border-radius: 10px; background: var(--bg); border: 1px solid var(--border); }
        .act-icon { width: 28px; height: 28px; border-radius: 8px; display: grid; place-items: center; flex-shrink: 0; font-size: 12px; }
        .act-icon.blue  { background: var(--blue-light); color: var(--blue); }
        .act-icon.green { background: #dcfce7; color: #16a34a; }
        .act-icon.amber { background: #fef9c3; color: #ca8a04; }
        .act-body { flex: 1; min-width: 0; }
        .act-title { font-size: 10px; font-weight: 700; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .act-sub   { font-size: 9px; color: var(--muted); margin-top: 2px; }
        .act-time  { font-size: 9px; font-weight: 600; color: var(--muted); flex-shrink: 0; }

        /* ── Floating Mini Cards ── */
        .card-mini-1 {
            position: absolute; bottom: -20px; left: -40px;
            width: 180px; padding: 14px 16px;
            animation: floatB 4.2s ease-in-out infinite;
            box-shadow: 0 16px 40px rgba(37,99,235,0.12), 0 4px 12px rgba(15,23,42,0.06);
        }
        .card-mini-2 {
            position: absolute; top: 20px; right: -30px;
            width: 160px; padding: 12px 14px;
            animation: floatC 6s ease-in-out infinite;
            box-shadow: 0 16px 40px rgba(15,23,42,0.08), 0 4px 12px rgba(15,23,42,0.05);
        }
        .mini-label { font-size: 9px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 6px; }
        .mini-value { font-size: 22px; font-weight: 900; letter-spacing: -0.04em; color: var(--text); }
        .mini-sub   { font-size: 9px; color: var(--muted); margin-top: 2px; }
        .mini-trend {
            display: inline-flex; align-items: center; gap: 4px;
            margin-top: 8px; padding: 3px 8px; border-radius: 999px;
            font-size: 9px; font-weight: 700;
            background: #dcfce7; color: #16a34a;
        }

        /* Live dot */
        .live-dot {
            display: inline-block; width: 7px; height: 7px; border-radius: 50%;
            background: #22c55e; margin-right: 5px;
            animation: pulse-dot 2s ease-in-out infinite;
        }

        /* ── Gradient Shimmer on chart bars ── */
        .bar-fill.active {
            background: linear-gradient(135deg, #2563eb, #60a5fa);
        }

        /* ── Features Section ── */
        .features-section { padding: 96px 0; background: var(--bg); border-top: 1px solid var(--border); }
        .section-tag {
            display: inline-block; padding: 4px 14px; border-radius: 999px;
            font-size: 11px; font-weight: 700; letter-spacing: .1em;
            background: var(--blue-light); color: var(--blue);
            border: 1px solid var(--blue-mid); margin-bottom: 16px;
        }
        .section-title { font-size: clamp(26px, 3vw, 36px); font-weight: 800; letter-spacing: -0.04em; margin-bottom: 12px; }
        .section-desc { font-size: 15px; line-height: 1.7; color: var(--muted); max-width: 500px; margin: 0 auto; }
        .section-header { text-align: center; margin-bottom: 56px; }

        .features-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; }
        .feature-card {
            padding: 28px 24px; border-radius: 16px;
            background: var(--white); border: 1px solid var(--border);
            transition: transform .2s, box-shadow .2s;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 50px rgba(15,23,42,0.08);
        }
        .feat-icon {
            width: 44px; height: 44px; border-radius: 12px;
            background: var(--blue-light); display: grid; place-items: center;
            margin-bottom: 18px; font-size: 18px;
        }
        .feat-title { font-size: 15px; font-weight: 700; margin-bottom: 8px; }
        .feat-desc  { font-size: 13px; line-height: 1.65; color: var(--muted); }

        /* ── Stats Section ── */
        .stats-section { padding: 72px 0; background: var(--white); border-top: 1px solid var(--border); }
        .stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 24px; text-align: center; }
        .stat-item {}
        .stat-big { font-size: 42px; font-weight: 900; letter-spacing: -0.05em; color: var(--blue); }
        .stat-info { font-size: 13px; color: var(--muted); margin-top: 4px; font-weight: 500; }

        /* ── CTA Section ── */
        .cta-section {
            padding: 96px 0;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);
            text-align: center;
        }
        .cta-section h2 { font-size: clamp(28px, 3vw, 42px); font-weight: 900; color: #fff; letter-spacing: -0.04em; margin-bottom: 16px; }
        .cta-section p  { font-size: 15px; color: rgba(255,255,255,0.75); max-width: 480px; margin: 0 auto 36px; line-height: 1.7; }
        .btn-white {
            background: #fff; color: var(--blue);
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            font-weight: 700;
        }
        .btn-white:hover { background: #f0f9ff; box-shadow: 0 12px 30px rgba(0,0,0,0.2); }
        .cta-note { margin-top: 20px; font-size: 12px; color: rgba(255,255,255,0.55); }

        /* ── Footer ── */
        .footer { padding: 32px 0; background: var(--bg); border-top: 1px solid var(--border); }
        .footer-inner { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .footer-brand { display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 700; color: var(--text); }
        .footer-brand-icon { width: 28px; height: 28px; border-radius: 8px; background: var(--blue); display: grid; place-items: center; }
        .footer-brand-icon svg { width: 14px; height: 14px; color: #fff; }
        .footer-copy { font-size: 12px; color: var(--muted); }
        .footer-links { display: flex; gap: 20px; }
        .footer-links a { font-size: 12px; color: var(--muted); transition: color .15s; }
        .footer-links a:hover { color: var(--blue); }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .features-grid { grid-template-columns: repeat(2,1fr); }
            .stats-grid { grid-template-columns: repeat(2,1fr); gap: 32px; }
        }
        @media (max-width: 768px) {
            .hero { padding: 56px 0 72px; }
            .hero .container { grid-template-columns: 1fr !important; gap: 48px !important; }
            .hero-visual { order: -1; }
            .cards-scene { max-width: 380px; margin: 0 auto; }
            .card-mini-1 { left: -10px; }
            .card-mini-2 { right: -10px; }
            .hero h1 { font-size: clamp(32px, 8vw, 48px); }
        }
        @media (max-width: 600px) {
            .nav-links .btn-ghost { display: none; }
            .features-grid { grid-template-columns: 1fr; gap: 12px; }
            .stats-grid { grid-template-columns: repeat(2,1fr); }
            .footer-inner { flex-direction: column; align-items: flex-start; gap: 12px; }
            .footer-links { flex-wrap: wrap; gap: 12px; }
            .card-mini-1, .card-mini-2 { display: none; }
            .container { padding: 0 16px; }
        }
    </style>
</head>
<body>

    <!-- ═══════════════════════════════ NAVBAR ═══════════════════════════════ -->
    <nav class="navbar">
        <div class="container navbar-inner">
            <a href="{{ route('home') }}" class="brand" aria-label="Monitoring PKL beranda">
                <div class="brand-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                </div>
                <span>Monitoring PKL</span>
            </a>

            <div class="nav-links">
                <a href="#fitur" class="nav-link">Fitur</a>
                <a href="#statistik" class="nav-link">Statistik</a>
                <a href="{{ route('login') }}" class="btn btn-ghost">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/>
                    </svg>
                    Login Sistem
                </a>
                <a href="{{ route('register') }}" class="btn btn-primary">
                    Daftar Gratis
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </nav>

    <main>
        <!-- ═══════════════════════════════ HERO ═══════════════════════════════ -->
        <section class="hero">
            <div class="blob blob-1" aria-hidden="true"></div>
            <div class="blob blob-2" aria-hidden="true"></div>

            <div class="container hero-grid">

                <!-- Left: Copy -->
                <div class="hero-content">
                    <div class="badge">
                        <span class="badge-dot" aria-hidden="true"></span>
                        MONITORING PRAKTIK KERJA LAPANGAN
                    </div>

                    <h1>
                        PKL lebih terarah.<br>
                        <span class="accent">Progres mudah</span><br>
                        dipantau.
                    </h1>

                    <p class="hero-desc">
                        Satu platform terpadu untuk siswa, guru pembimbing, dan mitra industri — catat aktivitas harian, pantau kehadiran, dan evaluasi PKL dengan lebih efisien.
                    </p>

                    <div class="hero-cta">
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                            Mulai Sekarang — Gratis
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-ghost btn-lg">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/>
                            </svg>
                            Login Sistem
                        </a>
                    </div>

                    <div class="hero-note">
                        <svg width="14" height="14" fill="none" stroke="#22c55e" stroke-width="2.5" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/>
                        </svg>
                        Portal khusus untuk siswa, guru pembimbing &amp; pembimbing industri
                    </div>
                </div>

                <!-- Right: Floating Cards Visual -->
                <div class="hero-visual">
                    <div class="cards-scene">

                        <!-- Main Dashboard Card -->
                        <div class="float-card card-main">
                            <div class="card-main-header">
                                <div>
                                    <div class="card-label">Dashboard Monitoring PKL</div>
                                    <div class="card-sublabel">
                                        <span class="live-dot" aria-hidden="true"></span>Live — Data real-time
                                    </div>
                                </div>
                                <span class="status-pill">✓ Aktif</span>
                            </div>

                            <!-- Stats -->
                            <div class="stats-row">
                                <div class="stat-box">
                                    <div class="stat-num">24</div>
                                    <div class="stat-name">Siswa PKL</div>
                                </div>
                                <div class="stat-box">
                                    <div class="stat-num">96%</div>
                                    <div class="stat-name">Kehadiran</div>
                                </div>
                                <div class="stat-box">
                                    <div class="stat-num">138</div>
                                    <div class="stat-name">Logbook</div>
                                </div>
                            </div>

                            <!-- Attendance Bar Chart -->
                            <div class="chart-section">
                                <div class="chart-header">
                                    <span class="chart-title">Grafik Kehadiran Minggu Ini</span>
                                    <span class="chart-pct">↑ 4.2%</span>
                                </div>
                                <div class="chart-bars" aria-label="Grafik kehadiran harian">
                                    <div class="bar-col">
                                        <div class="bar-fill active" style="height:40px"></div>
                                        <span class="bar-day">Sen</span>
                                    </div>
                                    <div class="bar-col">
                                        <div class="bar-fill active" style="height:48px"></div>
                                        <span class="bar-day">Sel</span>
                                    </div>
                                    <div class="bar-col">
                                        <div class="bar-fill low" style="height:30px"></div>
                                        <span class="bar-day">Rab</span>
                                    </div>
                                    <div class="bar-col">
                                        <div class="bar-fill active" style="height:44px"></div>
                                        <span class="bar-day">Kam</span>
                                    </div>
                                    <div class="bar-col">
                                        <div class="bar-fill active" style="height:50px"></div>
                                        <span class="bar-day">Jum</span>
                                    </div>
                                    <div class="bar-col">
                                        <div class="bar-fill low" style="height:20px"></div>
                                        <span class="bar-day">Sab</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress -->
                            <div class="progress-row">
                                <div class="progress-label">
                                    <span>Progress PKL — Semester Ganjil</span>
                                    <span>72%</span>
                                </div>
                                <div class="progress-track"><div class="progress-fill"></div></div>
                            </div>

                            <!-- Activity -->
                            <div class="activity-list">
                                <div class="activity-item">
                                    <div class="act-icon blue">📝</div>
                                    <div class="act-body">
                                        <div class="act-title">Ahmad Fauzi mengisi logbook harian</div>
                                        <div class="act-sub">Siswa PKL — PT. Maju Bersama</div>
                                    </div>
                                    <span class="act-time">09:14</span>
                                </div>
                                <div class="activity-item">
                                    <div class="act-icon green">✓</div>
                                    <div class="act-body">
                                        <div class="act-title">Guru verifikasi logbook Siti Rahma</div>
                                        <div class="act-sub">Guru Pembimbing — Kelas XI RPL</div>
                                    </div>
                                    <span class="act-time">09:30</span>
                                </div>
                                <div class="activity-item">
                                    <div class="act-icon amber">⭐</div>
                                    <div class="act-body">
                                        <div class="act-title">Penilaian akhir diterima dari industri</div>
                                        <div class="act-sub">Pembimbing Industri — CV. Tekno</div>
                                    </div>
                                    <span class="act-time">10:05</span>
                                </div>
                            </div>
                        </div>

                        <!-- Floating Mini Card 1: Absensi -->
                        <div class="float-card card-mini-1" aria-hidden="true">
                            <div class="mini-label">Tingkat Kehadiran</div>
                            <div class="mini-value">96.4%</div>
                            <div class="mini-sub">22 dari 24 siswa hadir</div>
                            <div class="mini-trend">
                                <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path d="M18 15l-6-6-6 6"/>
                                </svg>
                                +2.1% dari minggu lalu
                            </div>
                        </div>

                        <!-- Floating Mini Card 2: Jurnal -->
                        <div class="float-card card-mini-2" aria-hidden="true">
                            <div class="mini-label">Jurnal Hari Ini</div>
                            <div class="mini-value">18</div>
                            <div class="mini-sub">Logbook tersubmit</div>
                            <div class="mini-trend">
                                <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path d="M18 15l-6-6-6 6"/>
                                </svg>
                                Semua terverifikasi
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </section>

        <!-- ═══════════════════════════════ FEATURES ═══════════════════════════════ -->
        <section class="features-section" id="fitur">
            <div class="container">
                <div class="section-header">
                    <div class="section-tag">FITUR LENGKAP</div>
                    <h2 class="section-title">Semua pihak tetap terhubung</h2>
                    <p class="section-desc">Platform monitoring PKL yang dirancang khusus untuk memudahkan kolaborasi antara siswa, guru, dan mitra industri.</p>
                </div>

                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feat-icon">👨‍🎓</div>
                        <div class="feat-title">Portal Siswa</div>
                        <p class="feat-desc">Catat kegiatan harian melalui logbook digital, laporkan kehadiran, dan pantau perkembangan PKL secara mandiri.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feat-icon">👨‍🏫</div>
                        <div class="feat-title">Dashboard Guru</div>
                        <p class="feat-desc">Pantau seluruh aktivitas siswa bimbingan, verifikasi logbook, dan lakukan evaluasi hasil PKL dengan mudah.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feat-icon">🏭</div>
                        <div class="feat-title">Akses Industri</div>
                        <p class="feat-desc">Pembimbing dari perusahaan dapat memantau perkembangan dan memberikan penilaian langsung di platform.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feat-icon">📊</div>
                        <div class="feat-title">Laporan & Sertifikat</div>
                        <p class="feat-desc">Generate laporan otomatis dan sertifikat PKL digital setelah siswa menyelesaikan program dengan berhasil.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════ STATS ═══════════════════════════════ -->
        <section class="stats-section" id="statistik">
            <div class="container">
                <div class="section-header" style="margin-bottom:48px">
                    <div class="section-tag">TERPERCAYA</div>
                    <h2 class="section-title">Platform yang telah terbukti</h2>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-big">500+</div>
                        <div class="stat-info">Siswa PKL terdaftar</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-big">96%</div>
                        <div class="stat-info">Rata-rata kehadiran</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-big">50+</div>
                        <div class="stat-info">Mitra industri aktif</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-big">3K+</div>
                        <div class="stat-info">Logbook terverifikasi</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════ CTA ═══════════════════════════════ -->
        <section class="cta-section">
            <div class="container">
                <h2>Siap memulai monitoring PKL?</h2>
                <p>Bergabunglah dan rasakan kemudahan memantau perjalanan PKL siswa dalam satu platform yang terintegrasi.</p>
                <div style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap;">
                    <a href="{{ route('register') }}" class="btn btn-white btn-lg">
                        Buat Akun Sekarang
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-lg" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/>
                        </svg>
                        Login Sistem
                    </a>
                </div>
                <p class="cta-note">Tidak perlu kartu kredit · Gratis selamanya untuk sekolah</p>
            </div>
        </section>

    </main>

    <!-- ═══════════════════════════════ FOOTER ═══════════════════════════════ -->
    <footer class="footer">
        <div class="container">
            <div class="footer-inner">
                <div class="footer-brand">
                    <div class="footer-brand-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>
                    Monitoring PKL
                </div>
                <span class="footer-copy">&copy; {{ date('Y') }} Monitoring PKL · Mendukung PKL yang lebih terpantau</span>
                <div class="footer-links">
                    <a href="#fitur">Fitur</a>
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ route('register') }}">Daftar</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
