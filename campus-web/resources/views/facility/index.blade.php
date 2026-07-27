@extends('layouts.app')

@section('title', 'Katalog Fasilitas - Campus Care')
@section('role_text', 'Mahasiswa')

@push('styles')
<style>
    .facility-hero {
        background:
            radial-gradient(circle at 90% 20%, rgba(255,255,255,0.22), transparent 28%),
            linear-gradient(135deg, #dc0000 0%, #b80000 48%, #720000 100%);
        border-radius: 28px;
        color: white;
        padding: 28px 30px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 45px rgba(204, 0, 0, 0.18);
        margin-bottom: 26px;
    }

    .facility-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,0.07) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.07) 1px, transparent 1px);
        background-size: 34px 34px;
        opacity: 0.35;
    }

    .facility-hero-content {
        position: relative;
        z-index: 2;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 12px;
        border-radius: 999px;
        background: rgba(255,255,255,0.14);
        border: 1px solid rgba(255,255,255,0.2);
        color: rgba(255,255,255,0.86);
        font-size: 0.78rem;
        font-weight: 800;
        margin-bottom: 13px;
    }

    .hero-title {
        font-size: 1.62rem;
        font-weight: 850;
        letter-spacing: -0.8px;
        margin-bottom: 7px;
    }

    .hero-subtitle {
        color: rgba(255,255,255,0.72);
        font-weight: 500;
        max-width: 720px;
        margin: 0;
        line-height: 1.7;
    }

    .btn-hero-report {
        background: #ffffff;
        color: #c40000;
        border: 0;
        border-radius: 999px;
        font-weight: 850;
        padding: 12px 22px;
        box-shadow: 0 14px 24px rgba(0,0,0,0.12);
        transition: 0.2s ease;
    }

    .btn-hero-report:hover {
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

    .stat-card {
        background: #ffffff;
        border: 1px solid #edf0f5;
        border-radius: 22px;
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.045);
        transition: 0.2s ease;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.07);
    }

    .stat-icon {
        width: 49px;
        height: 49px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .stat-number {
        font-size: 1.72rem;
        font-weight: 850;
        line-height: 1;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 0.72rem;
        color: #6b7280;
        font-weight: 850;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .toolbar-card {
        background: #ffffff;
        border: 1px solid #edf0f5;
        border-radius: 22px;
        padding: 14px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.04);
        margin-bottom: 20px;
    }

    .search-box {
        position: relative;
    }

    .search-box i {
        position: absolute;
        top: 50%;
        left: 16px;
        transform: translateY(-50%);
        color: #9ca3af;
    }

    .search-box input {
        border: 1px solid #edf0f5;
        background: #f9fafb;
        border-radius: 16px;
        padding: 12px 14px 12px 43px;
        font-weight: 600;
        font-size: 0.92rem;
    }

    .search-box input:focus {
        box-shadow: none;
        border-color: #ffd0d0;
        background: #ffffff;
    }

    .filter-chip {
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
    }

    .filter-chip:hover,
    .filter-chip.active {
        background: #cc0000;
        color: #ffffff;
        border-color: #cc0000;
        box-shadow: 0 10px 20px rgba(204, 0, 0, 0.18);
    }

    .facility-card {
        background: #ffffff;
        border-radius: 24px;
        border: 1px solid #edf0f5;
        overflow: hidden;
        box-shadow: 0 14px 35px rgba(15, 23, 42, 0.055);
        transition: 0.25s ease;
        height: 100%;
    }

    .facility-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 22px 45px rgba(15, 23, 42, 0.09);
    }

    .facility-image-wrap {
        position: relative;
        height: 210px;
        background: #f3f4f6;
        overflow: hidden;
    }

    .facility-image-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: 0.35s ease;
    }

    .facility-card:hover .facility-image-wrap img {
        transform: scale(1.04);
    }

    .image-gradient {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0.02) 40%, rgba(0,0,0,0.45) 100%);
    }

    .status-pill {
        position: absolute;
        top: 14px;
        right: 14px;
        padding: 7px 13px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 850;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        backdrop-filter: blur(10px);
    }

    .pill-pending {
        background: rgba(255, 247, 217, 0.94);
        color: #a16207;
    }

    .pill-progress {
        background: rgba(219, 234, 254, 0.94);
        color: #1d4ed8;
    }

    .pill-completed {
        background: rgba(220, 252, 231, 0.94);
        color: #15803d;
    }

    .facility-body {
        padding: 18px;
    }

    .facility-title {
        font-weight: 850;
        color: #111827;
        letter-spacing: -0.3px;
        margin-bottom: 8px;
        font-size: 1.02rem;
        text-transform: uppercase;
    }

    .facility-meta {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        color: #6b7280;
        font-size: 0.86rem;
        font-weight: 650;
        margin-bottom: 13px;
        line-height: 1.5;
    }

    .facility-desc {
        color: #6b7280;
        font-size: 0.88rem;
        line-height: 1.65;
        min-height: 44px;
        margin-bottom: 16px;
    }

    .reporter-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-top: 14px;
        border-top: 1px solid #f1f3f7;
    }

    .reporter-left {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .reporter-avatar {
        width: 34px;
        height: 34px;
        border-radius: 12px;
        background: linear-gradient(135deg, #cc0000, #750000);
        color: #ffffff;
        font-weight: 850;
        font-size: 0.78rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .reporter-name {
        font-size: 0.82rem;
        font-weight: 800;
        color: #374151;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 160px;
    }

    .report-date {
        font-size: 0.78rem;
        color: #9ca3af;
        font-weight: 700;
        white-space: nowrap;
    }

    .note-box {
        margin-top: 14px;
        padding: 12px;
        background: #f9fafb;
        border: 1px solid #edf0f5;
        border-radius: 16px;
        color: #6b7280;
        font-size: 0.83rem;
        line-height: 1.55;
    }

    .empty-state {
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

    .filtered-empty-state {
        background: #ffffff;
        border: 1px dashed #d1d5db;
        border-radius: 26px;
        padding: 58px 20px;
        text-align: center;
        color: #6b7280;
        width: 100%;
        min-height: 280px;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .filtered-empty-state:not([hidden]) {
        display: flex !important;
    }

    .filtered-empty-state[hidden] {
        display: none !important;
    }

    @media (max-width: 767px) {
        .facility-hero {
            padding: 24px 20px;
            border-radius: 24px;
        }

        .hero-title {
            font-size: 1.35rem;
        }

        .btn-hero-report {
            width: 100%;
            text-align: center;
        }

        .stat-card {
            padding: 15px;
        }

        .facility-image-wrap {
            height: 190px;
        }
    }
</style>
@endpush

@section('content')
@php
    $userName = auth()->check() ? auth()->user()->name : 'Mahasiswa Telkom';
@endphp

<div class="facility-hero">
    <div class="facility-hero-content">
        <div class="row align-items-center g-4">
            <div class="col-lg-8 col-12">
                <div class="hero-badge">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                    Facility Report System
                </div>
                <h1 class="hero-title">Halo, {{ $userName }} 👋</h1>
                <p class="hero-subtitle">
                    Ada infrastruktur atau fasilitas kelas bermasalah? Laporkan aduan kamu dengan cepat dan pantau progres perbaikannya melalui Campus Care.
                </p>
            </div>

            <div class="col-lg-4 col-12 text-lg-end">
                <a href="{{ route('facility.create') }}" class="btn btn-hero-report">
                    <i class="fa-solid fa-bullhorn me-2"></i>
                    Buat Aduan Baru
                </a>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 rounded-4 mb-4 shadow-sm small fw-semibold">
        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
    </div>
@endif

<div class="mb-4">
    <div class="section-heading">
        <h2 class="section-title">Ringkasan Laporan</h2>
    </div>

    <div class="row g-3">
        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div class="stat-icon" style="background:#eff6ff; color:#2563eb;">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <div>
                    <div class="stat-number text-dark">{{ $stats['total'] }}</div>
                    <div class="stat-label">Total Laporan</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div class="stat-icon" style="background:#fffbeb; color:#f59e0b;">
                    <i class="fa-solid fa-spinner"></i>
                </div>
                <div>
                    <div class="stat-number" style="color:#f59e0b;">{{ $stats['diproses'] }}</div>
                    <div class="stat-label">Diproses</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div class="stat-icon" style="background:#ecfdf5; color:#10b981;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <div class="stat-number" style="color:#10b981;">{{ $stats['selesai'] }}</div>
                    <div class="stat-label">Selesai</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="section-heading">
    <h2 class="section-title">Riwayat & Katalog Kerusakan</h2>
</div>

<div class="toolbar-card">
    <div class="row g-3 align-items-center">
        <div class="col-lg-6 col-12">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" class="form-control" id="searchReport" placeholder="Cari nama fasilitas, lokasi, atau deskripsi...">
            </div>
        </div>

        <div class="col-lg-6 col-12">
            <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                <button class="filter-chip active" type="button" data-filter="all">
                    <i class="fa-solid fa-layer-group"></i> Semua
                </button>
                <button class="filter-chip" type="button" data-filter="pending">
                    <i class="fa-regular fa-clock"></i> Menunggu
                </button>
                <button class="filter-chip" type="button" data-filter="in_progress">
                    <i class="fa-solid fa-spinner"></i> Diproses
                </button>
                <button class="filter-chip" type="button" data-filter="completed">
                    <i class="fa-solid fa-check"></i> Selesai
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row g-4" id="reportGrid">
    @forelse($reports as $report)
        @php
            $status = $report['status'] ?? 'pending';

            $statusLabel = match($status) {
                'in_progress' => 'Diproses',
                'completed' => 'Selesai',
                default => 'Menunggu',
            };

            $statusClass = match($status) {
                'in_progress' => 'pill-progress',
                'completed' => 'pill-completed',
                default => 'pill-pending',
            };

            $statusIcon = match($status) {
                'in_progress' => 'fa-spinner',
                'completed' => 'fa-check',
                default => 'fa-clock',
            };

            $reporterName = data_get($report, 'user.nama')
                ?? data_get($report, 'user.name')
                ?? data_get($report, 'reporter_name')
                ?? 'Mahasiswa';

            $words = collect(explode(' ', trim($reporterName)))->filter()->values();
            $initials = strtoupper(
                ($words->get(0) ? mb_substr($words->get(0), 0, 1) : 'M') .
                ($words->get(1) ? mb_substr($words->get(1), 0, 1) : '')
            );

            $roomText = trim(
                (data_get($report, 'room.gedung') ?? '-') .
                ' - Lt.' .
                (data_get($report, 'room.lantai') ?? '-') .
                ' (' .
                (data_get($report, 'room.nama_ruangan') ?? 'Umum') .
                ')'
            );

            $createdAt = data_get($report, 'created_at');

            try {
                $dateText = $createdAt ? \Carbon\Carbon::parse($createdAt)->format('d M Y') : '-';
            } catch (\Exception $e) {
                $dateText = '-';
            }

            $searchText = strtolower(
                ($report['nama_fasilitas'] ?? '') . ' ' .
                ($report['deskripsi_kerusakan'] ?? '') . ' ' .
                $roomText . ' ' .
                $reporterName . ' ' .
                $statusLabel
            );
        @endphp

        <div class="col-12 col-md-6 col-xl-4 report-item"
             data-status="{{ $status }}"
             data-search="{{ $searchText }}">
            <div class="facility-card">
                <div class="facility-image-wrap">
                    <img src="{{ $report['foto'] ?? 'https://placehold.co/700x420/f3f4f6/9ca3af?text=No+Image+Available' }}"
                         alt="{{ $report['nama_fasilitas'] ?? 'Laporan Fasilitas' }}"
                         onerror="this.onerror=null; this.src='https://placehold.co/700x420/f3f4f6/9ca3af?text=No+Image+Available';">
                    <div class="image-gradient"></div>

                    <span class="status-pill {{ $statusClass }}">
                        <i class="fa-solid {{ $statusIcon }}"></i>
                        {{ $statusLabel }}
                    </span>
                </div>

                <div class="facility-body">
                    <h3 class="facility-title">{{ $report['nama_fasilitas'] ?? '-' }}</h3>

                    <div class="facility-meta">
                        <i class="fa-solid fa-location-dot text-danger mt-1"></i>
                        <span>{{ $roomText }}</span>
                    </div>

                    <p class="facility-desc">
                        {{ $report['deskripsi_kerusakan'] ?? '-' }}
                    </p>

                    @if(!empty($report['catatan_teknisi']))
                        <div class="note-box">
                            <strong class="text-dark d-block mb-1">
                                <i class="fa-solid fa-comment-dots me-1"></i>
                                Catatan Perbaikan
                            </strong>
                            <span>"{{ $report['catatan_teknisi'] }}"</span>
                        </div>
                    @endif

                    <div class="reporter-row">
                        <div class="reporter-left">
                            <div class="reporter-avatar">{{ $initials }}</div>
                            <div class="min-w-0">
                                <div class="reporter-name">{{ $reporterName }}</div>
                                <div class="text-muted small fw-semibold">
                                    {{ data_get($report, 'user.nim') ?? 'Pelapor' }}
                                </div>
                            </div>
                        </div>

                        <div class="report-date">
                            <i class="fa-regular fa-calendar me-1"></i>
                            {{ $dateText }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Belum ada laporan fasilitas</h5>
                <p class="m-0">Belum ada laporan fasilitas rusak yang terdata saat ini.</p>
            </div>
        </div>
    @endforelse
</div>

<div id="emptyFilteredState" class="filtered-empty-state mt-4" hidden>
    <div class="empty-icon">
        <i class="fa-solid fa-magnifying-glass"></i>
    </div>
    <h5 class="fw-bold text-dark mb-2">Laporan tidak ditemukan</h5>
    <p class="m-0">Coba gunakan kata kunci atau filter status yang berbeda.</p>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchReport');
        const filterButtons = document.querySelectorAll('.filter-chip');
        const reportItems = document.querySelectorAll('.report-item');
        const emptyFilteredState = document.getElementById('emptyFilteredState');

        let activeFilter = 'all';

        function applyFilter() {
            const keyword = (searchInput?.value || '').toLowerCase().trim();
            let visibleCount = 0;

            reportItems.forEach(item => {
                const status = item.dataset.status;
                const text = item.dataset.search || '';

                const matchStatus = activeFilter === 'all' || status === activeFilter;
                const matchSearch = keyword === '' || text.includes(keyword);

                if (matchStatus && matchSearch) {
                    item.classList.remove('d-none');
                    visibleCount++;
                } else {
                    item.classList.add('d-none');
                }
            });

            if (emptyFilteredState) {
                emptyFilteredState.hidden = !(visibleCount === 0 && reportItems.length > 0);
            }
        }

        filterButtons.forEach(button => {
            button.addEventListener('click', function () {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                activeFilter = this.dataset.filter || 'all';
                applyFilter();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', applyFilter);
        }

        applyFilter();
    });
</script>
@endpush