@extends('layouts.app')

@section('title', 'Riwayat Aktivitas - Campus Care')
@section('role_text', session('nim') ?? 'Mahasiswa Telkom')

@push('styles')
<style>
    .history-page {
        animation: fadeUp 0.35s ease-out;
    }

    .history-hero {
        background:
            radial-gradient(circle at 92% 18%, rgba(255,255,255,0.24), transparent 28%),
            linear-gradient(135deg, #d40000 0%, #b50000 48%, #760000 100%);
        border-radius: 30px;
        color: #ffffff;
        padding: 32px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 22px 48px rgba(204, 0, 0, 0.18);
        margin-bottom: 26px;
    }

    .history-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,0.07) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.07) 1px, transparent 1px);
        background-size: 34px 34px;
        opacity: 0.32;
    }

    .history-hero-content {
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
        color: rgba(255,255,255,0.9);
        font-size: 0.78rem;
        font-weight: 800;
        margin-bottom: 15px;
    }

    .history-title {
        font-size: 1.85rem;
        font-weight: 850;
        letter-spacing: -0.9px;
        margin-bottom: 8px;
    }

    .history-subtitle {
        color: rgba(255,255,255,0.76);
        font-weight: 500;
        max-width: 820px;
        margin: 0;
        line-height: 1.7;
    }

    .btn-hero-action {
        background: #ffffff;
        color: #c40000;
        border: 0;
        border-radius: 999px;
        font-weight: 850;
        padding: 12px 22px;
        box-shadow: 0 14px 24px rgba(0,0,0,0.12);
        transition: 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
    }

    .btn-hero-action:hover {
        transform: translateY(-2px);
        background: #fff6f6;
        color: #a30000;
    }

    .section-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 15px;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.95rem;
        font-weight: 850;
        color: #111827;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin: 0;
    }

    .section-title::before {
        content: "";
        width: 4px;
        height: 20px;
        border-radius: 999px;
        background: #cc0000;
        display: inline-block;
    }

    .history-stat-card {
        background: #ffffff;
        border: 1px solid #edf0f5;
        border-radius: 22px;
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 15px;
        height: 100%;
        text-decoration: none;
        color: inherit;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.045);
        transition: 0.22s ease;
        position: relative;
        overflow: hidden;
    }

    .history-stat-card::after {
        content: "";
        position: absolute;
        inset: auto 0 0 0;
        height: 3px;
        background: transparent;
        transition: 0.22s ease;
    }

    .history-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 38px rgba(15, 23, 42, 0.075);
        color: inherit;
    }

    .history-stat-card.active-tab {
        border-color: #ffd1d1;
        background: linear-gradient(135deg, #fff7f7 0%, #ffffff 100%);
    }

    .history-stat-card.active-tab::after {
        background: #cc0000;
    }

    .history-stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 17px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.18rem;
        flex-shrink: 0;
    }

    .history-stat-number {
        font-size: 1.72rem;
        font-weight: 850;
        line-height: 1;
        margin-bottom: 5px;
    }

    .history-stat-label {
        font-size: 0.72rem;
        color: #6b7280;
        font-weight: 850;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .history-toolbar {
        background: #ffffff;
        border: 1px solid #edf0f5;
        border-radius: 24px;
        padding: 14px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.04);
        margin-bottom: 22px;
    }

    .tab-chip {
        border: 1px solid #edf0f5;
        background: #ffffff;
        border-radius: 999px;
        padding: 10px 16px;
        color: #6b7280;
        font-weight: 800;
        font-size: 0.84rem;
        text-decoration: none;
        transition: 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .tab-chip:hover,
    .tab-chip.active {
        background: #cc0000;
        color: #ffffff;
        border-color: #cc0000;
        box-shadow: 0 10px 20px rgba(204, 0, 0, 0.18);
    }

    .history-table-card {
        background: #ffffff;
        border: 1px solid #edf0f5;
        border-radius: 26px;
        overflow: hidden;
        box-shadow: 0 16px 38px rgba(15, 23, 42, 0.055);
    }

    .history-table {
        margin: 0;
    }

    .history-table thead th {
        background: #f9fafb;
        color: #6b7280;
        font-size: 0.72rem;
        font-weight: 850;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 16px 18px;
        border-bottom: 1px solid #edf0f5;
        white-space: nowrap;
    }

    .history-table tbody td {
        padding: 16px 18px;
        border-bottom: 1px solid #f1f3f7;
        vertical-align: middle;
    }

    .history-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .history-table tbody tr {
        transition: 0.18s ease;
    }

    .history-table tbody tr:hover {
        background: #fffafa;
    }

    .item-row {
        display: flex;
        align-items: center;
        gap: 13px;
        min-width: 0;
    }

    .item-photo {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        object-fit: cover;
        border: 1px solid #edf0f5;
        background: #f3f4f6;
        flex-shrink: 0;
    }

    .item-name {
        color: #111827;
        font-weight: 850;
        font-size: 0.92rem;
        line-height: 1.35;
        max-width: 260px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .item-meta {
        color: #9ca3af;
        font-size: 0.78rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 4px;
    }

    .activity-badge,
    .status-badge {
        border-radius: 999px;
        padding: 7px 12px;
        font-size: 0.72rem;
        font-weight: 850;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        white-space: nowrap;
    }

    .activity-found {
        background: #ecfdf5;
        color: #059669;
    }

    .activity-lost {
        background: #fff7ed;
        color: #ea580c;
    }

    .activity-claim {
        background: #eff6ff;
        color: #2563eb;
    }

    .status-review {
        background: #fff7d9;
        color: #a16207;
    }

    .status-active {
        background: #dcfce7;
        color: #15803d;
    }

    .status-scheduled {
        background: #e0f2fe;
        color: #0369a1;
    }

    .status-done {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-detail-history {
        border: 1px solid #ffd1d1;
        background: #fff7f7;
        color: #cc0000;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 850;
        padding: 8px 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        transition: 0.2s ease;
        white-space: nowrap;
    }

    .btn-detail-history:hover {
        background: #cc0000;
        border-color: #cc0000;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .empty-history-state {
        background: #ffffff;
        border: 1px dashed #d1d5db;
        border-radius: 26px;
        padding: 58px 20px;
        text-align: center;
        color: #6b7280;
    }

    .empty-icon {
        width: 68px;
        height: 68px;
        border-radius: 22px;
        background: #f3f4f6;
        color: #9ca3af;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-bottom: 16px;
    }

    .history-mobile-list {
        display: none;
    }

    .history-mobile-card {
        background: #ffffff;
        border: 1px solid #edf0f5;
        border-radius: 24px;
        padding: 16px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, 0.05);
        margin-bottom: 14px;
    }

    .history-mobile-top {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
    }

    .history-mobile-top .item-photo {
        width: 58px;
        height: 58px;
    }

    .history-mobile-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid #f1f3f7;
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(12px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 991px) {
        .history-table-card {
            display: none;
        }

        .history-mobile-list {
            display: block;
        }
    }

    @media (max-width: 767px) {
        .history-hero {
            padding: 26px 22px;
            border-radius: 24px;
        }

        .history-title {
            font-size: 1.42rem;
        }

        .btn-hero-action {
            width: 100%;
        }

        .history-toolbar {
            overflow-x: auto;
        }

        .history-toolbar .d-flex {
            flex-wrap: nowrap !important;
            min-width: max-content;
        }

        .history-stat-card {
            padding: 15px;
        }
    }
</style>
@endpush

@section('content')
@php
    $tab = $tab ?? request('tab', 'all');

    $safeStats = [
        'total_found' => $stats['total_found'] ?? 0,
        'total_lost'  => $stats['total_lost'] ?? 0,
        'total_claim' => $stats['total_claim'] ?? 0,
    ];

    $grandTotal = array_sum($safeStats);

    $photoUrl = function ($item) {
        $photo = $item->photo ?? null;

        if ($photo && str_starts_with((string) $photo, 'http')) {
            return $photo;
        }

        if ($photo) {
            return asset('storage/' . $photo);
        }

        return 'https://placehold.co/120x120/f3f4f6/9ca3af?text=No+Photo';
    };

    $activityInfo = function ($item) {
        if (($item->type ?? null) === 'lost') {
            return [
                'label' => 'Lapor Hilang',
                'icon' => 'fa-magnifying-glass',
                'class' => 'activity-lost',
            ];
        }

        if (($item->type ?? null) === 'found' && ($item->claim_id_number ?? null) === (auth()->user()->nim ?? null)) {
            return [
                'label' => 'Klaim Barang',
                'icon' => 'fa-file-invoice',
                'class' => 'activity-claim',
            ];
        }

        return [
            'label' => 'Lapor Temuan',
            'icon' => 'fa-hand-holding-hand',
            'class' => 'activity-found',
        ];
    };

    $statusInfo = function ($status) {
        return match($status) {
            'pending_publish', 'claim_pending' => [
                'label' => 'Review Satpam',
                'icon' => 'fa-clock',
                'class' => 'status-review',
            ],
            'available' => [
                'label' => 'Tayang Aktif',
                'icon' => 'fa-circle-check',
                'class' => 'status-active',
            ],
            'claim_scheduled' => [
                'label' => 'Tunggu Diambil',
                'icon' => 'fa-calendar-check',
                'class' => 'status-scheduled',
            ],
            default => [
                'label' => 'Selesai / Diarsipkan',
                'icon' => 'fa-box-archive',
                'class' => 'status-done',
            ],
        };
    };

    $tabLabel = match($tab) {
        'temuan' => 'Lapor Temuan',
        'kehilangan' => 'Lapor Kehilangan',
        'klaim' => 'Pengajuan Klaim',
        default => 'Semua Riwayat',
    };
@endphp

<div class="history-page">
    <div class="history-hero">
        <div class="history-hero-content">
            <div class="row align-items-center g-4">
                <div class="col-lg-8 col-12">
                    <div class="hero-badge">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        Activity History
                    </div>

                    <h1 class="history-title">Riwayat Aktivitas Saya</h1>
                    <p class="history-subtitle">
                        Pantau status laporan barang temuan, laporan kehilangan, dan pengajuan klaim kamu dalam satu halaman.
                    </p>
                </div>

                <div class="col-lg-4 col-12 text-lg-end">
                    <a href="{{ route('lost-found.index') }}" class="btn-hero-action">
                        <i class="fa-solid fa-plus"></i>
                        Buat Laporan Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="section-heading">
        <h2 class="section-title">Ringkasan Riwayat</h2>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <a href="{{ route('lost-found.history', ['tab' => 'all']) }}" class="history-stat-card {{ $tab === 'all' ? 'active-tab' : '' }}">
                <div class="history-stat-icon" style="background:#f1f5f9; color:#475569;">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div>
                    <div class="history-stat-number text-dark">{{ $grandTotal }}</div>
                    <div class="history-stat-label">Semua Riwayat</div>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <a href="{{ route('lost-found.history', ['tab' => 'temuan']) }}" class="history-stat-card {{ $tab === 'temuan' ? 'active-tab' : '' }}">
                <div class="history-stat-icon" style="background:#ecfdf5; color:#10b981;">
                    <i class="fa-solid fa-hand-holding-hand"></i>
                </div>
                <div>
                    <div class="history-stat-number" style="color:#10b981;">{{ $safeStats['total_found'] }}</div>
                    <div class="history-stat-label">Lapor Temuan</div>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <a href="{{ route('lost-found.history', ['tab' => 'kehilangan']) }}" class="history-stat-card {{ $tab === 'kehilangan' ? 'active-tab' : '' }}">
                <div class="history-stat-icon" style="background:#fff7ed; color:#f97316;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <div>
                    <div class="history-stat-number" style="color:#f97316;">{{ $safeStats['total_lost'] }}</div>
                    <div class="history-stat-label">Lapor Kehilangan</div>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <a href="{{ route('lost-found.history', ['tab' => 'klaim']) }}" class="history-stat-card {{ $tab === 'klaim' ? 'active-tab' : '' }}">
                <div class="history-stat-icon" style="background:#eff6ff; color:#2563eb;">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
                <div>
                    <div class="history-stat-number" style="color:#2563eb;">{{ $safeStats['total_claim'] }}</div>
                    <div class="history-stat-label">Pengajuan Klaim</div>
                </div>
            </a>
        </div>
    </div>

    <div class="section-heading">
        <h2 class="section-title">Daftar Aktivitas</h2>
    </div>

    <div class="history-toolbar">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('lost-found.history', ['tab' => 'all']) }}" class="tab-chip {{ $tab === 'all' ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group"></i>
                Semua
            </a>

            <a href="{{ route('lost-found.history', ['tab' => 'temuan']) }}" class="tab-chip {{ $tab === 'temuan' ? 'active' : '' }}">
                <i class="fa-solid fa-hand-holding-hand"></i>
                Temuan
            </a>

            <a href="{{ route('lost-found.history', ['tab' => 'kehilangan']) }}" class="tab-chip {{ $tab === 'kehilangan' ? 'active' : '' }}">
                <i class="fa-solid fa-magnifying-glass"></i>
                Kehilangan
            </a>

            <a href="{{ route('lost-found.history', ['tab' => 'klaim']) }}" class="tab-chip {{ $tab === 'klaim' ? 'active' : '' }}">
                <i class="fa-solid fa-file-invoice"></i>
                Klaim
            </a>

            <span class="tab-chip ms-lg-auto" style="pointer-events:none; background:#f9fafb;">
                <i class="fa-solid fa-filter"></i>
                {{ $tabLabel }}
            </span>
        </div>
    </div>

    <div class="history-table-card">
        <div class="table-responsive">
            <table class="table history-table align-middle">
                <thead>
                    <tr>
                        <th>Detail Objek Barang</th>
                        <th>Kategori Aktivitas</th>
                        <th>Status Terkini</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($items as $item)
                        @php
                            $activity = $activityInfo($item);
                            $status = $statusInfo($item->status ?? null);

                            try {
                                $dateText = $item->created_at
                                    ? \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y')
                                    : '-';
                            } catch (\Exception $e) {
                                $dateText = '-';
                            }
                        @endphp

                        <tr>
                            <td>
                                <div class="item-row">
                                    <img
                                        src="{{ $photoUrl($item) }}"
                                        class="item-photo"
                                        alt="{{ $item->name ?? 'Barang' }}"
                                        onerror="this.onerror=null; this.src='https://placehold.co/120x120/f3f4f6/9ca3af?text=No+Photo';"
                                    >

                                    <div class="min-w-0">
                                        <div class="item-name" title="{{ $item->name ?? 'Barang tanpa nama' }}">
                                            {{ $item->name ?? 'Barang tanpa nama' }}
                                        </div>
                                        <div class="item-meta">
                                            <i class="fa-regular fa-calendar"></i>
                                            {{ $dateText }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="activity-badge {{ $activity['class'] }}">
                                    <i class="fa-solid {{ $activity['icon'] }}"></i>
                                    {{ $activity['label'] }}
                                </span>
                            </td>

                            <td>
                                <span class="status-badge {{ $status['class'] }}">
                                    <i class="fa-solid {{ $status['icon'] }}"></i>
                                    {{ $status['label'] }}
                                </span>
                            </td>

                            <td class="text-center">
                                <a href="{{ route('lost-found.show', $item->id) }}" class="btn-detail-history">
                                    <i class="fa-solid fa-eye"></i>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-history-state">
                                    <div class="empty-icon">
                                        <i class="fa-solid fa-box-open"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-2">Riwayat belum ditemukan</h5>
                                    <p class="m-0">
                                        Tidak ada log aktivitas pada kategori <strong>{{ $tabLabel }}</strong> saat ini.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="history-mobile-list">
        @forelse($items as $item)
            @php
                $activity = $activityInfo($item);
                $status = $statusInfo($item->status ?? null);

                try {
                    $dateText = $item->created_at
                        ? \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y')
                        : '-';
                } catch (\Exception $e) {
                    $dateText = '-';
                }
            @endphp

            <div class="history-mobile-card">
                <div class="history-mobile-top">
                    <img
                        src="{{ $photoUrl($item) }}"
                        class="item-photo"
                        alt="{{ $item->name ?? 'Barang' }}"
                        onerror="this.onerror=null; this.src='https://placehold.co/120x120/f3f4f6/9ca3af?text=No+Photo';"
                    >

                    <div class="min-w-0">
                        <div class="item-name" title="{{ $item->name ?? 'Barang tanpa nama' }}">
                            {{ $item->name ?? 'Barang tanpa nama' }}
                        </div>
                        <div class="item-meta">
                            <i class="fa-regular fa-calendar"></i>
                            {{ $dateText }}
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <span class="activity-badge {{ $activity['class'] }}">
                        <i class="fa-solid {{ $activity['icon'] }}"></i>
                        {{ $activity['label'] }}
                    </span>

                    <span class="status-badge {{ $status['class'] }}">
                        <i class="fa-solid {{ $status['icon'] }}"></i>
                        {{ $status['label'] }}
                    </span>
                </div>

                <div class="history-mobile-actions">
                    <a href="{{ route('lost-found.show', $item->id) }}" class="btn-detail-history w-100">
                        <i class="fa-solid fa-eye"></i>
                        Lihat Detail
                    </a>
                </div>
            </div>
        @empty
            <div class="empty-history-state">
                <div class="empty-icon">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Riwayat belum ditemukan</h5>
                <p class="m-0">
                    Tidak ada log aktivitas pada kategori <strong>{{ $tabLabel }}</strong> saat ini.
                </p>
            </div>
        @endforelse
    </div>

    @if(method_exists($items, 'links'))
        <div class="mt-4 d-flex justify-content-center">
            {{ $items->links() }}
        </div>
    @endif
</div>
@endsection
