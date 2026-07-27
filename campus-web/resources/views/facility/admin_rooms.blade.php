@extends('layouts.app')
@section('title', 'Admin Kelola Ruangan - Campus Care')
@section('role_text', 'Admin Fasilitas')

@section('sidebar_menu')
    <li class="menu-item">
        <a href="#collapseLostFound" data-bs-toggle="collapse" class="menu-link d-flex justify-content-between align-items-center text-decoration-none {{ request()->routeIs('lost-found.*') ? 'active' : '' }}" aria-expanded="{{ request()->routeIs('lost-found.*') ? 'true' : 'false' }}">
            <span><i class="fa-solid fa-box-open"></i> Pelaporan Barang Hilang</span>
            <i class="fa-solid fa-chevron-down" style="font-size: 0.75rem;"></i>
        </a>
        <div class="collapse {{ request()->routeIs('lost-found.*') ? 'show' : '' }}" id="collapseLostFound">
            <ul class="list-unstyled ms-4 mt-2 mb-0">
                <li class="mb-2"><a href="{{ route('lost-found.index') }}" class="menu-link text-decoration-none {{ request()->routeIs('lost-found.index') || request()->routeIs('lost-found.show') ? 'active' : '' }}" style="display: block;">
                    <i class="fa-solid fa-list-ul"></i> Daftar Semua Laporan
                </a></li>
            </ul>
        </div>
    </li>
    
    <li class="menu-item mt-3">
        <a href="#collapseFacility" data-bs-toggle="collapse" class="menu-link d-flex justify-content-between align-items-center text-decoration-none {{ request()->routeIs('admin.facility.*') ? 'active' : '' }}" aria-expanded="{{ request()->routeIs('admin.facility.*') ? 'true' : 'false' }}">
            <span><i class="fa-solid fa-screwdriver-wrench"></i> Pelaporan Fasilitas Rusak</span>
            <i class="fa-solid fa-chevron-down" style="font-size: 0.75rem;"></i>
        </a>
        
        <div class="collapse {{ request()->routeIs('admin.facility.*') ? 'show' : '' }}" id="collapseFacility">
            <ul class="list-unstyled ms-4 mt-2 mb-0">
                <li class="mb-2">
                    <a href="{{ route('admin.facility.reports') }}" class="menu-link text-decoration-none {{ request()->routeIs('admin.facility.reports') ? 'active' : '' }}" style="display: block;">
                        <i class="fa-solid fa-list-check"></i> Daftar Laporan Masuk
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('admin.facility.rooms') }}" class="menu-link text-decoration-none {{ request()->routeIs('admin.facility.rooms') ? 'active' : '' }}" style="display: block;">
                        <i class="fa-solid fa-building"></i> Kelola Ruangan Kampus
                    </a>
                </li>
            </ul>
        </div>
    </li>
@endsection

@push('styles')
<style>
    .admin-card { background: #fff; border-radius: 15px; border: 1px solid #eaeaea; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    .form-label { font-weight: 700; font-size: 0.75rem; color: #444; text-transform: uppercase; margin-bottom: 8px; }
    .table thead th { background-color: #f8f9fa; color: #666; font-size: 0.7rem; text-transform: uppercase; padding: 15px 10px; border: none; }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <div class="mb-4">
        <h4 class="fw-bold text-dark mb-1">Dashboard Admin: Kelola Ruangan Kampus</h4>
        <p class="text-muted small">Input data ruangan dari form web ini otomatis tersimpan langsung ke Database.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-3 mb-4 shadow-sm small">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-3 mb-4 shadow-sm small">{{ $errors->first() }}</div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-plus-circle me-1 text-danger"></i> Tambah Ruangan Baru</h6>
                <form action="{{ route('admin.facility.rooms.store') }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted mb-1">Nama Ruangan</label>
                        <input type="text" name="nama_ruangan" class="form-control form-control-sm rounded-3" placeholder="Contoh: Lab Komputer 2" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted mb-1">Nama Gedung</label>
                        <input type="text" name="gedung" class="form-control form-control-sm rounded-3" placeholder="Contoh: Tokong Nanas" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">Lantai</label>
                        <input type="number" name="lantai" class="form-control form-control-sm rounded-3" placeholder="Contoh: 3" required>
                    </div>
                    <button type="submit" class="btn btn-danger btn-sm w-100 rounded-3 fw-bold" style="background-color: #cc0000;">Simpan Ruangan</button>
                </form>
            </div>
        </div>

        <div class="col-12 col-md-8">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-table me-1 text-danger"></i> Daftar Ruangan Aktif di API</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nama Ruangan</th>
                                <th>Gedung</th>
                                <th>Lantai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rooms as $room)
                            <tr>
                                <td class="fw-bold text-muted">#{{ $room['id'] }}</td>
                                <td class="fw-bold text-dark">{{ $room['nama_ruangan'] }}</td>
                                <td>Gedung {{ $room['gedung'] }}</td>
                                <td>Lantai {{ $room['lantai'] }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada data ruangan di database API.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection