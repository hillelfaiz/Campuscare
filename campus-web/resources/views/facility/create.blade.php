@extends('layouts.app')

@section('title', 'Lapor Kerusakan - Campus Care')

@push('styles')
<style>
    .report-page {
        animation: fadeUp 0.35s ease-out;
    }

    .report-hero {
        background:
            radial-gradient(circle at 92% 16%, rgba(255,255,255,0.23), transparent 28%),
            linear-gradient(135deg, #d40000 0%, #b50000 50%, #760000 100%);
        border-radius: 30px;
        color: #ffffff;
        padding: 32px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 22px 48px rgba(204, 0, 0, 0.18);
        margin-bottom: 26px;
    }

    .report-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,0.07) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.07) 1px, transparent 1px);
        background-size: 34px 34px;
        opacity: 0.32;
    }

    .report-hero-content {
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

    .hero-title {
        font-size: 1.85rem;
        font-weight: 850;
        letter-spacing: -0.9px;
        margin-bottom: 8px;
    }

    .hero-subtitle {
        color: rgba(255,255,255,0.78);
        font-weight: 500;
        max-width: 780px;
        margin: 0;
        line-height: 1.7;
    }

    .btn-back-hero {
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
        gap: 8px;
        white-space: nowrap;
    }

    .btn-back-hero:hover {
        transform: translateY(-2px);
        background: #fff6f6;
        color: #a30000;
    }

    .form-shell {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) minmax(300px, 0.55fr);
        gap: 24px;
        align-items: start;
    }

    .form-card,
    .side-card {
        background: #ffffff;
        border: 1px solid #edf0f5;
        border-radius: 28px;
        box-shadow: 0 16px 38px rgba(15, 23, 42, 0.055);
    }

    .form-card {
        padding: 28px;
    }

    .side-card {
        padding: 24px;
        position: sticky;
        top: 96px;
    }

    .form-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.96rem;
        font-weight: 850;
        color: #111827;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 20px;
    }

    .form-section-title::before {
        content: "";
        width: 4px;
        height: 20px;
        border-radius: 999px;
        background: #cc0000;
        display: inline-block;
    }

    .form-label-premium {
        font-size: 0.73rem;
        font-weight: 850;
        color: #4b5563;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .form-label-premium i {
        color: #cc0000;
    }

    .required-mark {
        color: #cc0000;
        font-weight: 900;
    }

    .custom-input {
        background: #f9fafb;
        border: 1px solid #edf0f5;
        border-radius: 16px;
        padding: 13px 15px;
        font-size: 0.92rem;
        font-weight: 600;
        color: #111827;
        transition: 0.2s ease;
    }

    .custom-input:focus {
        background: #ffffff;
        border-color: #ffd0d0;
        box-shadow: 0 0 0 4px rgba(204, 0, 0, 0.07);
        outline: none;
    }

    .custom-input::placeholder {
        color: #a8adb7;
        font-weight: 500;
    }

    .cc-dropdown {
        position: relative;
    }

    .cc-dropdown-button {
        width: 100%;
        background: #f9fafb;
        border: 1px solid #edf0f5;
        border-radius: 16px;
        padding: 13px 15px;
        font-size: 0.92rem;
        font-weight: 700;
        color: #111827;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        transition: 0.2s ease;
        min-height: 50px;
        text-align: left;
    }

    .cc-dropdown-button:hover,
    .cc-dropdown.open .cc-dropdown-button {
        background: #ffffff;
        border-color: #ffd0d0;
        box-shadow: 0 0 0 4px rgba(204, 0, 0, 0.06);
    }

    .cc-dropdown-button .dropdown-current {
        display: flex;
        align-items: center;
        gap: 9px;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .cc-dropdown-button .dropdown-current i {
        color: #cc0000;
        flex-shrink: 0;
    }

    .cc-dropdown-button .chevron {
        color: #9ca3af;
        transition: 0.2s ease;
        flex-shrink: 0;
    }

    .cc-dropdown.open .chevron {
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
        z-index: 999;
        max-height: 280px;
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
        align-items: flex-start;
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
        margin-top: 2px;
    }

    .cc-dropdown-item small {
        display: block;
        color: #9ca3af;
        font-weight: 600;
        margin-top: 2px;
    }

    .upload-zone {
        border: 2px dashed #d1d5db;
        background: #f9fafb;
        border-radius: 22px;
        padding: 26px;
        text-align: center;
        cursor: pointer;
        transition: 0.22s ease;
        position: relative;
        min-height: 185px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .upload-zone:hover {
        background: #fff5f5;
        border-color: #cc0000;
    }

    .upload-zone.is-filled {
        background: #f0fdf4;
        border-color: #10b981;
    }

    .upload-icon {
        width: 62px;
        height: 62px;
        border-radius: 20px;
        background: #ffffff;
        color: #9ca3af;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.7rem;
        margin-bottom: 14px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        transition: 0.22s ease;
    }

    .upload-zone:hover .upload-icon {
        color: #cc0000;
    }

    .upload-zone.is-filled .upload-icon {
        color: #10b981;
    }

    .upload-title {
        font-weight: 850;
        color: #111827;
        font-size: 0.95rem;
        margin-bottom: 5px;
        max-width: 100%;
        word-break: break-word;
    }

    .upload-subtitle {
        color: #6b7280;
        font-size: 0.8rem;
        font-weight: 600;
        line-height: 1.5;
    }

    .hidden-file-input {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .preview-wrapper {
        display: none;
        margin-top: 16px;
        border-radius: 18px;
        overflow: hidden;
        border: 1px solid #edf0f5;
        background: #ffffff;
    }

    .preview-wrapper.show {
        display: block;
    }

    .preview-wrapper img {
        width: 100%;
        max-height: 260px;
        object-fit: cover;
        display: block;
    }

    .form-alert {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
        border-radius: 18px;
        padding: 14px 16px;
        font-size: 0.86rem;
        font-weight: 650;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 18px;
    }

    .tip-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .tip-list li {
        display: flex;
        gap: 11px;
        color: #4b5563;
        font-size: 0.86rem;
        font-weight: 600;
        line-height: 1.58;
        padding: 13px 0;
        border-bottom: 1px solid #f1f3f7;
    }

    .tip-list li:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .tip-list i {
        color: #cc0000;
        margin-top: 3px;
        flex-shrink: 0;
    }

    .side-mini-card {
        background: linear-gradient(135deg, #fff7f7 0%, #ffffff 100%);
        border: 1px solid #ffe1e1;
        border-radius: 20px;
        padding: 16px;
        margin-top: 18px;
    }

    .side-mini-card strong {
        color: #111827;
        display: block;
        margin-bottom: 6px;
        font-size: 0.92rem;
    }

    .side-mini-card p {
        margin: 0;
        color: #6b7280;
        font-size: 0.82rem;
        line-height: 1.55;
        font-weight: 600;
    }

    .btn-cancel {
        border: 1px solid #e5e7eb;
        background: #ffffff;
        color: #4b5563;
        font-weight: 800;
        font-size: 0.9rem;
        border-radius: 999px;
        padding: 13px 22px;
        transition: 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-cancel:hover {
        background: #f9fafb;
        color: #111827;
        border-color: #d1d5db;
    }

    .btn-submit-premium {
        background: linear-gradient(135deg, #cc0000 0%, #aa0000 100%);
        color: #ffffff;
        font-weight: 850;
        font-size: 0.9rem;
        border-radius: 999px;
        padding: 13px 26px;
        border: none;
        box-shadow: 0 14px 28px rgba(204, 0, 0, 0.18);
        transition: 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-submit-premium:hover {
        transform: translateY(-1px);
        box-shadow: 0 18px 34px rgba(204, 0, 0, 0.26);
        color: #ffffff;
    }

    .field-error {
        color: #cc0000;
        font-size: 0.78rem;
        font-weight: 700;
        margin-top: 7px;
        display: none;
    }

    .field-error.show {
        display: block;
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
        .form-shell {
            grid-template-columns: 1fr;
        }

        .side-card {
            position: static;
        }
    }

    @media (max-width: 767px) {
        .report-hero {
            padding: 26px 22px;
            border-radius: 24px;
        }

        .hero-title {
            font-size: 1.42rem;
        }

        .btn-back-hero {
            width: 100%;
            justify-content: center;
        }

        .form-card {
            padding: 22px;
            border-radius: 24px;
        }

        .d-action {
            flex-direction: column-reverse;
            align-items: stretch !important;
        }

        .btn-cancel,
        .btn-submit-premium {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
@php
    $selectedRoomId = old('room_id');
    $selectedRoom = collect($rooms ?? [])->firstWhere('id', $selectedRoomId);

    $selectedRoomLabel = $selectedRoom
        ? 'Gedung ' . ($selectedRoom['gedung'] ?? '-') . ' — Lantai ' . ($selectedRoom['lantai'] ?? '-') . ' (' . ($selectedRoom['nama_ruangan'] ?? '-') . ')'
        : 'Pilih lokasi ruangan';

    $roomCount = is_countable($rooms ?? []) ? count($rooms ?? []) : 0;
@endphp

<div class="report-page">
    <div class="report-hero">
        <div class="report-hero-content">
            <div class="row align-items-center g-4">
                <div class="col-lg-8 col-12">
                    <div class="hero-badge">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                        Facility Report Form
                    </div>

                    <h1 class="hero-title">Buat Aduan Kerusakan Fasilitas</h1>
                    <p class="hero-subtitle">
                        Laporkan fasilitas kelas atau area kampus yang bermasalah. Sertakan foto bukti agar laporan lebih mudah diverifikasi oleh petugas.
                    </p>
                </div>

                <div class="col-lg-4 col-12 text-lg-end">
                    <a href="{{ route('facility.index') }}" class="btn-back-hero">
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali ke Katalog
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="form-alert">
            <i class="fa-solid fa-circle-exclamation mt-1"></i>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <div id="clientError" class="form-alert d-none">
        <i class="fa-solid fa-circle-exclamation mt-1"></i>
        <div id="clientErrorText">Lengkapi data wajib terlebih dahulu.</div>
    </div>

    <div class="form-shell">
        <div class="form-card">
            <form id="facilityReportForm" action="{{ route('facility.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf

                <div class="form-section-title">Detail Laporan Kerusakan</div>

                <div class="mb-4">
                    <label class="form-label-premium">
                        <i class="fa-solid fa-location-dot"></i>
                        Lokasi / Ruangan Kuliah <span class="required-mark">*</span>
                    </label>

                    <input type="hidden" name="room_id" id="roomInput" value="{{ old('room_id') }}">

                    <div class="cc-dropdown" data-target="roomInput">
                        <button type="button" class="cc-dropdown-button" id="roomDropdownButton">
                            <span class="dropdown-current">
                                <i class="fa-solid fa-building"></i>
                                <span class="cc-dropdown-label">{{ $selectedRoomLabel }}</span>
                            </span>
                            <i class="fa-solid fa-chevron-down chevron"></i>
                        </button>

                        <div class="cc-dropdown-menu">
                            @forelse($rooms as $room)
                                @php
                                    $roomLabel = 'Gedung ' . ($room['gedung'] ?? '-') . ' — Lantai ' . ($room['lantai'] ?? '-') . ' (' . ($room['nama_ruangan'] ?? '-') . ')';
                                @endphp

                                <button
                                    type="button"
                                    class="cc-dropdown-item {{ (string) old('room_id') === (string) ($room['id'] ?? '') ? 'active' : '' }}"
                                    data-value="{{ $room['id'] }}"
                                    data-label="{{ $roomLabel }}"
                                >
                                    <i class="fa-solid fa-door-open"></i>
                                    <span>
                                        {{ $roomLabel }}
                                        <small>ID Ruangan: {{ $room['id'] ?? '-' }}</small>
                                    </span>
                                </button>
                            @empty
                                <button type="button" class="cc-dropdown-item" disabled>
                                    <i class="fa-solid fa-circle-info"></i>
                                    <span>
                                        Data ruangan belum tersedia
                                        <small>Pastikan API fasilitas sedang menyala.</small>
                                    </span>
                                </button>
                            @endforelse
                        </div>
                    </div>

                    <div class="field-error" id="roomError">
                        Lokasi / ruangan wajib dipilih.
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label-premium">
                        <i class="fa-solid fa-cube"></i>
                        Nama Fasilitas Bermasalah <span class="required-mark">*</span>
                    </label>

                    <input
                        type="text"
                        name="nama_fasilitas"
                        id="namaFasilitas"
                        class="form-control custom-input"
                        placeholder="Contoh: Proyektor, AC, Kursi, Lampu, Pintu"
                        value="{{ old('nama_fasilitas') }}"
                        required
                    >

                    <div class="field-error" id="namaError">
                        Nama fasilitas wajib diisi.
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label-premium">
                        <i class="fa-solid fa-align-left"></i>
                        Kronologi & Detail Kondisi Kerusakan <span class="required-mark">*</span>
                    </label>

                    <textarea
                        name="deskripsi_kerusakan"
                        id="deskripsiKerusakan"
                        class="form-control custom-input"
                        rows="5"
                        placeholder="Contoh: AC bocor sejak pagi, air menetes ke meja mahasiswa. Proyektor tidak menyala walaupun kabel sudah dipasang."
                        style="resize: none;"
                        required
                    >{{ old('deskripsi_kerusakan') }}</textarea>

                    <div class="field-error" id="deskripsiError">
                        Detail kerusakan wajib diisi.
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label-premium">
                        <i class="fa-solid fa-camera"></i>
                        Lampiran Bukti Foto Fisik <span class="required-mark">*</span>
                    </label>

                    <div class="upload-zone" id="uploadZone">
                        <div class="upload-icon" id="uploadIcon">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>

                        <div class="upload-title" id="uploadTitle">
                            Upload foto bukti kerusakan
                        </div>

                        <div class="upload-subtitle">
                            Foto wajib diunggah. Format JPG, JPEG, PNG, atau WEBP maksimal 2 MB.
                        </div>

                        <input
                            type="file"
                            name="foto"
                            id="fotoInput"
                            class="hidden-file-input"
                            accept="image/jpeg,image/jpg,image/png,image/webp"
                            required
                        >
                    </div>

                    <div class="field-error" id="fotoError">
                        Foto bukti kerusakan wajib diunggah.
                    </div>

                    <div class="preview-wrapper" id="previewWrapper">
                        <img id="previewImage" src="" alt="Preview Foto Kerusakan">
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-3 pt-3 border-top border-light d-action">
                    <a href="{{ route('facility.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i>
                        Batalkan
                    </a>

                    <button type="submit" class="btn-submit-premium">
                        <i class="fa-solid fa-paper-plane"></i>
                        Kirim Aduan Sekarang
                    </button>
                </div>
            </form>
        </div>

        <aside class="side-card">
            <div class="form-section-title mb-3">Panduan Laporan</div>

            <ul class="tip-list">
                <li>
                    <i class="fa-solid fa-check-circle"></i>
                    <span>Pilih lokasi ruangan yang paling sesuai dengan tempat kerusakan.</span>
                </li>
                <li>
                    <i class="fa-solid fa-check-circle"></i>
                    <span>Tulis nama fasilitas secara jelas, misalnya AC, proyektor, kursi, lampu, atau pintu.</span>
                </li>
                <li>
                    <i class="fa-solid fa-check-circle"></i>
                    <span>Jelaskan kronologi kerusakan supaya teknisi lebih mudah memahami masalah.</span>
                </li>
                <li>
                    <i class="fa-solid fa-check-circle"></i>
                    <span>Foto bukti wajib diunggah agar laporan bisa diverifikasi lebih cepat.</span>
                </li>
            </ul>

            <div class="side-mini-card">
                <strong>
                    <i class="fa-solid fa-circle-info text-danger me-1"></i>
                    Ruangan tersedia
                </strong>
                <p>
                    Saat ini terdapat {{ $roomCount }} data ruangan yang dapat dipilih dari layanan fasilitas.
                </p>
            </div>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById('facilityReportForm');

        const roomInput = document.getElementById('roomInput');
        const namaFasilitas = document.getElementById('namaFasilitas');
        const deskripsiKerusakan = document.getElementById('deskripsiKerusakan');
        const fotoInput = document.getElementById('fotoInput');

        const roomError = document.getElementById('roomError');
        const namaError = document.getElementById('namaError');
        const deskripsiError = document.getElementById('deskripsiError');
        const fotoError = document.getElementById('fotoError');

        const clientError = document.getElementById('clientError');
        const clientErrorText = document.getElementById('clientErrorText');

        const uploadZone = document.getElementById('uploadZone');
        const uploadIcon = document.getElementById('uploadIcon');
        const uploadTitle = document.getElementById('uploadTitle');
        const previewWrapper = document.getElementById('previewWrapper');
        const previewImage = document.getElementById('previewImage');

        function showError(element, show) {
            if (!element) return;

            if (show) {
                element.classList.add('show');
            } else {
                element.classList.remove('show');
            }
        }

        function setClientError(message) {
            if (!clientError || !clientErrorText) return;

            if (message) {
                clientErrorText.innerText = message;
                clientError.classList.remove('d-none');
            } else {
                clientError.classList.add('d-none');
            }
        }

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
                    if (this.hasAttribute('disabled')) return;

                    const value = this.dataset.value || '';
                    const text = this.dataset.label || this.innerText.trim();

                    if (input) input.value = value;
                    if (label) label.innerText = text;

                    items.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    dropdown.classList.remove('open');
                    showError(roomError, false);
                    setClientError(null);
                });
            });
        });

        document.addEventListener('click', function () {
            document.querySelectorAll('.cc-dropdown').forEach(function (dropdown) {
                dropdown.classList.remove('open');
            });
        });

        if (fotoInput && uploadZone && uploadTitle && uploadIcon && previewWrapper && previewImage) {
            fotoInput.addEventListener('change', function () {
                const file = this.files && this.files.length > 0 ? this.files[0] : null;

                if (!file) {
                    uploadZone.classList.remove('is-filled');
                    uploadTitle.innerText = 'Upload foto bukti kerusakan';
                    uploadIcon.innerHTML = '<i class="fa-solid fa-cloud-arrow-up"></i>';
                    previewWrapper.classList.remove('show');
                    previewImage.src = '';
                    return;
                }

                const maxSize = 2 * 1024 * 1024;
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

                if (!allowedTypes.includes(file.type)) {
                    this.value = '';
                    showError(fotoError, true);
                    setClientError('Format foto tidak valid. Gunakan JPG, JPEG, PNG, atau WEBP.');
                    uploadZone.classList.remove('is-filled');
                    previewWrapper.classList.remove('show');
                    return;
                }

                if (file.size > maxSize) {
                    this.value = '';
                    showError(fotoError, true);
                    setClientError('Ukuran foto terlalu besar. Maksimal ukuran file adalah 2 MB.');
                    uploadZone.classList.remove('is-filled');
                    previewWrapper.classList.remove('show');
                    return;
                }

                uploadZone.classList.add('is-filled');
                uploadTitle.innerHTML = `<span class="text-success">${file.name}</span>`;
                uploadIcon.innerHTML = '<i class="fa-solid fa-circle-check"></i>';
                showError(fotoError, false);
                setClientError(null);

                const reader = new FileReader();
                reader.onload = function (event) {
                    previewImage.src = event.target.result;
                    previewWrapper.classList.add('show');
                };
                reader.readAsDataURL(file);
            });
        }

        namaFasilitas?.addEventListener('input', function () {
            showError(namaError, this.value.trim() === '');
        });

        deskripsiKerusakan?.addEventListener('input', function () {
            showError(deskripsiError, this.value.trim() === '');
        });

        form?.addEventListener('submit', function (event) {
            const isRoomEmpty = !roomInput?.value;
            const isNamaEmpty = !namaFasilitas?.value.trim();
            const isDeskripsiEmpty = !deskripsiKerusakan?.value.trim();
            const isFotoEmpty = !fotoInput?.files || fotoInput.files.length === 0;

            showError(roomError, isRoomEmpty);
            showError(namaError, isNamaEmpty);
            showError(deskripsiError, isDeskripsiEmpty);
            showError(fotoError, isFotoEmpty);

            if (isRoomEmpty || isNamaEmpty || isDeskripsiEmpty || isFotoEmpty) {
                event.preventDefault();

                setClientError('Semua field bertanda wajib harus diisi, termasuk foto bukti kerusakan.');
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        });
    });
</script>
@endpush