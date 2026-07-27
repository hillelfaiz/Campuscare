@extends('layouts.app')

@section('title', 'Dashboard - Campus Care')

@push('styles')
<style>
    .dashboard-hero {
        background:
            radial-gradient(circle at 92% 18%, rgba(255,255,255,0.22), transparent 28%),
            linear-gradient(135deg, #d40000 0%, #b50000 48%, #760000 100%);
        border-radius: 30px;
        color: white;
        padding: 34px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 22px 48px rgba(204, 0, 0, 0.18);
        margin-bottom: 28px;
    }

    .dashboard-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,0.07) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.07) 1px, transparent 1px);
        background-size: 34px 34px;
        opacity: 0.32;
    }

    .dashboard-hero-content {
        position: relative;
        z-index: 2;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 13px;
        border-radius: 999px;
        background: rgba(255,255,255,0.14);
        border: 1px solid rgba(255,255,255,0.22);
        color: rgba(255,255,255,0.88);
        font-size: 0.78rem;
        font-weight: 800;
        margin-bottom: 15px;
    }

    .dashboard-title {
        font-size: 1.9rem;
        font-weight: 850;
        letter-spacing: -0.9px;
        margin-bottom: 8px;
    }

    .dashboard-subtitle {
        color: rgba(255,255,255,0.75);
        font-weight: 500;
        max-width: 820px;
        margin: 0;
        line-height: 1.7;
    }

    .section-title-dashboard {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.95rem;
        font-weight: 850;
        color: #111827;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 16px;
    }

    .section-title-dashboard::before {
        content: "";
        width: 4px;
        height: 20px;
        border-radius: 999px;
        background: #cc0000;
        display: inline-block;
    }

    .quick-card {
        background: #ffffff;
        border: 1px solid #edf0f5;
        border-radius: 24px;
        padding: 22px;
        height: 100%;
        text-decoration: none;
        color: #111827;
        display: block;
        box-shadow: 0 14px 35px rgba(15, 23, 42, 0.055);
        transition: 0.22s ease;
    }

    .quick-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 22px 46px rgba(15, 23, 42, 0.09);
        color: #111827;
    }

    .quick-icon {
        width: 58px;
        height: 58px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 17px;
        font-size: 1.35rem;
    }

    .quick-title {
        font-weight: 850;
        margin-bottom: 7px;
        font-size: 1.04rem;
        letter-spacing: -0.3px;
    }

    .quick-desc {
        color: #6b7280;
        font-size: 0.88rem;
        line-height: 1.65;
        margin: 0;
        font-weight: 500;
    }

    .info-panel {
        background: #ffffff;
        border: 1px solid #edf0f5;
        border-radius: 26px;
        padding: 22px;
        height: 100%;
        box-shadow: 0 14px 35px rgba(15, 23, 42, 0.05);
    }

    .info-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .info-panel-title {
        font-size: 1rem;
        font-weight: 850;
        margin: 0;
        color: #111827;
    }

    .info-panel-link {
        font-size: 0.8rem;
        color: #cc0000;
        text-decoration: none;
        font-weight: 800;
    }

    .info-panel-link:hover {
        text-decoration: underline;
    }

    .latest-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 13px 0;
        border-bottom: 1px solid #f1f3f7;
    }

    .latest-item:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .latest-icon {
        width: 42px;
        height: 42px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1rem;
    }

    .latest-title {
        font-size: 0.92rem;
        font-weight: 850;
        color: #111827;
        margin-bottom: 4px;
        line-height: 1.35;
    }

    .latest-meta {
        color: #6b7280;
        font-size: 0.8rem;
        font-weight: 600;
        line-height: 1.5;
    }

    .status-mini {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 9px;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 850;
        margin-top: 6px;
    }

    .status-pending {
        background: #fff7d9;
        color: #a16207;
    }

    .status-progress {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .status-completed {
        background: #dcfce7;
        color: #15803d;
    }

    .empty-mini {
        padding: 24px 10px;
        text-align: center;
        color: #9ca3af;
        font-size: 0.88rem;
        font-weight: 600;
    }

    .tips-card {
        background: linear-gradient(135deg, #ffffff 0%, #fff7f7 100%);
        border: 1px solid #ffe1e1;
        border-radius: 26px;
        padding: 22px;
        height: 100%;
        box-shadow: 0 14px 35px rgba(204, 0, 0, 0.055);
    }

    .tips-list {
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .tips-list li {
        display: flex;
        gap: 11px;
        color: #4b5563;
        font-size: 0.88rem;
        font-weight: 600;
        line-height: 1.55;
        margin-bottom: 13px;
    }

    .tips-list li:last-child {
        margin-bottom: 0;
    }

    .tips-list i {
        color: #cc0000;
        margin-top: 3px;
    }

    @media (max-width: 767px) {
        .dashboard-hero {
            padding: 26px 22px;
            border-radius: 24px;
        }

        .dashboard-title {
            font-size: 1.45rem;
        }

        .quick-card {
            padding: 20px;
        }
    }
</style>
@endpush

@section('content')
@php
    $userName = auth()->check() ? auth()->user()->name : 'Mahasiswa Telkom';

    $lostFoundCount = isset($latestLostFound) ? $latestLostFound->count() : 0;
    $facilityCount = isset($latestFacilities) ? $latestFacilities->count() : 0;
@endphp

<div class="dashboard-hero">
    <div class="dashboard-hero-content">
        <div class="hero-badge">
            <i class="fa-solid fa-house"></i>
            Welcome Page
        </div>

        <h1 class="dashboard-title">Halo, {{ $userName }} 👋</h1>
        <p class="dashboard-subtitle">
            Selamat datang di Campus Care. Kamu bisa melaporkan barang hilang, melihat barang temuan, dan membuat aduan fasilitas rusak dalam satu sistem.
        </p>
    </div>
</div>

<div class="section-title-dashboard">Akses Cepat</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-md-6 col-xl-3">
        <a href="{{ route('lost-found.index') }}" class="quick-card">
            <div class="quick-icon" style="background:#fff1f1; color:#cc0000;">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <h3 class="quick-title">Barang Hilang</h3>
            <p class="quick-desc">
                Lihat katalog barang temuan dan laporan kehilangan yang sudah masuk ke sistem.
            </p>
        </a>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <a href="{{ route('facility.index') }}" class="quick-card">
            <div class="quick-icon" style="background:#eff6ff; color:#2563eb;">
                <i class="fa-solid fa-screwdriver-wrench"></i>
            </div>
            <h3 class="quick-title">Fasilitas Rusak</h3>
            <p class="quick-desc">
                Pantau katalog laporan fasilitas rusak dan perkembangan status perbaikannya.
            </p>
        </a>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <a href="{{ route('lost-found.index') }}" class="quick-card">
            <div class="quick-icon" style="background:#fff7ed; color:#f97316;">
                <i class="fa-solid fa-magnifying-glass-location"></i>
            </div>
            <h3 class="quick-title">Buat Aduan Barang Hilang</h3>
            <p class="quick-desc">
                Buat laporan baru jika kamu kehilangan barang di area kampus.
            </p>
        </a>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <a href="{{ route('facility.create') }}" class="quick-card">
            <div class="quick-icon" style="background:#ecfdf5; color:#10b981;">
                <i class="fa-solid fa-bullhorn"></i>
            </div>
            <h3 class="quick-title">Buat Aduan Fasilitas</h3>
            <p class="quick-desc">
                Buat laporan baru jika menemukan fasilitas kelas atau kampus yang bermasalah.
            </p>
        </a>
    </div>
</div>

<div class="section-title-dashboard">Info Laporan Terbaru</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-xl-6">
        <div class="info-panel">
            <div class="info-panel-header">
                <h3 class="info-panel-title">
                    <i class="fa-solid fa-box-open text-danger me-2"></i>
                    Barang Hilang Terbaru
                </h3>
                <a href="{{ route('lost-found.index') }}" class="info-panel-link">Lihat Semua</a>
            </div>

            @forelse($latestLostFound ?? [] as $item)
                @php
                    $itemTitle = $item->title
                        ?? $item->nama_barang
                        ?? $item->item_name
                        ?? $item->name
                        ?? 'Barang tanpa judul';

                    $itemLocation = $item->lokasi
                        ?? $item->location
                        ?? $item->tempat
                        ?? 'Lokasi belum tersedia';

                    $itemStatus = $item->status
                        ?? $item->claim_status
                        ?? 'tersedia';

                    try {
                        $itemDate = $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d M Y') : '-';
                    } catch (\Exception $e) {
                        $itemDate = '-';
                    }
                @endphp

                <div class="latest-item">
                    <div class="latest-icon" style="background:#fff1f1; color:#cc0000;">
                        <i class="fa-solid fa-box"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="latest-title">{{ $itemTitle }}</div>
                        <div class="latest-meta">
                            <i class="fa-solid fa-location-dot me-1"></i>{{ $itemLocation }}
                            <span class="mx-1">•</span>
                            <i class="fa-regular fa-calendar me-1"></i>{{ $itemDate }}
                        </div>
                        <span class="status-mini status-pending">
                            <i class="fa-regular fa-circle"></i>
                            {{ ucfirst(str_replace('_', ' ', $itemStatus)) }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="empty-mini">
                    <i class="fa-solid fa-folder-open d-block mb-2 fs-4"></i>
                    Belum ada laporan barang hilang terbaru.
                </div>
            @endforelse
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="info-panel">
            <div class="info-panel-header">
                <h3 class="info-panel-title">
                    <i class="fa-solid fa-screwdriver-wrench text-primary me-2"></i>
                    Fasilitas Rusak Terbaru
                </h3>
                <a href="{{ route('facility.index') }}" class="info-panel-link">Lihat Semua</a>
            </div>

            @forelse($latestFacilities ?? [] as $report)
                @php
                    $status = data_get($report, 'status', 'pending');

                    $statusLabel = match($status) {
                        'in_progress' => 'Diproses',
                        'completed' => 'Selesai',
                        default => 'Menunggu',
                    };

                    $statusClass = match($status) {
                        'in_progress' => 'status-progress',
                        'completed' => 'status-completed',
                        default => 'status-pending',
                    };

                    $facilityName = data_get($report, 'nama_fasilitas', 'Fasilitas');
                    $roomName = data_get($report, 'room.nama_ruangan', 'Umum');
                    $building = data_get($report, 'room.gedung', '-');
                    $floor = data_get($report, 'room.lantai', '-');

                    try {
                        $reportDate = data_get($report, 'created_at')
                            ? \Carbon\Carbon::parse(data_get($report, 'created_at'))->format('d M Y')
                            : '-';
                    } catch (\Exception $e) {
                        $reportDate = '-';
                    }
                @endphp

                <div class="latest-item">
                    <div class="latest-icon" style="background:#eff6ff; color:#2563eb;">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="latest-title">{{ $facilityName }}</div>
                        <div class="latest-meta">
                            <i class="fa-solid fa-location-dot me-1"></i>
                            {{ $building }} - Lt.{{ $floor }} ({{ $roomName }})
                            <span class="mx-1">•</span>
                            <i class="fa-regular fa-calendar me-1"></i>{{ $reportDate }}
                        </div>
                        <span class="status-mini {{ $statusClass }}">
                            <i class="fa-regular fa-clock"></i>
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="empty-mini">
                    <i class="fa-solid fa-folder-open d-block mb-2 fs-4"></i>
                    Belum ada laporan fasilitas rusak terbaru.
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="section-title-dashboard">Info Tambahan</div>

<div class="row g-4">
    <div class="col-12 col-xl-4">
        <div class="info-panel">
            <div class="info-panel-header">
                <h3 class="info-panel-title">
                    <i class="fa-solid fa-chart-simple text-danger me-2"></i>
                    Ringkasan Aktivitas
                </h3>
            </div>

            <div class="latest-item">
                <div class="latest-icon" style="background:#fff1f1; color:#cc0000;">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <div>
                    <div class="latest-title">{{ $lostFoundCount }} laporan barang terbaru</div>
                    <div class="latest-meta">Data diambil dari fitur Barang Hilang.</div>
                </div>
            </div>

            <div class="latest-item">
                <div class="latest-icon" style="background:#eff6ff; color:#2563eb;">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                </div>
                <div>
                    <div class="latest-title">{{ $facilityCount }} laporan fasilitas terbaru</div>
                    <div class="latest-meta">Data diambil dari API Report Facility.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="tips-card">
            <div class="info-panel-header">
                <h3 class="info-panel-title">
                    <i class="fa-solid fa-lightbulb text-warning me-2"></i>
                    Tips Cepat
                </h3>
            </div>

            <ul class="tips-list">
                <li>
                    <i class="fa-solid fa-check-circle"></i>
                    Gunakan foto yang jelas agar petugas lebih mudah memverifikasi laporan.
                </li>
                <li>
                    <i class="fa-solid fa-check-circle"></i>
                    Tulis lokasi sedetail mungkin, seperti gedung, lantai, dan ruangan.
                </li>
                <li>
                    <i class="fa-solid fa-check-circle"></i>
                    Cek katalog terlebih dahulu supaya laporan tidak terduplikasi.
                </li>
            </ul>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="info-panel">
            <div class="info-panel-header">
                <h3 class="info-panel-title">
                    <i class="fa-solid fa-circle-info text-primary me-2"></i>
                    Status Sistem
                </h3>
            </div>

            <div class="latest-item">
                <div class="latest-icon" style="background:#ecfdf5; color:#10b981;">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div>
                    <div class="latest-title">Lost & Found aktif</div>
                    <div class="latest-meta">Fitur barang hilang dapat digunakan.</div>
                </div>
            </div>

            <div class="latest-item">
                <div class="latest-icon" style="background:#ecfdf5; color:#10b981;">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div>
                    <div class="latest-title">Report Facility terintegrasi</div>
                    <div class="latest-meta">Data fasilitas diambil dari backend API.</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection