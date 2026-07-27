@extends('layouts.app')

@section('title', 'Dashboard Satpam - Campus Care')

@section('role_text', 'Petugas Satpam')

@section('sidebar_menu')
    <li class="menu-item">
        <a href="{{ route('lost-found.index') }}" class="menu-link active">
            <i class="fa-solid fa-box-open"></i> Manajemen Barang Hilang
        </a>
    </li>
    <li class="menu-item">
        <a href="javascript:void(0)" class="menu-link opacity-50" style="cursor: default;">
            <i class="fa-solid fa-screwdriver-wrench"></i> Manajemen Fasilitas Rusak 
            <span class="badge rounded-pill bg-light text-dark border ms-1" style="font-size: 0.6rem;">Soon</span>
        </a>
    </li>
    <li class="menu-item">
        <a href="#" class="menu-link">
            <i class="fa-solid fa-history"></i> Riwayat Verifikasi
        </a>
    </li>
@endsection

@push('styles')
<style>
    .admin-card { background: #fff; border-radius: 15px; border: 1px solid #eaeaea; box-shadow: 0 4px 12px rgba(0,0,0,0.03); height: 100%; }
    .form-label { font-weight: 700; font-size: 0.75rem; color: #444; text-transform: uppercase; margin-bottom: 8px; }
    .btn-save { background-color: #cc0000; color: white; font-weight: 700; border-radius: 10px; padding: 12px; border: none; }
    .table thead th { background-color: #f8f9fa; color: #666; font-size: 0.7rem; text-transform: uppercase; padding: 15px 10px; }
    .status-pill { padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; display: inline-block; }
    .pill-available { background: #fff5f5; color: #cc0000; border: 1px solid #ffc1c1; }
    .pill-claimed { background: #f1f5f9; color: #475569; }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="border-radius: 12px;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-4">
        <h3 class="fw-bold mb-1">Dashboard <span class="text-danger">Satpam</span></h3>
        <p class="text-muted small">Kelola laporan temuan barang dan verifikasi pengambilan.</p>
    </div>

    <div class="row g-4">
        <div class="col-xl-4 col-lg-5">
            <div class="admin-card p-4">
                <h6 class="fw-bold mb-4 text-danger"><i class="fa-solid fa-circle-plus me-2"></i> Input Barang Baru</h6>
                <form action="{{ route('lost-found.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Barang *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori *</label>
                        <select name="category" class="form-select" required>
                            <option value="electronic">Elektronik</option>
                            <option value="document">Dokumen</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lokasi Penemuan *</label>
                        <input type="text" name="location" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Foto Barang</label>
                        <input type="file" name="photo" class="form-control">
                    </div>
                    <button type="submit" class="btn-save w-100 shadow-sm">SIMPAN BARANG</button>
                </form>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="admin-card p-4">
                <h6 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-list-check me-2"></i> Manajemen Barang Temuan</h6>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>DETAIL BARANG</th>
                                <th>STATUS</th>
                                <th class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $item->photo ?? 'https://via.placeholder.com/50' }}" class="rounded me-3 shadow-sm" width="45" height="45" style="object-fit: cover;">
                                        <div>
                                            <div class="fw-bold small">{{ $item->name }}</div>
                                            <small class="text-muted small">{{ $item->location }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($item->claimed_at)
                                        <span class="status-pill pill-claimed">Telah Diambil</span>
                                    @else
                                        <span class="status-pill pill-available">Tersedia</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        @if($item->claimed_at)
                                            <a href="{{ route('lost-found.proof', $item->id) }}" class="btn btn-sm btn-light border fw-bold text-secondary px-3 shadow-sm" style="border-radius: 8px; font-size: 0.7rem;">
                                                Bukti
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-outline-success fw-bold px-3 shadow-sm" 
                                                onclick="openClaimModal('{{ $item->id }}', '{{ $item->name }}')" 
                                                style="border-radius: 8px; font-size: 0.7rem;">
                                                Verifikasi
                                            </button>
                                        @endif
                                        <a href="{{ route('lost-found.show', $item) }}" class="btn btn-sm btn-outline-primary fw-bold px-3 shadow-sm" style="border-radius: 8px; font-size: 0.7rem;">
                                            Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- INCLUDE FILE MODAL KHUSUS KAMU --}}
@include('lost-found._claim-modal')

@endsection