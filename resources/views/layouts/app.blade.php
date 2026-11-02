<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIP-TPI Bengkulu') - Sistem Tata Kelola Data Aktivitas TPI</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons 1.11.3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary-navy: #0B3C5D;
            --dark-navy: #1D2731;
            --ocean-blue: #328CC1;
            --ocean-light: #D9B310;
            --ocean-cyan: #47b8e0;
            --bg-canvas: #f4f6fa;
            --sidebar-width: 270px;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-canvas);
            color: #2b2d42;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(180deg, #0B3C5D 0%, #102A43 60%, #1D2731 100%);
            color: #ecf0f1;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            transition: all 0.3s ease-in-out;
            box-shadow: 4px 0 16px rgba(11, 60, 93, 0.12);
            overflow-y: auto;
        }

        #sidebar .sidebar-brand {
            padding: 1.25rem 1.5rem;
            background: rgba(0, 0, 0, 0.18);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .brand-icon-wrap {
            width: 42px;
            height: 42px;
            background: rgba(50, 140, 193, 0.25);
            border: 1px solid rgba(50, 140, 193, 0.45);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #63b3ed;
            font-size: 1.35rem;
        }

        .brand-title {
            font-size: 1.08rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #ffffff;
            line-height: 1.2;
        }

        .brand-subtitle {
            font-size: 0.72rem;
            color: #90cdf4;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        .sidebar-section-title {
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 1.1px;
            text-transform: uppercase;
            color: #718096;
            padding: 1rem 1.5rem 0.4rem;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0.5rem 0.85rem;
            margin: 0;
        }

        .sidebar-nav .nav-item {
            margin-bottom: 0.25rem;
        }

        .sidebar-nav .nav-link {
            color: #cbd5e1;
            padding: 0.65rem 0.95rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            font-size: 0.88rem;
            font-weight: 500;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .sidebar-nav .nav-link i {
            font-size: 1.1rem;
            margin-right: 0.85rem;
            color: #94a3b8;
            width: 22px;
            text-align: center;
            transition: color 0.2s ease;
        }

        .sidebar-nav .nav-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
        }

        .sidebar-nav .nav-link:hover i {
            color: #63b3ed;
        }

        .sidebar-nav .nav-link.active {
            color: #ffffff;
            background: #328CC1;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(50, 140, 193, 0.35);
        }

        .sidebar-nav .nav-link.active i {
            color: #ffffff;
        }

        /* Top Navbar */
        #topNavbar {
            margin-left: var(--sidebar-width);
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1.75rem;
            position: sticky;
            top: 0;
            z-index: 1020;
            transition: margin-left 0.3s ease-in-out;
        }

        /* Main Content */
        #mainContent {
            margin-left: var(--sidebar-width);
            padding: 1.75rem;
            min-height: calc(100vh - 72px);
            transition: margin-left 0.3s ease-in-out;
        }

        /* Role Badges */
        .badge-role-admin {
            background-color: #2b6cb0;
            color: #fff;
        }
        .badge-role-verifikator {
            background-color: #0d9488;
            color: #fff;
        }
        .badge-role-pimpinan {
            background-color: #7c3aed;
            color: #fff;
        }

        /* Card stylings */
        .card-custom {
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            background: #ffffff;
        }

        /* Responsive Mobile Sidebar */
        @media (max-width: 991.98px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            #sidebar.show {
                margin-left: 0;
            }
            #topNavbar, #mainContent {
                margin-left: 0 !important;
            }
            .sidebar-backdrop {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0, 0, 0, 0.45);
                z-index: 1030;
            }
            .sidebar-backdrop.show {
                display: block;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    @php
        $currentUser = auth()->user();
        $sidebarPendingCount = $pendingVerificationCount ?? \App\Models\Pendaratan::where('status_verifikasi', 'diajukan')->count();
    @endphp

    <!-- Mobile Backdrop -->
    <div id="sidebarBackdrop" class="sidebar-backdrop" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <nav id="sidebar">
        <!-- Brand Header -->
        <div class="sidebar-brand d-flex align-items-center gap-3">
            <div class="brand-icon-wrap">
                <i class="bi bi-water"></i>
            </div>
            <div>
                <div class="brand-title">SIP-TPI BENGKULU</div>
                <div class="brand-subtitle">Tata Kelola Aktivitas TPI</div>
            </div>
        </div>

        <div class="py-2">
            <!-- Main Navigation -->
            <div class="sidebar-section-title">Menu Utama</div>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            </ul>

            <!-- Master Data Navigation -->
            @if(in_array($currentUser?->role, ['admin_tpi', 'verifikator', 'pimpinan']))
            <div class="sidebar-section-title">Master Data</div>
            <ul class="sidebar-nav">
                <!-- Data TPI -->
                <li class="nav-item">
                    <a href="{{ route('tpi.index') }}" class="nav-link {{ request()->routeIs('tpi.*') ? 'active' : '' }}">
                        <i class="bi bi-building"></i>
                        <span>Data TPI</span>
                    </a>
                </li>

                <!-- Data Nelayan -->
                <li class="nav-item">
                    <a href="{{ route('nelayan.index') }}" class="nav-link {{ request()->routeIs('nelayan.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Data Nelayan</span>
                    </a>
                </li>

                <!-- Data Kapal -->
                <li class="nav-item">
                    <a href="{{ route('kapal.index') }}" class="nav-link {{ request()->routeIs('kapal.*') ? 'active' : '' }}">
                        <i class="bi bi-tsunami"></i>
                        <span>Data Kapal</span>
                    </a>
                </li>

                <!-- Data Alat Tangkap -->
                <li class="nav-item">
                    <a href="{{ route('alat-tangkap.index') }}" class="nav-link {{ request()->routeIs('alat-tangkap.*') ? 'active' : '' }}">
                        <i class="bi bi-tools"></i>
                        <span>Data Alat Tangkap</span>
                    </a>
                </li>

                <!-- Data Jenis Ikan -->
                <li class="nav-item">
                    <a href="{{ route('jenis-ikan.index') }}" class="nav-link {{ request()->routeIs('jenis-ikan.*') ? 'active' : '' }}">
                        <i class="bi bi-fingerprint"></i>
                        <span>Data Jenis Ikan</span>
                    </a>
                </li>
            </ul>
            @endif

            <!-- Aktivitas & Transaksi -->
            <div class="sidebar-section-title">Aktivitas & Transaksi</div>
            <ul class="sidebar-nav">
                @if($currentUser?->role === 'admin_tpi')
                <!-- Input Pendaratan (Admin TPI) -->
                <li class="nav-item">
                    <a href="{{ route('pendaratan.create') }}" class="nav-link {{ request()->routeIs('pendaratan.create') ? 'active' : '' }}">
                        <i class="bi bi-plus-circle"></i>
                        <span>Input Pendaratan</span>
                    </a>
                </li>
                @endif

                <!-- Riwayat Pendaratan (Semua Role) -->
                <li class="nav-item">
                    <a href="{{ route('pendaratan.index') }}" class="nav-link {{ (request()->routeIs('pendaratan.*') && !request()->routeIs('pendaratan.create')) ? 'active' : '' }}">
                        <i class="bi bi-journal-text"></i>
                        <span>Riwayat Pendaratan</span>
                    </a>
                </li>

                @if($currentUser?->isVerifikator())
                <!-- Verifikasi Data (Verifikator only) -->
                <li class="nav-item">
                    <a href="{{ route('verifikasi.index') }}" class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('verifikasi.*') ? 'active' : '' }}">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check2-circle"></i>
                            <span>Verifikasi Data</span>
                        </div>
                        @if($sidebarPendingCount > 0)
                            <span class="badge bg-warning text-dark rounded-pill fw-bold" style="font-size: 0.72rem;">
                                {{ $sidebarPendingCount }}
                            </span>
                        @endif
                    </a>
                </li>
                @endif
            </ul>

            <!-- Laporan & Rekapitulasi -->
            @if(in_array($currentUser?->role, ['verifikator', 'pimpinan', 'admin_tpi']))
            <div class="sidebar-section-title">Laporan & Rekapitulasi</div>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a href="{{ route('laporan.index', ['tab' => 'tpi']) }}" class="nav-link {{ request()->routeIs('laporan.*') && (!request()->has('tab') || request('tab') === 'tpi') ? 'active' : '' }}">
                        <i class="bi bi-pie-chart"></i>
                        <span>Rekap per TPI</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('laporan.index', ['tab' => 'ikan']) }}" class="nav-link {{ request()->routeIs('laporan.*') && request('tab') === 'ikan' ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                        <span>Laporan Produksi</span>
                    </a>
                </li>
            </ul>
            @endif

            @if($currentUser?->isPimpinan())
            <div class="sidebar-section-title">Administrasi Sistem</div>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a href="{{ route('akun.index') }}" class="nav-link {{ request()->routeIs('akun.*') ? 'active' : '' }}">
                        <i class="bi bi-person-gear"></i>
                        <span>Manajemen Akun</span>
                    </a>
                </li>
            </ul>
            @endif
        </div>
    </nav>

    <!-- Top Navbar -->
    <header id="topNavbar" class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" onclick="toggleSidebar()" aria-label="Toggle Sidebar">
                <i class="bi bi-list fs-5"></i>
            </button>
            <div class="d-none d-sm-block">
                <div class="fw-semibold text-dark fs-6">Sistem Informasi Tata Kelola Data Aktivitas TPI</div>
                <div class="text-muted" style="font-size: 0.75rem;">Dinas Kelautan dan Perikanan Provinsi Bengkulu</div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <!-- User Dropdown -->
            @if($currentUser)
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 border py-1 px-2.5 rounded-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.85rem;">
                        {{ strtoupper(substr($currentUser->name, 0, 1)) }}
                    </div>
                    <div class="text-start d-none d-md-block">
                        <div class="fw-bold text-dark lh-1" style="font-size: 0.85rem;">{{ $currentUser->name }}</div>
                        <div class="text-muted" style="font-size: 0.72rem;">
                            @if($currentUser->role === 'admin_tpi')
                                <span class="badge badge-role-admin py-0 px-1 rounded">Admin TPI</span>
                                @if($currentUser->tpi)
                                    <span>• {{ $currentUser->tpi->nama_tpi }}</span>
                                @endif
                            @elseif($currentUser->role === 'verifikator')
                                <span class="badge badge-role-verifikator py-0 px-1 rounded">Verifikator</span>
                            @elseif($currentUser->role === 'pimpinan')
                                <span class="badge badge-role-pimpinan py-0 px-1 rounded">Pimpinan</span>
                            @endif
                        </div>
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" style="min-width: 240px;">
                    <li class="px-3 py-2 border-bottom">
                        <div class="fw-bold text-dark">{{ $currentUser->name }}</div>
                        <div class="text-muted small">{{ $currentUser->email }}</div>
                        <div class="mt-1">
                            @if($currentUser->role === 'admin_tpi')
                                <span class="badge badge-role-admin">Admin TPI</span>
                                @if($currentUser->tpi)
                                    <div class="small text-primary mt-1"><i class="bi bi-geo-alt"></i> {{ $currentUser->tpi->nama_tpi }}</div>
                                @endif
                            @elseif($currentUser->role === 'verifikator')
                                <span class="badge badge-role-verifikator">Verifikator DKP</span>
                            @elseif($currentUser->role === 'pimpinan')
                                <span class="badge badge-role-pimpinan">Pimpinan DKP</span>
                            @endif
                        </div>
                    </li>
                    <li class="pt-1">
                        <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2 py-2">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Keluar (Logout)</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            @endif
        </div>
    </header>

    <!-- Main Content Area -->
    <main id="mainContent">
        <!-- Flash Alerts Banner -->
        <div class="container-fluid p-0">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-circle-fill fs-5"></i>
                    <div>{{ session('warning') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2 fw-semibold mb-1">
                        <i class="bi bi-exclamation-octagon-fill"></i>
                        <span>Terjadi kesalahan validasi:</span>
                    </div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Bootstrap 5.3.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            sidebar.classList.toggle('show');
            backdrop.classList.toggle('show');
        }
    </script>
    @stack('scripts')
</body>
</html>
