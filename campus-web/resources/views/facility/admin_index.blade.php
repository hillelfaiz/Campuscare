@extends('layouts.app')

@section('title', 'Dashboard Admin Fasilitas - Campus Care')
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
    .table align-middle th { background-color: #f8f9fa; color: #666; font-size: 0.7rem; text-transform: uppercase; padding: 15px 10px; border: none; }
    .status-pill { padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; display: inline-block; text-align: center; white-space: nowrap;}
    .pill-pending { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
    .pill-progress { background: #cce5ff; color: #004085; border: 1px solid #b8daff; }
    .pill-completed { background: #e2f0d9; color: #385723; border: 1px solid #c5e0b4; }
    .filter-box { background: #fff; border-radius: 12px; border: 1px solid #eee; padding: 15px; margin-bottom: 20px; }

    /* Stats Grid Flutter Design Sync */
    .stat-widget-admin { background: #ffffff; border-radius: 14px; border: 1px solid #efeefe; padding: 15px; display: flex; align-items: center; box-shadow: 0 4px 10px rgba(0,0,0,0.015); }
    .stat-icon-admin { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px; font-size: 1.1rem; }
    .section-indicator { width: 3px; height: 16px; background-color: #cc0000; border-radius: 2px; display: inline-block; vertical-align: middle; margin-right: 6px; }
    .section-title-text { font-size: 0.9rem; font-weight: 700; color: #111; vertical-align: middle; text-transform: uppercase; }
</style>
@endpush

@section('content')
<div class="mb-4">
    <h3 class="fw-bold mb-1">Dashboard Admin <span class="text-danger">Fasilitas Rusak</span></h3>
    <p class="text-muted small m-0">Validasi laporan kerusakan infrastruktur kampus.</p>
</div>

<div class="mb-4">
    <div class="mb-3">
        <span class="section-indicator"></span>
        <span class="section-title-text">Ringkasan Tugas & Logistik</span>
    </div>
    <div class="row g-3">
        <div class="col-6 col-lg-3">
            <div class="stat-widget-admin">
                <div class="stat-icon-admin" style="background: #FFF0F2; color: #cc0000;"><i class="fa-solid fa-list-check"></i></div>
                <div>
                    <h4 class="fw-bold m-0 p-0 text-danger" style="line-height:1.1; font-size:1.4rem;">{{ $stats['total'] }}</h4>
                    <small class="text-muted fw-semibold small" style="font-size:0.68rem; text-transform: uppercase;">Total Laporan</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-widget-admin">
                <div class="stat-icon-admin" style="background: #FFFBEB; color: #F59E0B;"><i class="fa-solid fa-clock"></i></div>
                <div>
                    <h4 class="fw-bold m-0 p-0 text-warning" style="line-height:1.1; font-size:1.4rem;">{{ $stats['pending'] }}</h4>
                    <small class="text-muted fw-semibold small" style="font-size:0.68rem; text-transform: uppercase;">Menunggu</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-widget-admin">
                <div class="stat-icon-admin" style="background: #EFF6FF; color: #3B82F6;"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                <div>
                    <h4 class="fw-bold m-0 p-0 text-primary" style="line-height:1.1; font-size:1.4rem;">{{ $stats['in_progress'] }}</h4>
                    <small class="text-muted fw-semibold small" style="font-size:0.68rem; text-transform: uppercase;">Diproses</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-widget-admin">
                <div class="stat-icon-admin" style="background: #ECFDF5; color: #10B981;"><i class="fa-solid fa-circle-check"></i></div>
                <div>
                    <h4 class="fw-bold m-0 p-0 text-success" style="line-height:1.1; font-size:1.4rem;">{{ $stats['completed'] }}</h4>
                    <small class="text-muted fw-semibold small" style="font-size:0.68rem; text-transform: uppercase;">Selesai</small>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 rounded-3 mb-3 small shadow-sm">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger border-0 rounded-3 mb-3 small shadow-sm">{{ $errors->first() }}</div>
@endif

<div class="filter-box">
    <form action="{{ route('admin.facility.reports') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-10 col-12">
            <select name="status" class="form-select border-0 bg-light" onchange="this.form.submit()">
                <option value="">Semua Status Laporan Fasilitas</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending (Laporan Baru)</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress (Sedang Dikerjakan Teknisi)</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed (Selesai Diperbaiki)</option>
            </select>
        </div>
        <div class="col-md-2 col-12"><a href="{{ route('admin.facility.reports') }}" class="btn btn-light w-100"><i class="fa-solid fa-rotate-left"></i> Reset</a></div>
    </form>
</div>

<div class="admin-card p-0 p-md-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table align-middle m-0">
            <thead>
                <tr>
                    <th class="ps-3">FASILITAS / LOKASI</th>
                    <th>PELAPOR</th>
                    <th>STATUS</th>
                    <th class="text-center pe-3">TINDAKAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                <tr>
                    <td class="ps-3 py-3">
                        <div class="d-flex align-items-center">
                            <img src="{{ $report['foto'] ?? 'https://via.placeholder.com/45?text=No+Photo' }}" class="rounded me-3 border" width="45" height="45" style="object-fit: cover; background:#f8f9fa;">
                            <div>
                                <div class="fw-bold small text-dark">{{ $report['nama_fasilitas'] }}</div>
                                <small class="text-muted small">Gedung {{ $report['room']['gedung'] ?? '-' }} - Lt.{{ $report['room']['lantai'] ?? '-' }} ({{ $report['room']['nama_ruangan'] ?? 'Unknown' }})</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="small fw-bold text-dark">{{ $report['user']['nama'] ?? 'User Web' }}</div>
                        <small class="text-muted">{{ $report['user']['email'] ?? '-' }}</small>
                    </td>
                    <td>
                        @if($report['status'] === 'pending') <span class="status-pill pill-pending">Pending</span>
                        @elseif($report['status'] === 'in_progress') <span class="status-pill pill-progress">In Progress</span>
                        @else <span class="status-pill pill-completed">Completed</span> @endif
                    </td>
                    <td class="text-center pe-3">
                        <button class="btn btn-sm btn-danger fw-bold px-3" data-bs-toggle="modal" data-bs-target="#tindakanModal{{ $report['id'] }}" style="font-size: 0.7rem; background-color: #cc0000;">
                            Tindak
                        </button>
                    </td>
                </tr>

                <div class="modal fade" id="tindakanModal{{ $report['id'] }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                            <form action="{{ route('admin.facility.reports.update', $report['id']) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-header border-0 pb-0 px-4 pt-4 text-danger">
                                    <h5 class="fw-bold"><i class="fa-solid fa-screwdriver-wrench me-2"></i> Update Penanganan Kerusakan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="mb-3">
                                        <label class="form-label">Deskripsi Kerusakan Mahasiswa</label>
                                        <p class="p-2 bg-light rounded small text-muted border fst-italic">"{{ $report['deskripsi_kerusakan'] }}"</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Ubah Status Logistik *</label>
                                        <select name="status" class="form-select" required>
                                            <option value="pending" {{ $report['status'] == 'pending' ? 'selected' : '' }}>Pending (Menunggu Antrean)</option>
                                            <option value="in_progress" {{ $report['status'] == 'in_progress' ? 'selected' : '' }}>In Progress (Sedang Dikerjakan Teknisi)</option>
                                            <option value="completed" {{ $report['status'] == 'completed' ? 'selected' : '' }}>Completed (Selesai Diperbaiki)</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Teknisi yang Ditugaskan</label>
                                        <input type="text" name="assigned_to" class="form-control" placeholder="Contoh: Pak Bambang / Tim IT" value="{{ $report['assigned_to'] ?? '' }}">
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label">Catatan Perkembangan Teknisi</label>
                                        <textarea name="catatan_teknisi" class="form-control" rows="3" placeholder="Contoh: Suku cadang proyektor sedang dipesan.">{{ $report['catatan_teknisi'] ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 p-4 pt-0">
                                    <button type="submit" class="btn btn-danger w-100 fw-bold rounded-pill py-2" style="background-color: #cc0000;">SIMPAN & PERBARUI STATUS</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">Tidak ditemukan data pelaporan fasilitas masuk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection