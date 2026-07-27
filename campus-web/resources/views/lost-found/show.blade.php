@extends('layouts.app')

@section('title', ($item->name ?? 'Detail Barang') . ' - Campus Care')

@push('styles')
<style>
    .detail-page {
        animation: fadeUp 0.32s ease-out;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        color: #374151;
        text-decoration: none;
        font-weight: 850;
        font-size: 0.9rem;
        margin-bottom: 20px;
        transition: 0.2s ease;
    }

    .back-link:hover {
        color: #cc0000;
        transform: translateX(-2px);
    }

    .detail-card {
        background: #ffffff;
        border: 1px solid #edf0f5;
        border-radius: 30px;
        overflow: hidden;
        box-shadow: 0 18px 44px rgba(15, 23, 42, 0.065);
    }

    .image-panel {
        padding: 26px;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        min-height: 520px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .image-frame {
        width: 100%;
        height: 100%;
        min-height: 450px;
        border-radius: 26px;
        background: #f3f4f6;
        overflow: hidden;
        border: 1px solid #edf0f5;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .image-frame img {
        width: 100%;
        height: 100%;
        max-height: 520px;
        object-fit: contain;
        display: block;
    }

    .detail-panel {
        padding: 42px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 520px;
    }

    .item-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        width: fit-content;
        padding: 8px 13px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 850;
        margin-bottom: 16px;
    }

    .type-found {
        background: #fff1f1;
        color: #cc0000;
        border: 1px solid #ffd7d7;
    }

    .type-lost {
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #fed7aa;
    }

    .detail-title {
        font-size: 2rem;
        font-weight: 850;
        color: #111827;
        letter-spacing: -0.9px;
        margin-bottom: 14px;
        line-height: 1.2;
    }

    .badge-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 9px;
        margin-bottom: 24px;
    }

    .soft-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 13px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 850;
        border: 1px solid transparent;
    }

    .badge-available {
        background: #fff1f1;
        color: #cc0000;
        border-color: #ffd7d7;
    }

    .badge-process {
        background: #fffbeb;
        color: #a16207;
        border-color: #fde68a;
    }

    .badge-claimed {
        background: #f3f4f6;
        color: #374151;
        border-color: #e5e7eb;
    }

    .badge-category {
        background: #f8fafc;
        color: #475569;
        border-color: #e2e8f0;
    }

    .info-block {
        margin-bottom: 22px;
    }

    .info-label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6b7280;
        font-size: 0.73rem;
        font-weight: 850;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 8px;
    }

    .info-label i {
        color: #cc0000;
    }

    .info-value {
        color: #111827;
        font-size: 0.96rem;
        line-height: 1.75;
        font-weight: 600;
    }

    .notice-box {
        border-radius: 22px;
        padding: 18px;
        margin-top: 8px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        border: 1px solid transparent;
    }

    .notice-warning {
        background: #fff9e6;
        color: #8a6d3b;
        border-color: #fde68a;
    }

    .notice-info {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }

    .notice-box i {
        font-size: 1.25rem;
        margin-top: 3px;
        flex-shrink: 0;
    }

    .notice-title {
        display: block;
        font-weight: 850;
        margin-bottom: 4px;
        color: inherit;
    }

    .notice-text {
        font-size: 0.88rem;
        line-height: 1.65;
        font-weight: 600;
        opacity: 0.92;
    }

    .btn-claim {
        width: 100%;
        border: none;
        border-radius: 999px;
        padding: 14px 20px;
        background: linear-gradient(135deg, #cc0000 0%, #a70000 100%);
        color: #ffffff;
        font-weight: 850;
        box-shadow: 0 16px 30px rgba(204, 0, 0, 0.2);
        transition: 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
    }

    .btn-claim:hover {
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 20px 36px rgba(204, 0, 0, 0.28);
    }

    .btn-disabled {
        width: 100%;
        border-radius: 999px;
        padding: 14px 20px;
        background: #f3f4f6;
        color: #6b7280;
        border: 1px solid #e5e7eb;
        font-weight: 850;
        cursor: not-allowed;
    }

    .detail-side-meta {
        background: #f9fafb;
        border: 1px solid #edf0f5;
        border-radius: 22px;
        padding: 16px;
        margin-bottom: 24px;
    }

    .meta-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid #edf0f5;
    }

    .meta-row:last-child {
        border-bottom: 0;
    }

    .meta-key {
        color: #6b7280;
        font-size: 0.78rem;
        font-weight: 800;
    }

    .meta-value {
        color: #111827;
        font-size: 0.82rem;
        font-weight: 800;
        text-align: right;
    }

    .modal-content-modern {
        border: 0;
        border-radius: 26px;
        overflow: hidden;
        box-shadow: 0 30px 80px rgba(15, 23, 42, 0.22);
    }

    .modal-header-modern {
        padding: 24px 26px 10px;
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
        line-height: 1.55;
    }

    .form-label-modern {
        font-size: 0.73rem;
        font-weight: 850;
        color: #4b5563;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
    }

    .custom-input {
        background: #f9fafb;
        border: 1px solid #edf0f5;
        border-radius: 16px;
        padding: 12px 14px;
        font-size: 0.92rem;
        font-weight: 650;
        transition: 0.2s ease;
    }

    .custom-input:focus {
        background: #ffffff;
        border-color: #ffd0d0;
        box-shadow: 0 0 0 4px rgba(204, 0, 0, 0.07);
        outline: none;
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

    .upload-box-premium.is-filled {
        background: #f0fdf4;
        border-color: #10b981;
    }

    .hidden-file {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .btn-submit-claim {
        width: 100%;
        border: none;
        border-radius: 999px;
        padding: 13px 20px;
        background: linear-gradient(135deg, #cc0000 0%, #aa0000 100%);
        color: #ffffff;
        font-weight: 850;
        letter-spacing: 0.03em;
        transition: 0.2s ease;
    }

    .btn-submit-claim:hover {
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 14px 28px rgba(204, 0, 0, 0.2);
    }

    .alert-modern {
        border: 0;
        border-radius: 18px;
        padding: 14px 16px;
        font-size: 0.88rem;
        font-weight: 650;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 991px) {
        .detail-panel {
            padding: 28px;
            min-height: auto;
        }

        .image-panel {
            min-height: auto;
        }

        .image-frame {
            min-height: 320px;
        }

        .detail-title {
            font-size: 1.55rem;
        }
    }

    @media (max-width: 575px) {
        .detail-card {
            border-radius: 24px;
        }

        .image-panel {
            padding: 16px;
        }

        .detail-panel {
            padding: 22px;
        }
    }
</style>
@endpush

@section('content')
@php
    $user = Auth::user();
    $isStaff = $user && ((method_exists($user, 'isSatpam') && $user->isSatpam()) || ($user->role ?? null) === 'admin');

    $photo = $item->photo ?? null;

    if ($photo && \Illuminate\Support\Str::startsWith($photo, ['http://', 'https://'])) {
        $photoUrl = str_replace(
            ['http://127.0.0.1:8000/storage/', 'http://localhost:8000/storage/'],
            asset('storage') . '/',
            $photo
        );
    } elseif ($photo && \Illuminate\Support\Str::startsWith($photo, '/storage/')) {
        $photoUrl = asset(ltrim($photo, '/'));
    } elseif ($photo) {
        $photoUrl = asset('image/' . ltrim($photo, '/'));
    } else {
        $photoUrl = 'https://placehold.co/900x620/f3f4f6/9ca3af?text=No+Photo';
    }

    $categoryLabel = match($item->category) {
        'electronic' => 'Elektronik / Gawai',
        'document' => 'Dokumen / Kartu / KTM',
        'accessory' => 'Aksesori / Pakaian / Lainnya',
        default => ucfirst($item->category ?? 'Lainnya'),
    };

    $statusLabel = match($item->status) {
        'available' => 'Tersedia',
        'pending_publish' => 'Menunggu Verifikasi',
        'claim_pending' => 'Pengajuan Klaim',
        'claim_scheduled' => 'Jadwal Pengambilan',
        'claimed' => 'Selesai',
        default => ucfirst(str_replace('_', ' ', $item->status ?? 'Tersedia')),
    };

    $statusClass = match($item->status) {
        'available' => 'badge-available',
        'pending_publish', 'claim_pending', 'claim_scheduled' => 'badge-process',
        'claimed' => 'badge-claimed',
        default => 'badge-available',
    };

    try {
        $createdDate = $item->created_at ? \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y') : '-';
    } catch (\Exception $e) {
        $createdDate = '-';
    }

    $backRoute = $item->type === 'lost' ? route('lost-found.lost.index') : route('lost-found.index');
@endphp

<div class="detail-page">
    <a href="{{ $backRoute }}" class="back-link">
        <i class="fa-solid fa-arrow-left"></i>
        Kembali ke Katalog
    </a>

    @if(session('success'))
        <div class="alert alert-success alert-modern mb-4 shadow-sm">
            <i class="fa-solid fa-circle-check mt-1"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-modern mb-4 shadow-sm">
            <i class="fa-solid fa-circle-exclamation mt-1"></i>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <div class="detail-card">
        <div class="row g-0">
            <div class="col-lg-6">
                <div class="image-panel">
                    <div class="image-frame">
                        <img
                            src="{{ $photoUrl }}"
                            alt="{{ $item->name ?? 'Foto Barang' }}"
                            onerror="this.onerror=null; this.src='https://placehold.co/900x620/f3f4f6/9ca3af?text=No+Photo';"
                        >
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="detail-panel">
                    <div class="item-type-badge {{ $item->type === 'lost' ? 'type-lost' : 'type-found' }}">
                        @if($item->type === 'lost')
                            <i class="fa-solid fa-magnifying-glass"></i>
                            Laporan Kehilangan
                        @else
                            <i class="fa-solid fa-box-open"></i>
                            Barang Temuan
                        @endif
                    </div>

                    <h1 class="detail-title">{{ $item->name ?? 'Barang tanpa nama' }}</h1>

                    <div class="badge-row">
                        <span class="soft-badge {{ $statusClass }}">
                            <i class="fa-solid fa-circle"></i>
                            {{ $statusLabel }}
                        </span>

                        <span class="soft-badge badge-category">
                            <i class="fa-solid fa-tag"></i>
                            {{ $categoryLabel }}
                        </span>
                    </div>

                    <div class="detail-side-meta">
                        <div class="meta-row">
                            <span class="meta-key">Tanggal Laporan</span>
                            <span class="meta-value">{{ $createdDate }}</span>
                        </div>

                        <div class="meta-row">
                            <span class="meta-key">Status</span>
                            <span class="meta-value">{{ $statusLabel }}</span>
                        </div>

                        <div class="meta-row">
                            <span class="meta-key">Kategori</span>
                            <span class="meta-value">{{ $categoryLabel }}</span>
                        </div>
                    </div>

                    @if($item->type === 'found')
                        <div class="info-block">
                            <div class="info-label">
                                <i class="fa-solid fa-location-dot"></i>
                                Lokasi Penemuan
                            </div>
                            <div class="info-value">
                                {{ $item->location ?: 'Lokasi belum tersedia.' }}
                            </div>
                        </div>
                    @endif

                    <div class="info-block">
                        <div class="info-label">
                            <i class="fa-solid fa-align-left"></i>
                            Deskripsi
                        </div>
                        <div class="info-value">
                            {{ $item->description ?: '-' }}
                        </div>
                    </div>

                    @if($item->type === 'found')
                        @if($item->status === 'available' && !$isStaff)
                            <button class="btn btn-claim mt-2" data-bs-toggle="modal" data-bs-target="#claimModal">
                                <i class="fa-solid fa-hand-holding-hand"></i>
                                Ajukan Pengambilan Barang
                            </button>
                        @elseif($item->status !== 'available' && !$isStaff)
                            <button class="btn-disabled mt-2" disabled>
                                <i class="fa-solid fa-clock me-1"></i>
                                Barang Sedang Diproses / Telah Diambil
                            </button>
                        @elseif($isStaff && $item->status === 'pending_publish')
                            <form action="{{ route('lost-found.verify-report', $item->id) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="btn btn-claim">
                                    <i class="fa-solid fa-circle-check"></i>
                                    Verifikasi & Tampilkan Laporan
                                </button>
                            </form>
                        @endif
                    @else
                        <div class="notice-box notice-warning">
                            <i class="fa-solid fa-circle-info"></i>
                            <div>
                                <strong class="notice-title">Barang Sedang Dicari Pemilik!</strong>
                                <div class="notice-text">
                                    Jika Anda menemukan barang dengan ciri-ciri di atas, mohon segera menyerahkan barang tersebut ke Pos Satpam terdekat atau hubungi petugas kampus.
                                </div>
                            </div>
                        </div>

                        @if($isStaff && $item->status === 'pending_publish')
                            <form action="{{ route('lost-found.verify-report', $item->id) }}" method="POST" class="mt-3">
                                @csrf
                                <button type="submit" class="btn btn-claim">
                                    <i class="fa-solid fa-circle-check"></i>
                                    Verifikasi & Tampilkan Laporan
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($item->type === 'found' && $item->status === 'available' && !$isStaff)
<div class="modal fade" id="claimModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-modern">
            <form action="{{ route('lost-found.request-claim', $item->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header modal-header-modern d-flex align-items-start justify-content-between">
                    <div>
                        <h5 class="modal-title-modern">
                            <i class="fa-solid fa-hand-holding-hand text-danger me-2"></i>
                            Ajukan Pengambilan
                        </h5>
                        <p class="modal-subtitle mb-0">
                            Isi data klaim dengan benar. Petugas akan memverifikasi bukti identitas kamu sebelum serah terima.
                        </p>
                    </div>

                    <button type="button" class="btn-close mt-1" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="notice-box notice-info mb-4">
                        <i class="fa-solid fa-circle-info"></i>
                        <div>
                            <strong class="notice-title">Pastikan kamu pemilik sah barang ini.</strong>
                            <div class="notice-text">
                                Gunakan identitas asli dan foto bukti yang jelas agar proses verifikasi tidak tertunda.
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-modern">Nama Lengkap *</label>
                        <input
                            type="text"
                            name="claim_name"
                            class="form-control custom-input"
                            value="{{ old('claim_name', $user->name ?? '') }}"
                            placeholder="Masukkan nama lengkap"
                            required
                        >
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-5">
                            <label class="form-label-modern">Jenis ID *</label>
                            <select name="claim_id_type" class="form-select custom-input" required>
                                <option value="KTM" {{ old('claim_id_type') === 'KTM' ? 'selected' : '' }}>KTM</option>
                                <option value="KTP" {{ old('claim_id_type') === 'KTP' ? 'selected' : '' }}>KTP</option>
                                <option value="SIM" {{ old('claim_id_type') === 'SIM' ? 'selected' : '' }}>SIM</option>
                            </select>
                        </div>

                        <div class="col-7">
                            <label class="form-label-modern">Nomor ID / NIM *</label>
                            <input
                                type="text"
                                name="claim_id_number"
                                class="form-control custom-input"
                                value="{{ old('claim_id_number', $user->nim ?? session('nim')) }}"
                                placeholder="Masukkan NIM / nomor identitas"
                                required
                            >
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-modern text-danger">Foto Identitas Asli *</label>
                        <div class="upload-box-premium" id="claimUploadBox">
                            <i class="fa-solid fa-id-card fs-3 mb-2 text-danger opacity-75"></i>
                            <div class="small fw-bold text-danger text-truncate" id="claimFileText">
                                Upload Foto Identitas
                            </div>
                            <div class="small text-muted mt-1">JPG, PNG, JPEG, atau WEBP maksimal 2 MB</div>
                            <input
                                type="file"
                                name="claim_proof_photo"
                                id="claimProofInput"
                                class="hidden-file"
                                accept="image/jpeg,image/jpg,image/png,image/webp"
                                required
                            >
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn-submit-claim text-uppercase">
                        <i class="fa-solid fa-paper-plane me-2"></i>
                        Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const claimProofInput = document.getElementById('claimProofInput');
        const claimFileText = document.getElementById('claimFileText');
        const claimUploadBox = document.getElementById('claimUploadBox');

        if (claimProofInput && claimFileText && claimUploadBox) {
            claimProofInput.addEventListener('change', function () {
                const file = this.files && this.files.length > 0 ? this.files[0] : null;

                if (!file) {
                    claimFileText.innerText = 'Upload Foto Identitas';
                    claimUploadBox.classList.remove('is-filled');
                    return;
                }

                claimFileText.innerText = file.name;
                claimUploadBox.classList.add('is-filled');
            });
        }
    });
</script>
@endpush
