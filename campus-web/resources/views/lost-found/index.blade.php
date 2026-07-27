@extends('layouts.app')

@section('title', 'Katalog Penemuan - Campus Care')
@section('role_text', session('nim') ?? 'Mahasiswa Telkom')

@push('styles')
<style>
    .found-hero {
        background:
            radial-gradient(circle at 90% 18%, rgba(255,255,255,0.22), transparent 28%),
            linear-gradient(135deg, #d40000 0%, #b50000 48%, #760000 100%);
        border-radius: 30px;
        color: #ffffff;
        padding: 32px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 22px 48px rgba(204, 0, 0, 0.18);
        margin-bottom: 26px;
    }

    .found-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,0.07) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.07) 1px, transparent 1px);
        background-size: 34px 34px;
        opacity: 0.32;
    }

    .found-hero-content {
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

    .found-title {
        font-size: 1.85rem;
        font-weight: 850;
        letter-spacing: -0.9px;
        margin-bottom: 8px;
    }

    .found-subtitle {
        color: rgba(255,255,255,0.76);
        font-weight: 500;
        max-width: 780px;
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
        white-space: nowrap;
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
        height: 100%;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.045);
        transition: 0.2s ease;
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
        padding: 15px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.04);
        margin-bottom: 22px;
        position: relative;
        z-index: 10;
    }

    .custom-input {
        background: #f9fafb;
        border: 1px solid #edf0f5;
        border-radius: 16px;
        padding: 12px 14px;
        font-size: 0.92rem;
        font-weight: 600;
        transition: 0.2s ease;
    }

    .custom-input:focus {
        background: #ffffff;
        border-color: #ffd0d0;
        box-shadow: 0 0 0 4px rgba(204, 0, 0, 0.07);
        outline: none;
    }

    .search-field {
        position: relative;
    }

    .search-field i {
        position: absolute;
        top: 50%;
        left: 16px;
        transform: translateY(-50%);
        color: #9ca3af;
        z-index: 2;
    }

    .search-field input {
        padding-left: 43px;
    }

    .cc-dropdown {
        position: relative;
    }

    .cc-dropdown-button {
        width: 100%;
        background: #f9fafb;
        border: 1px solid #edf0f5;
        border-radius: 16px;
        padding: 12px 14px;
        font-size: 0.92rem;
        font-weight: 700;
        color: #111827;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        transition: 0.2s ease;
        min-height: 48px;
    }

    .cc-dropdown-button:hover,
    .cc-dropdown.open .cc-dropdown-button {
        background: #ffffff;
        border-color: #ffd0d0;
        box-shadow: 0 0 0 4px rgba(204, 0, 0, 0.06);
    }

    .cc-dropdown-button span {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .cc-dropdown-button i:last-child {
        color: #9ca3af;
        transition: 0.2s ease;
    }

    .cc-dropdown.open .cc-dropdown-button i:last-child {
        transform: rotate(180deg);
        color: #cc0000;
    }

    .cc-dropdown-menu {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        right: 0;
        background: #ffffff;
        border: 1px solid #edf0f5;
        border-radius: 18px;
        box-shadow: 0 22px 48px rgba(15, 23, 42, 0.13);
        padding: 8px;
        display: none;
        z-index: 3000;
        max-height: 260px;
        overflow-y: auto;
    }

    .cc-dropdown.open .cc-dropdown-menu {
        display: block;
    }

    .cc-dropdown-item {
        width: 100%;
        border: 0;
        background: transparent;
        color: #4b5563;
        padding: 11px 12px;
        border-radius: 13px;
        font-size: 0.88rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
        text-align: left;
        transition: 0.18s ease;
    }

    .cc-dropdown-item:hover {
        background: #fff4f4;
        color: #cc0000;
    }

    .cc-dropdown-item.active {
        background: linear-gradient(135deg, #fff0f0, #ffe6e6);
        color: #cc0000;
    }

    .cc-dropdown-item i {
        width: 18px;
        text-align: center;
        flex-shrink: 0;
    }

    .btn-filter {
        background: #111827;
        color: #ffffff;
        border: 0;
        border-radius: 999px;
        font-weight: 850;
        padding: 12px 18px;
        width: 100%;
        transition: 0.2s ease;
        min-height: 48px;
    }

    .btn-filter:hover {
        background: #cc0000;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .found-card {
        background: #ffffff;
        border-radius: 24px;
        border: 1px solid #edf0f5;
        overflow: hidden;
        box-shadow: 0 14px 35px rgba(15, 23, 42, 0.055);
        transition: 0.25s ease;
        height: 100%;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
    }

    .found-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 22px 45px rgba(15, 23, 42, 0.09);
        color: inherit;
    }

    .found-image-wrap {
        height: 210px;
        background: #f3f4f6;
        position: relative;
        overflow: hidden;
    }

    .found-image-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: 0.35s ease;
    }

    .found-card:hover .found-image-wrap img {
        transform: scale(1.04);
    }

    .image-gradient {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0.02) 42%, rgba(0,0,0,0.42) 100%);
    }

    .status-badge-found {
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

    .status-available {
        background: rgba(255, 255, 255, 0.95);
        color: #cc0000;
        border: 1px solid rgba(255, 204, 204, 0.8);
    }

    .status-process {
        background: rgba(255, 247, 217, 0.95);
        color: #a16207;
        border: 1px solid rgba(253, 230, 138, 0.8);
    }

    .status-claimed {
        background: rgba(31, 41, 55, 0.94);
        color: #ffffff;
        border: 1px solid rgba(31, 41, 55, 0.3);
    }

    .found-card-body {
        padding: 18px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .category-tag {
        background: #f1f5f9;
        color: #475569;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 850;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        align-self: flex-start;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-bottom: 12px;
    }

    .found-card-title {
        font-size: 1rem;
        font-weight: 850;
        color: #111827;
        margin-bottom: 8px;
        line-height: 1.35;
    }

    .found-description {
        color: #6b7280;
        font-size: 0.86rem;
        line-height: 1.6;
        margin-bottom: 14px;
        min-height: 42px;
    }

    .found-meta {
        margin-top: auto;
        padding-top: 14px;
        border-top: 1px solid #f1f3f7;
        color: #9ca3af;
        font-size: 0.78rem;
        font-weight: 700;
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .found-meta-row {
        display: flex;
        align-items: center;
        gap: 7px;
        min-width: 0;
    }

    .found-meta-row span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .empty-found-state {
        background: #ffffff;
        border: 1px dashed #d1d5db;
        border-radius: 26px;
        padding: 58px 20px;
        text-align: center;
        color: #6b7280;
        width: 100%;
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

    .modal-content-modern {
        border: 0;
        border-radius: 26px;
        overflow: hidden;
        box-shadow: 0 28px 70px rgba(15, 23, 42, 0.18);
    }

    .modal-header-modern {
        padding: 24px 26px 8px;
        border: 0;
    }

    .modal-title-modern {
        font-weight: 850;
        color: #111827;
        letter-spacing: -0.4px;
        margin: 0;
    }

    .modal-subtitle {
        color: #6b7280;
        font-size: 0.88rem;
        margin-top: 5px;
    }

    .type-switch-box {
        background: #f9fafb;
        border: 1px solid #edf0f5;
        border-radius: 18px;
        padding: 9px;
    }

    .type-switch-box .btn {
        border-radius: 13px;
        font-weight: 850;
        font-size: 0.86rem;
        padding: 11px 12px;
    }

    .upload-box-premium {
        border: 2px dashed #d1d5db;
        background: #f9fafb;
        border-radius: 18px;
        padding: 22px;
        text-align: center;
        position: relative;
        cursor: pointer;
        transition: 0.2s ease;
        min-height: 128px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .upload-box-premium:hover {
        background: #fff5f5;
        border-color: #cc0000;
    }

    .hidden-file {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .btn-submit-report {
        background: linear-gradient(135deg, #cc0000 0%, #aa0000 100%);
        border: none;
        color: #ffffff;
        font-weight: 850;
        border-radius: 999px;
        padding: 13px 18px;
        letter-spacing: 0.04em;
        transition: 0.2s ease;
    }

    .btn-submit-report:hover {
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 14px 28px rgba(204,0,0,0.18);
    }

    @media (max-width: 767px) {
        .found-hero {
            padding: 26px 22px;
            border-radius: 24px;
        }

        .found-title {
            font-size: 1.42rem;
        }

        .btn-hero-report {
            width: 100%;
            text-align: center;
        }

        .stat-card {
            padding: 15px;
        }

        .found-image-wrap {
            height: 190px;
        }
    }
</style>
@endpush

@section('content')
@php
    $categoryLabel = function ($category) {
        return match($category) {
            'electronic' => 'Elektronik / Gawai',
            'document' => 'Dokumen / Kartu / KTM',
            'accessory' => 'Aksesori / Pakaian / Lainnya',
            default => $category ? ucfirst($category) : 'Semua Kategori',
        };
    };

    $statusLabel = function ($status) {
        return match($status) {
            'available' => 'Tersedia',
            'pending_publish' => 'Review Satpam',
            'claim_pending' => 'Pengajuan Klaim',
            'claim_scheduled' => 'Tunggu Diambil',
            'claimed' => 'Selesai',
            'all' => 'Semua Status',
            default => ucfirst(str_replace('_', ' ', $status ?? 'tersedia')),
        };
    };

    $statusClass = function ($status) {
        return match($status) {
            'available' => 'status-available',
            'pending_publish', 'claim_pending', 'claim_scheduled' => 'status-process',
            'claimed' => 'status-claimed',
            default => 'status-available',
        };
    };

    $selectedCategory = request('category', '');
    $selectedStatus = request('status', 'available');

    $selectedCategoryText = $selectedCategory ? $categoryLabel($selectedCategory) : 'Semua Kategori';
    $selectedStatusText = $statusLabel($selectedStatus);
@endphp

<div class="found-hero">
    <div class="found-hero-content">
        <div class="row align-items-center g-4">
            <div class="col-lg-8 col-12">
                <div class="hero-badge">
                    <i class="fa-solid fa-box-open"></i>
                    Found Item Center
                </div>

                <h1 class="found-title">Katalog Barang Temuan</h1>
                <p class="found-subtitle">
                    Daftar barang yang diamankan oleh petugas Satpam. Cek katalog ini jika kamu merasa kehilangan barang di area kampus.
                </p>
            </div>

            <div class="col-lg-4 col-12 text-lg-end">
                <button class="btn btn-hero-report" data-bs-toggle="modal" data-bs-target="#laporModal">
                    <i class="fa-solid fa-bullhorn me-2"></i>
                    Buat Laporan Baru
                </button>
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
        <h2 class="section-title">Ringkasan Lost & Found</h2>
    </div>

    <div class="row g-3">
        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div class="stat-icon" style="background:#fff1f1; color:#cc0000;">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <div>
                    <div class="stat-number text-dark">{{ $stats['total_found'] ?? 0 }}</div>
                    <div class="stat-label">Barang Temuan</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div class="stat-icon" style="background:#fff7ed; color:#f97316;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <div>
                    <div class="stat-number" style="color:#f97316;">{{ $stats['total_lost'] ?? 0 }}</div>
                    <div class="stat-label">Sedang Dicari</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div class="stat-icon" style="background:#eff6ff; color:#2563eb;">
                    <i class="fa-solid fa-user-clock"></i>
                </div>
                <div>
                    <div class="stat-number" style="color:#2563eb;">{{ $stats['my_reports'] ?? 0 }}</div>
                    <div class="stat-label">Laporan Saya</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="section-heading">
    <h2 class="section-title">Daftar Barang Temuan</h2>
</div>

<div class="toolbar-card">
    <form action="{{ route('lost-found.index') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-lg-5 col-12">
            <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size:0.68rem;">
                Cari Judul Barang
            </label>
            <div class="search-field">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input
                    type="text"
                    name="search"
                    class="form-control custom-input"
                    placeholder="Kunci, tumbler, KTM, dompet, dll..."
                    value="{{ request('search') }}"
                >
            </div>
        </div>

        <div class="col-lg-3 col-12">
            <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size:0.68rem;">
                Kategori
            </label>

            <input type="hidden" name="category" id="categoryInput" value="{{ $selectedCategory }}">

            <div class="cc-dropdown" data-target="categoryInput">
                <button type="button" class="cc-dropdown-button">
                    <span>
                        <i class="fa-solid fa-tag text-danger"></i>
                        <span class="cc-dropdown-label">{{ $selectedCategoryText }}</span>
                    </span>
                    <i class="fa-solid fa-chevron-down"></i>
                </button>

                <div class="cc-dropdown-menu">
                    <button type="button" class="cc-dropdown-item {{ $selectedCategory === '' ? 'active' : '' }}" data-value="" data-label="Semua Kategori">
                        <i class="fa-solid fa-layer-group"></i>
                        Semua Kategori
                    </button>
                    <button type="button" class="cc-dropdown-item {{ $selectedCategory === 'electronic' ? 'active' : '' }}" data-value="electronic" data-label="Elektronik / Gawai">
                        <i class="fa-solid fa-mobile-screen"></i>
                        Elektronik / Gawai
                    </button>
                    <button type="button" class="cc-dropdown-item {{ $selectedCategory === 'document' ? 'active' : '' }}" data-value="document" data-label="Dokumen / Kartu / KTM">
                        <i class="fa-solid fa-id-card"></i>
                        Dokumen / Kartu / KTM
                    </button>
                    <button type="button" class="cc-dropdown-item {{ $selectedCategory === 'accessory' ? 'active' : '' }}" data-value="accessory" data-label="Aksesori / Pakaian / Lainnya">
                        <i class="fa-solid fa-shirt"></i>
                        Aksesori / Pakaian / Lainnya
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-12">
            <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size:0.68rem;">
                Status Arsip
            </label>

            <input type="hidden" name="status" id="statusInput" value="{{ $selectedStatus }}">

            <div class="cc-dropdown" data-target="statusInput">
                <button type="button" class="cc-dropdown-button">
                    <span>
                        <i class="fa-solid fa-circle-check text-danger"></i>
                        <span class="cc-dropdown-label">{{ $selectedStatusText }}</span>
                    </span>
                    <i class="fa-solid fa-chevron-down"></i>
                </button>

                <div class="cc-dropdown-menu">
                    <button type="button" class="cc-dropdown-item {{ $selectedStatus === 'available' ? 'active' : '' }}" data-value="available" data-label="Tersedia">
                        <i class="fa-solid fa-box-open"></i>
                        Tersedia
                    </button>
                    <button type="button" class="cc-dropdown-item {{ $selectedStatus === 'claim_pending' ? 'active' : '' }}" data-value="claim_pending" data-label="Pengajuan Klaim">
                        <i class="fa-solid fa-hand-pointer"></i>
                        Pengajuan Klaim
                    </button>
                    <button type="button" class="cc-dropdown-item {{ $selectedStatus === 'claim_scheduled' ? 'active' : '' }}" data-value="claim_scheduled" data-label="Tunggu Diambil">
                        <i class="fa-solid fa-calendar-check"></i>
                        Tunggu Diambil
                    </button>
                    <button type="button" class="cc-dropdown-item {{ $selectedStatus === 'claimed' ? 'active' : '' }}" data-value="claimed" data-label="Selesai">
                        <i class="fa-solid fa-check"></i>
                        Selesai
                    </button>
                    <button type="button" class="cc-dropdown-item {{ $selectedStatus === 'all' ? 'active' : '' }}" data-value="all" data-label="Semua Status">
                        <i class="fa-solid fa-layer-group"></i>
                        Semua Status
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-12">
            <button type="submit" class="btn-filter">
                <i class="fa-solid fa-filter me-1"></i>
                Filter
            </button>
        </div>
    </form>
</div>

<div class="row g-4">
    @forelse($items as $item)
        @php
            $photo = $item->photo ?? null;

            if ($photo && str_starts_with((string) $photo, 'http')) {
                $photoUrl = $photo;
            } elseif ($photo) {
                $photoUrl = asset('storage/' . $photo);
            } else {
                $photoUrl = 'https://placehold.co/700x420/f3f4f6/9ca3af?text=No+Photo';
            }

            try {
                $dateText = $item->created_at
                    ? \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y')
                    : '-';
            } catch (\Exception $e) {
                $dateText = '-';
            }
        @endphp

        <div class="col-12 col-md-6 col-xl-3">
            <a href="{{ route('lost-found.show', $item->id) }}" class="found-card">
                <div class="found-image-wrap">
                    <img
                        src="{{ $photoUrl }}"
                        alt="{{ $item->name ?? 'Barang Temuan' }}"
                        onerror="this.onerror=null; this.src='https://placehold.co/700x420/f3f4f6/9ca3af?text=No+Photo';"
                    >
                    <div class="image-gradient"></div>

                    <span class="status-badge-found {{ $statusClass($item->status ?? 'available') }}">
                        <i class="fa-solid fa-box"></i>
                        {{ $statusLabel($item->status ?? 'available') }}
                    </span>
                </div>

                <div class="found-card-body">
                    <span class="category-tag">
                        <i class="fa-solid fa-tag"></i>
                        {{ $categoryLabel($item->category ?? null) }}
                    </span>

                    <h3 class="found-card-title text-truncate">
                        {{ $item->name ?? 'Barang tanpa nama' }}
                    </h3>

                    <p class="found-description">
                        {{ \Illuminate\Support\Str::limit($item->description ?? 'Tidak ada keterangan detail.', 92) }}
                    </p>

                    <div class="found-meta">
                        <div class="found-meta-row">
                            <i class="fa-solid fa-location-dot text-danger"></i>
                            <span>{{ $item->location ?? 'Lokasi belum tersedia' }}</span>
                        </div>

                        <div class="found-meta-row">
                            <i class="fa-regular fa-calendar"></i>
                            <span>Diamankan {{ $dateText }}</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @empty
        <div class="col-12">
            <div class="empty-found-state">
                <div class="empty-icon">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Data barang temuan tidak ditemukan</h5>
                <p class="m-0">
                    Belum ada barang temuan yang sesuai dengan kriteria filter saat ini.
                </p>
            </div>
        </div>
    @endforelse
</div>

@if(method_exists($items, 'links'))
    <div class="mt-4 d-flex justify-content-center">
        {{ $items->links() }}
    </div>
@endif

<div class="modal fade" id="laporModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-content-modern">
            <form action="{{ route('lost-found.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header modal-header-modern d-flex align-items-start justify-content-between">
                    <div>
                        <h5 class="modal-title-modern">
                            <i class="fa-solid fa-folder-plus text-danger me-2"></i>
                            Formulir Laporan Lost & Found
                        </h5>
                        <p class="modal-subtitle mb-0">
                            Pilih jenis laporan, lengkapi detail barang, lalu kirim untuk diverifikasi.
                        </p>
                    </div>

                    <button type="button" class="btn-close mt-1" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 p-md-4">
                    <div class="type-switch-box mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2 d-block" style="letter-spacing:0.5px;">
                            Jenis Informasi
                        </label>

                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="type" id="typeFound" value="found" checked onchange="toggleLostFoundFormIndex()">
                            <label class="btn btn-outline-danger" for="typeFound">
                                <i class="fa-solid fa-hand-holding-hand me-1"></i>
                                Saya Menemukan Barang
                            </label>

                            <input type="radio" class="btn-check" name="type" id="typeLost" value="lost" onchange="toggleLostFoundFormIndex()">
                            <label class="btn btn-outline-danger" for="typeLost">
                                <i class="fa-solid fa-magnifying-glass me-1"></i>
                                Saya Kehilangan Barang
                            </label>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">
                                Nama Objek Barang *
                            </label>
                            <input type="text" name="name" class="form-control custom-input" placeholder="Contoh: Dompet Eiger Hitam, Airpods Pro" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">
                                Kategori Barang *
                            </label>

                            <input type="hidden" name="category" id="modalCategoryInputFound" value="electronic">

                            <div class="cc-dropdown" data-target="modalCategoryInputFound">
                                <button type="button" class="cc-dropdown-button">
                                    <span>
                                        <i class="fa-solid fa-mobile-screen text-danger"></i>
                                        <span class="cc-dropdown-label">Elektronik / Gawai</span>
                                    </span>
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>

                                <div class="cc-dropdown-menu">
                                    <button type="button" class="cc-dropdown-item active" data-value="electronic" data-label="Elektronik / Gawai">
                                        <i class="fa-solid fa-mobile-screen"></i>
                                        Elektronik / Gawai
                                    </button>

                                    <button type="button" class="cc-dropdown-item" data-value="document" data-label="Dokumen / Kartu / KTM">
                                        <i class="fa-solid fa-id-card"></i>
                                        Dokumen / Kartu / KTM
                                    </button>

                                    <button type="button" class="cc-dropdown-item" data-value="accessory" data-label="Aksesori / Pakaian / Lainnya">
                                        <i class="fa-solid fa-shirt"></i>
                                        Aksesori / Pakaian / Lainnya
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-12" id="divLokasi">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">
                                Lokasi Penemuan *
                            </label>
                            <input type="text" name="location" id="inputLokasi" class="form-control custom-input" placeholder="Contoh: Meja kantin gedung A, kursi RB 2.3">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">
                                Ciri-Ciri / Deskripsi Tambahan
                            </label>
                            <textarea name="description" class="form-control custom-input" rows="3" placeholder="Sebutkan ciri unik seperti stiker, lokasi terakhir, warna, merek, atau kondisi fisik barang..." style="resize:none;"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">
                                Foto Kondisi Barang *
                            </label>
                            <div class="upload-box-premium">
                                <i class="fa-solid fa-image fs-3 mb-2 text-secondary"></i>
                                <div class="small fw-bold text-dark text-truncate" id="txtPhoto">Upload Foto Objek</div>
                                <div class="small text-muted mt-1">JPG, PNG, atau JPEG</div>
                                <input type="file" name="photo" id="inPhoto" class="hidden-file" accept="image/*" required>
                            </div>
                        </div>

                        <div class="col-md-6" id="identityUploadWrapper" style="display:none;">
                            <label class="form-label small fw-bold text-danger text-uppercase mb-1" id="identityUploadLabel">
                                Foto KTM / KTP Pelapor *
                            </label>
                            <div class="upload-box-premium" style="border-color:#ffccd2;">
                                <i class="fa-solid fa-id-card fs-3 mb-2 text-danger opacity-75"></i>
                                <div class="small fw-bold text-danger text-truncate" id="txtId">Upload Tanda Pengenal</div>
                                <div class="small text-muted mt-1">Hanya wajib untuk laporan kehilangan</div>
                                <input type="file" name="reporter_id_photo" id="inId" class="hidden-file" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-submit-report w-100 text-uppercase">
                        <i class="fa-solid fa-paper-plane me-2"></i>
                        Kirim Formulir Aduan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleLostFoundFormIndex() {
        const selectedType = document.querySelector('input[name="type"]:checked')?.value;

        const divLokasi = document.getElementById('divLokasi');
        const inputLokasi = document.getElementById('inputLokasi');

        const identityWrapper = document.getElementById('identityUploadWrapper');
        const identityInput = document.getElementById('inId');
        const identityLabel = document.getElementById('identityUploadLabel');
        const identityText = document.getElementById('txtId');

        if (!divLokasi || !inputLokasi) return;

        if (selectedType === 'lost') {
            divLokasi.style.display = 'none';
            inputLokasi.removeAttribute('required');
            inputLokasi.value = '';

            if (identityWrapper) identityWrapper.style.display = 'block';
            if (identityInput) identityInput.setAttribute('required', 'required');
            if (identityLabel) identityLabel.innerText = 'Foto KTM / KTP Pelapor *';
        } else {
            divLokasi.style.display = 'block';
            inputLokasi.setAttribute('required', 'required');

            if (identityWrapper) identityWrapper.style.display = 'none';
            if (identityInput) {
                identityInput.removeAttribute('required');
                identityInput.value = '';
            }
            if (identityText) identityText.innerText = 'Upload Tanda Pengenal';
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.cc-dropdown').forEach(function (dropdown) {
            const button = dropdown.querySelector('.cc-dropdown-button');
            const label = dropdown.querySelector('.cc-dropdown-label');
            const items = dropdown.querySelectorAll('.cc-dropdown-item');
            const targetId = dropdown.dataset.target;
            const input = document.getElementById(targetId);

            button?.addEventListener('click', function (event) {
                event.stopPropagation();

                document.querySelectorAll('.cc-dropdown').forEach(function (other) {
                    if (other !== dropdown) {
                        other.classList.remove('open');
                    }
                });

                dropdown.classList.toggle('open');
            });

            items.forEach(function (item) {
                item.addEventListener('click', function () {
                    const value = this.dataset.value || '';
                    const text = this.dataset.label || this.innerText.trim();

                    if (input) input.value = value;
                    if (label) label.innerText = text;

                    items.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    dropdown.classList.remove('open');
                });
            });
        });

        document.addEventListener('click', function () {
            document.querySelectorAll('.cc-dropdown').forEach(function (dropdown) {
                dropdown.classList.remove('open');
            });
        });

        const inPhoto = document.getElementById('inPhoto');
        const inId = document.getElementById('inId');

        if (inPhoto) {
            inPhoto.addEventListener('change', function () {
                if (this.files.length > 0) {
                    document.getElementById('txtPhoto').innerText = this.files[0].name;
                }
            });
        }

        if (inId) {
            inId.addEventListener('change', function () {
                if (this.files.length > 0) {
                    document.getElementById('txtId').innerText = this.files[0].name;
                }
            });
        }

        toggleLostFoundFormIndex();
    });
</script>
@endpush