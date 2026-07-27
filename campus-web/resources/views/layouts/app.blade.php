<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Campus Care')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --main-red: #cc0000;
            --main-red-dark: #9f0000;
            --main-red-soft: #fff1f1;
            --bg-light: #f6f7fb;
            --text-dark: #111827;
            --text-muted: #6b7280;
            --border-soft: #edf0f5;
            --navbar-height: 72px;
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 78px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            background: var(--bg-light);
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            overflow-x: hidden;
        }

        .navbar-custom {
            height: var(--navbar-height);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-soft);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
            z-index: 1030;
        }

        .navbar-brand {
            color: var(--main-red);
            font-weight: 800;
            font-size: 1.32rem;
            letter-spacing: -0.7px;
            white-space: nowrap;
        }

        .navbar-brand span { color: #1f2937; }

        .brand-logo {
            width: 42px;
            height: 42px;
            object-fit: contain;
            border-radius: 12px;
            flex-shrink: 0;
        }

        .topbar-btn,
        .btn-logout {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            border: 1px solid var(--border-soft);
            background: #ffffff;
            color: #6b7280;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s ease;
            flex-shrink: 0;
        }

        .topbar-btn:hover {
            color: var(--main-red);
            background: var(--main-red-soft);
            border-color: #ffd2d2;
        }

        .btn-logout:hover {
            color: #ffffff;
            background: var(--main-red);
            border-color: var(--main-red);
        }

        .language-pill {
            height: 40px;
            padding: 0 14px;
            border-radius: 999px;
            background: #ffffff;
            border: 1px solid var(--border-soft);
            color: #4b5563;
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 0.84rem;
            font-weight: 800;
        }

        .sidebar-custom {
            width: var(--sidebar-width);
            height: calc(100vh - var(--navbar-height));
            position: fixed;
            top: var(--navbar-height);
            left: 0;
            background: #ffffff;
            border-right: 1px solid var(--border-soft);
            padding: 18px;
            z-index: 1020;
            overflow-y: auto;
            overflow-x: hidden;
            transition: width 0.22s ease, transform 0.3s ease, padding 0.22s ease;
        }

        .sidebar-custom::-webkit-scrollbar { width: 6px; }
        .sidebar-custom::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 999px;
        }

        .sidebar-resizer {
            position: fixed;
            top: var(--navbar-height);
            left: calc(var(--sidebar-width) - 3px);
            width: 6px;
            height: calc(100vh - var(--navbar-height));
            cursor: col-resize;
            z-index: 1025;
            transition: left 0.22s ease;
        }

        .sidebar-resizer:hover { background: rgba(204, 0, 0, 0.12); }

        .content-custom {
            margin-left: var(--sidebar-width);
            margin-top: var(--navbar-height);
            padding: 34px 40px;
            min-height: calc(100vh - var(--navbar-height));
            transition: margin-left 0.22s ease;
        }

        .profile-card {
            background: linear-gradient(135deg, #ffffff 0%, #fff7f7 100%);
            border: 1px solid #ffe1e1;
            border-radius: 20px;
            padding: 15px;
            margin-bottom: 18px;
            box-shadow: 0 14px 28px rgba(204, 0, 0, 0.055);
            min-width: 0;
            overflow: hidden;
        }

        .profile-row {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .profile-avatar {
            width: 52px;
            height: 52px;
            border-radius: 18px;
            background: linear-gradient(135deg, #cc0000, #650000);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.95rem;
            flex-shrink: 0;
            overflow: hidden;
        }

        .profile-avatar img,
        .topbar-avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-info {
            min-width: 0;
            flex: 1;
            overflow: hidden;
        }

        .profile-name {
            font-weight: 800;
            font-size: 0.95rem;
            color: #111827;
            line-height: 1.25;
            margin: 0;
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .profile-role {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 6px;
            max-width: 100%;
            background: #ffffff;
            border: 1px solid #ffe0e0;
            color: var(--main-red);
            padding: 4px 9px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: capitalize;
            white-space: nowrap;
        }

        .profile-meta {
            margin-top: 11px;
            color: #6b7280;
            font-size: 0.78rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 7px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .home-link-wrap {
            margin-bottom: 16px;
        }

        .sidebar-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.09em;
            color: #9ca3af;
            font-weight: 800;
            margin: 10px 8px 12px;
            white-space: nowrap;
        }

        .menu-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .menu-item { margin-bottom: 8px; }

        .menu-link {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 12px 13px;
            color: #4b5563;
            text-decoration: none;
            border-radius: 15px;
            font-weight: 700;
            font-size: 0.91rem;
            transition: 0.2s ease;
            min-width: 0;
            position: relative;
        }

        .menu-link .menu-icon {
            width: 22px;
            text-align: center;
            font-size: 1.05rem;
            flex-shrink: 0;
        }

        .menu-link .menu-text {
            flex: 1;
            min-width: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .menu-link .menu-arrow {
            font-size: 0.72rem;
            flex-shrink: 0;
        }

        .menu-link:hover {
            background: #fff4f4;
            color: var(--main-red);
            transform: translateX(3px);
        }

        .menu-link.active {
            background: linear-gradient(135deg, #fff0f0, #ffe4e4);
            color: var(--main-red);
            font-weight: 800;
            box-shadow: inset 3px 0 0 var(--main-red);
        }

        .submenu-list {
            list-style: none;
            padding-left: 18px;
            margin: 8px 0 0;
        }

        .submenu-link {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 10px 12px;
            color: #6b7280;
            text-decoration: none;
            border-radius: 12px;
            font-size: 0.84rem;
            font-weight: 700;
            transition: 0.2s ease;
            margin-bottom: 5px;
            min-width: 0;
        }

        .submenu-link i {
            width: 18px;
            text-align: center;
            flex-shrink: 0;
        }

        .submenu-link span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .submenu-link:hover {
            background: #f9fafb;
            color: var(--main-red);
        }

        .submenu-link.active {
            background: var(--main-red-soft);
            color: var(--main-red);
            font-weight: 800;
        }

        .sidebar-footer {
            margin-top: 24px;
            padding: 14px;
            background: #f9fafb;
            border: 1px solid var(--border-soft);
            border-radius: 18px;
            color: #6b7280;
            font-size: 0.78rem;
            font-weight: 650;
        }

        .sidebar-footer strong { color: #111827; }

        body.sidebar-collapsed .sidebar-custom {
            width: var(--sidebar-collapsed-width);
            padding: 16px 0;
        }

        body.sidebar-collapsed .content-custom {
            margin-left: var(--sidebar-collapsed-width);
        }

        body.sidebar-collapsed .sidebar-resizer { display: none; }

        body.sidebar-collapsed .profile-card {
            padding: 0;
            margin-bottom: 22px;
            border: none;
            background: transparent;
            box-shadow: none;
            overflow: visible;
        }

        body.sidebar-collapsed .profile-row {
            justify-content: center;
        }

        body.sidebar-collapsed .profile-avatar {
            width: 54px;
            height: 54px;
            border-radius: 18px;
        }

        body.sidebar-collapsed .profile-info,
        body.sidebar-collapsed .profile-meta,
        body.sidebar-collapsed .sidebar-label,
        body.sidebar-collapsed .menu-text,
        body.sidebar-collapsed .menu-arrow,
        body.sidebar-collapsed .submenu-list,
        body.sidebar-collapsed .sidebar-footer,
        body.sidebar-collapsed .hide-on-collapse {
            display: none !important;
        }

        body.sidebar-collapsed .home-link-wrap,
        body.sidebar-collapsed .menu-list {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        body.sidebar-collapsed .menu-item {
            margin: 0;
            width: 100%;
            display: flex;
            justify-content: center;
        }

        body.sidebar-collapsed .menu-link,
        body.sidebar-collapsed .menu-link.justify-content-between {
            width: 54px !important;
            height: 54px !important;
            padding: 0 !important;
            border-radius: 18px;
            justify-content: center !important;
            align-items: center !important;
            gap: 0 !important;
            margin: 0 auto !important;
            transform: none !important;
        }

        body.sidebar-collapsed .menu-link .menu-icon {
            margin: 0 !important;
            width: auto !important;
            font-size: 1.08rem;
            line-height: 1;
            display: block;
            text-align: center;
        }

        body.sidebar-collapsed .menu-link:hover {
            transform: none;
            background: #fff4f4;
        }

        body.sidebar-collapsed .menu-link.active {
            background: linear-gradient(135deg, #ffe8e8, #ffe1e1);
            color: var(--main-red);
            box-shadow: none;
        }

        body.sidebar-collapsed .menu-link.active::before {
            content: "";
            position: absolute;
            left: -13px;
            width: 4px;
            height: 30px;
            background: var(--main-red);
            border-radius: 999px;
        }

        .sidebar-close-btn {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            border: 1px solid #ffe0e0;
            color: var(--main-red);
            background: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s ease;
            flex-shrink: 0;
        }

        .sidebar-close-btn:hover {
            background: var(--main-red);
            color: #ffffff;
        }

        .sidebar-mobile-head,
        .sidebar-overlay {
            display: none;
        }

        .content-custom .empty-state,
        .content-custom .empty-result,
        .content-custom .empty-wrapper,
        .content-custom .no-data,
        .content-custom .no-result {
            width: 100%;
            min-height: 280px;
            display: flex !important;
            flex-direction: column;
            align-items: center !important;
            justify-content: center !important;
            text-align: center !important;
            margin-left: auto;
            margin-right: auto;
            grid-column: 1 / -1;
        }

        @media (max-width: 991px) {
            .sidebar-custom {
                width: 300px !important;
                transform: translateX(-100%);
                z-index: 1040;
                box-shadow: 20px 0 40px rgba(15, 23, 42, 0.14);
                padding: 18px !important;
            }

            .sidebar-custom.show-mobile { transform: translateX(0); }
            .sidebar-resizer { display: none !important; }

            .content-custom {
                margin-left: 0 !important;
                padding: 24px 16px !important;
            }

            .sidebar-mobile-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 16px;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.45);
                z-index: 1035;
                backdrop-filter: blur(3px);
            }

            .sidebar-overlay.show { display: block; }

            body.sidebar-collapsed .sidebar-custom { width: 300px !important; }
            body.sidebar-collapsed .content-custom { margin-left: 0 !important; }

            body.sidebar-collapsed .profile-card {
                padding: 15px;
                border: 1px solid #ffe1e1;
                background: linear-gradient(135deg, #ffffff 0%, #fff7f7 100%);
                box-shadow: 0 14px 28px rgba(204, 0, 0, 0.055);
            }

            body.sidebar-collapsed .profile-row { justify-content: flex-start; }

            body.sidebar-collapsed .profile-info,
            body.sidebar-collapsed .profile-meta,
            body.sidebar-collapsed .sidebar-label,
            body.sidebar-collapsed .menu-text,
            body.sidebar-collapsed .menu-arrow,
            body.sidebar-collapsed .sidebar-footer,
            body.sidebar-collapsed .hide-on-collapse {
                display: block !important;
            }

            body.sidebar-collapsed .submenu-list { display: block !important; }

            body.sidebar-collapsed .home-link-wrap,
            body.sidebar-collapsed .menu-list {
                align-items: stretch;
                gap: 0;
            }

            body.sidebar-collapsed .menu-item {
                display: block;
                margin-bottom: 8px;
            }

            body.sidebar-collapsed .menu-link,
            body.sidebar-collapsed .menu-link.justify-content-between {
                width: auto !important;
                height: auto !important;
                justify-content: flex-start !important;
                padding: 12px 13px !important;
                gap: 11px !important;
                margin: 0 !important;
            }

            body.sidebar-collapsed .menu-link.active::before { display: none; }
        }

        @media (max-width: 575px) {
            .content-custom { padding: 20px 14px !important; }
            .navbar-brand { font-size: 1.08rem; }
            .brand-logo {
                width: 36px;
                height: 36px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    @php
        $currentUser = auth()->user();

        $displayName = $currentUser->name ?? 'Mahasiswa Telkom';
        $displayRole = $currentUser->role ?? 'mahasiswa';
        $displayNim = $currentUser->nim ?? null;

        $facilityUser = null;

        try {
            if ($currentUser && $currentUser->email) {
                $profileResponse = \Illuminate\Support\Facades\Http::timeout(2)
                    ->acceptJson()
                    ->get('http://127.0.0.1:8001/api/eai/users/by-email', [
                        'email' => $currentUser->email,
                    ]);

                if ($profileResponse->successful()) {
                    $facilityUser = $profileResponse->json('data');
                }
            }
        } catch (\Throwable $e) {
            $facilityUser = null;
        }

        $displayName = data_get($facilityUser, 'nama') ?? $displayName;
        $displayRole = data_get($facilityUser, 'role') ?? $displayRole;
        $displayNim = data_get($facilityUser, 'nim') ?? $displayNim;

        $profilePhoto =
            data_get($facilityUser, 'foto_profil')
            ?? ($currentUser->foto_profil ?? null)
            ?? ($currentUser->profile_photo ?? null)
            ?? ($currentUser->avatar ?? null);

        if ($profilePhoto) {
            if (\Illuminate\Support\Str::startsWith($profilePhoto, 'http://localhost/')) {
                $profilePhoto = str_replace('http://localhost/', 'http://127.0.0.1:8001/', $profilePhoto);
            }

            if (\Illuminate\Support\Str::startsWith($profilePhoto, '/storage/')) {
                $profilePhoto = 'http://127.0.0.1:8001' . $profilePhoto;
            }
        }

        $words = collect(explode(' ', trim($displayName)))->filter()->values();

        $initials = strtoupper(
            ($words->get(0) ? mb_substr($words->get(0), 0, 1) : 'U') .
            ($words->get(1) ? mb_substr($words->get(1), 0, 1) : '')
        );
    @endphp

    <div class="sidebar-overlay" id="mobileOverlay"></div>

    <nav class="navbar navbar-expand-lg navbar-custom fixed-top py-0">
        <div class="container-fluid px-3 px-md-4">
            <div class="d-flex align-items-center gap-2">
                <button class="topbar-btn d-lg-none" id="btnToggleSidebar" type="button" title="Buka Menu">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <button class="topbar-btn d-none d-lg-inline-flex" id="btnCollapseSidebar" type="button" title="Tutup / buka sidebar">
                    <i class="fa-solid fa-bars-staggered"></i>
                </button>

                <a class="navbar-brand py-2 m-0 d-flex align-items-center text-decoration-none" href="{{ route('dashboard') }}">
                    <img src="/storage/lost-found/items/LOGO_TRANSPARAN.png" alt="Logo Campus Care" class="brand-logo me-2" onerror="this.onerror=null; this.src='/images/IMG_1277.png';">
                    Campus<span>Care</span>
                </a>
            </div>

            <div class="d-flex align-items-center gap-2">
                <div class="language-pill d-none d-sm-flex">
                    <i class="fa-solid fa-globe"></i>
                    ID
                </div>

                <div class="topbar-btn d-none d-md-inline-flex overflow-hidden" title="{{ $displayName }}">
                    @if($profilePhoto)
                        <img src="{{ $profilePhoto }}" alt="Profile" class="topbar-avatar-img">
                    @else
                        <span style="font-size:0.78rem; font-weight:800; color:var(--main-red);">{{ $initials }}</span>
                    @endif
                </div>
                
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-logout btn-sm" title="Keluar">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <aside class="sidebar-custom" id="sidebar">
        <div class="sidebar-mobile-head">
            <strong>Menu Campus Care</strong>
            <button class="sidebar-close-btn" id="btnCloseMobileSidebar" type="button">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="profile-card">
            <div class="profile-row">
                <div class="profile-avatar" title="{{ $displayName }}">
                    @if($profilePhoto)
                        <img src="{{ $profilePhoto }}" alt="Foto Profil">
                    @else
                        {{ $initials }}
                    @endif
                </div>

                <div class="profile-info">
                    <p class="profile-name" title="{{ $displayName }}">{{ $displayName }}</p>
                    <div class="profile-role">
                        <i class="fa-solid fa-user-check"></i>
                        {{ $displayRole }}
                    </div>
                </div>
            </div>

            <div class="profile-meta" title="{{ $displayNim ?: 'NIM belum tersedia' }}">
                <i class="fa-solid fa-id-card"></i>
                <span>{{ $displayNim ?: 'NIM belum tersedia' }}</span>
            </div>
        </div>

        <div class="home-link-wrap">
            <a href="{{ route('dashboard') }}"
               class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               title="Dashboard">
                <i class="fa-solid fa-house menu-icon"></i>
                <span class="menu-text">Home</span>
            </a>
        </div>

        <div class="sidebar-label">Menu Utama</div>
        
        <ul class="menu-list">
            @if(auth()->check())
                @if(auth()->user()->isSatpam() || auth()->user()->role === 'admin')
                    <li class="menu-item">
                        <a href="#collapseLostFound"
                           data-bs-toggle="collapse"
                           data-collapsed-href="{{ route('lost-found.index') }}"
                           class="menu-link justify-content-between {{ request()->routeIs('lost-found.*') ? 'active' : '' }}"
                           aria-expanded="{{ request()->routeIs('lost-found.*') ? 'true' : 'false' }}"
                           title="Barang Hilang">
                            <i class="fa-solid fa-box-open menu-icon"></i>
                            <span class="menu-text">Barang Hilang</span>
                            <i class="fa-solid fa-chevron-down menu-arrow"></i>
                        </a>

                        <div class="collapse {{ request()->routeIs('lost-found.*') ? 'show' : '' }}" id="collapseLostFound">
                            <ul class="submenu-list">
                                <li>
                                    <a href="{{ route('lost-found.index') }}" class="submenu-link {{ request()->routeIs('lost-found.index') || request()->routeIs('lost-found.show') ? 'active' : '' }}">
                                        <i class="fa-solid fa-list-ul"></i>
                                        <span>Semua Laporan</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    
                    <li class="menu-item">
                        <a href="#collapseFacility"
                           data-bs-toggle="collapse"
                           data-collapsed-href="{{ route('admin.facility.reports') }}"
                           class="menu-link justify-content-between {{ request()->routeIs('admin.facility.*') ? 'active' : '' }}"
                           aria-expanded="{{ request()->routeIs('admin.facility.*') ? 'true' : 'false' }}"
                           title="Fasilitas Rusak">
                            <i class="fa-solid fa-screwdriver-wrench menu-icon"></i>
                            <span class="menu-text">Fasilitas Rusak</span>
                            <i class="fa-solid fa-chevron-down menu-arrow"></i>
                        </a>

                        <div class="collapse {{ request()->routeIs('admin.facility.*') ? 'show' : '' }}" id="collapseFacility">
                            <ul class="submenu-list">
                                <li>
                                    <a href="{{ route('admin.facility.reports') }}" class="submenu-link {{ request()->routeIs('admin.facility.reports') ? 'active' : '' }}">
                                        <i class="fa-solid fa-list-check"></i>
                                        <span>Laporan Masuk</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.facility.rooms') }}" class="submenu-link {{ request()->routeIs('admin.facility.rooms') ? 'active' : '' }}">
                                        <i class="fa-solid fa-building"></i>
                                        <span>Kelola Ruangan</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @else
                    <li class="menu-item">
                        <a href="#collapseLostFound"
                           data-bs-toggle="collapse"
                           data-collapsed-href="{{ route('lost-found.index') }}"
                           class="menu-link justify-content-between {{ request()->routeIs('lost-found.*') ? 'active' : '' }}"
                           aria-expanded="{{ request()->routeIs('lost-found.*') ? 'true' : 'false' }}"
                           title="Barang Hilang">
                            <i class="fa-solid fa-box-open menu-icon"></i>
                            <span class="menu-text">Barang Hilang</span>
                            <i class="fa-solid fa-chevron-down menu-arrow"></i>
                        </a>

                        <div class="collapse {{ request()->routeIs('lost-found.*') ? 'show' : '' }}" id="collapseLostFound">
                            <ul class="submenu-list">
                                <li>
                                    <a href="{{ route('lost-found.index') }}" class="submenu-link {{ request()->routeIs('lost-found.index') && !request()->routeIs('lost-found.lost.*') && !request()->routeIs('lost-found.history*') ? 'active' : '' }}">
                                        <i class="fa-solid fa-table-list"></i>
                                        <span>Daftar Temuan</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('lost-found.lost.index') }}" class="submenu-link {{ request()->routeIs('lost-found.lost.index') ? 'active' : '' }}">
                                        <i class="fa-solid fa-search"></i>
                                        <span>Daftar Kehilangan</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('lost-found.history', ['tab' => 'all']) }}" class="submenu-link {{ request()->routeIs('lost-found.history') ? 'active' : '' }}">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                        <span>Riwayat Saya</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    
                    <li class="menu-item">
                        <a href="#collapseFacility"
                           data-bs-toggle="collapse"
                           data-collapsed-href="{{ route('facility.index') }}"
                           class="menu-link justify-content-between {{ request()->routeIs('facility.*') ? 'active' : '' }}"
                           aria-expanded="{{ request()->routeIs('facility.*') ? 'true' : 'false' }}"
                           title="Fasilitas Rusak">
                            <i class="fa-solid fa-screwdriver-wrench menu-icon"></i>
                            <span class="menu-text">Fasilitas Rusak</span>
                            <i class="fa-solid fa-chevron-down menu-arrow"></i>
                        </a>

                        <div class="collapse {{ request()->routeIs('facility.*') ? 'show' : '' }}" id="collapseFacility">
                            <ul class="submenu-list">
                                <li>
                                    <a href="{{ route('facility.index') }}" class="submenu-link {{ request()->routeIs('facility.index') || request()->routeIs('facility.create') ? 'active' : '' }}">
                                        <i class="fa-solid fa-images"></i>
                                        <span>Katalog Laporan</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif
            @endif
        </ul>

        <div class="sidebar-footer">
            <strong>Campus Care</strong><br>
            Sistem pelaporan fasilitas dan barang hilang Kampus mu.
        </div>
    </aside>

    <div class="sidebar-resizer" id="sidebarResizer"></div>

    <main class="content-custom">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const root = document.documentElement;
            const body = document.body;

            const sidebar = document.getElementById('sidebar');
            const resizer = document.getElementById('sidebarResizer');

            const btnToggleMobile = document.getElementById('btnToggleSidebar');
            const btnCloseMobile = document.getElementById('btnCloseMobileSidebar');
            const btnCollapseSidebar = document.getElementById('btnCollapseSidebar');
            const overlay = document.getElementById('mobileOverlay');

            const widthKey = 'campusCareSidebarWidth';
            const collapsedKey = 'campusCareSidebarCollapsed';

            const savedWidth = localStorage.getItem(widthKey);
            const savedCollapsed = localStorage.getItem(collapsedKey);

            if (savedWidth) {
                const parsedWidth = parseInt(savedWidth, 10);
                if (!Number.isNaN(parsedWidth) && parsedWidth >= 230 && parsedWidth <= 380) {
                    root.style.setProperty('--sidebar-width', parsedWidth + 'px');
                }
            }

            if (savedCollapsed === 'true') {
                body.classList.add('sidebar-collapsed');
            }

            function openMobileSidebar() {
                sidebar?.classList.add('show-mobile');
                overlay?.classList.add('show');
            }

            function closeMobileSidebar() {
                sidebar?.classList.remove('show-mobile');
                overlay?.classList.remove('show');
            }

            btnToggleMobile?.addEventListener('click', openMobileSidebar);
            btnCloseMobile?.addEventListener('click', closeMobileSidebar);
            overlay?.addEventListener('click', closeMobileSidebar);

            btnCollapseSidebar?.addEventListener('click', function () {
                body.classList.toggle('sidebar-collapsed');

                localStorage.setItem(
                    collapsedKey,
                    body.classList.contains('sidebar-collapsed') ? 'true' : 'false'
                );
            });

            document.querySelectorAll('.menu-link[data-collapsed-href]').forEach(function (link) {
                link.addEventListener('click', function (event) {
                    const isDesktop = window.innerWidth > 991;
                    const isCollapsed = document.body.classList.contains('sidebar-collapsed');

                    if (isDesktop && isCollapsed) {
                        event.preventDefault();
                        event.stopPropagation();
                        event.stopImmediatePropagation();

                        const targetUrl = this.getAttribute('data-collapsed-href');

                        if (targetUrl) {
                            window.location.href = targetUrl;
                        }
                    }
                }, true);
            });

            let isResizing = false;

            resizer?.addEventListener('mousedown', function (event) {
                if (body.classList.contains('sidebar-collapsed')) return;

                isResizing = true;
                document.body.style.cursor = 'col-resize';
                document.body.style.userSelect = 'none';
                event.preventDefault();
            });

            document.addEventListener('mousemove', function (event) {
                if (!isResizing) return;

                let newWidth = event.clientX;

                if (newWidth < 230) newWidth = 230;
                if (newWidth > 380) newWidth = 380;

                root.style.setProperty('--sidebar-width', newWidth + 'px');
                localStorage.setItem(widthKey, newWidth);
            });

            document.addEventListener('mouseup', function () {
                if (!isResizing) return;

                isResizing = false;
                document.body.style.cursor = '';
                document.body.style.userSelect = '';
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth <= 991) {
                    closeMobileSidebar();
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>