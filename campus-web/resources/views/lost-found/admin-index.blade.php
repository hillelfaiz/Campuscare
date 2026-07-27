@extends('layouts.app')
@section('title', 'Dashboard Satpam - Campus Care')
@section('role_text', 'Petugas Satpam')

@section('sidebar_menu')
    <li class="menu-item">
        <a href="#collapseLostFound" data-bs-toggle="collapse" class="menu-link d-flex justify-content-between align-items-center text-decoration-none {{ request()->routeIs('lost-found.*') ? 'active' : '' }}" aria-expanded="true">
            <span><i class="fa-solid fa-box-open"></i> Pelaporan Barang Hilang</span>
            <i class="fa-solid fa-chevron-down" style="font-size: 0.75rem;"></i>
        </a>
        <div class="collapse show" id="collapseLostFound">
            <ul class="list-unstyled ms-4 mt-2 mb-0">
                <li class="mb-2"><a href="{{ route('lost-found.index') }}" class="menu-link text-decoration-none active" style="display: block;"><i class="fa-solid fa-list-ul"></i> Daftar Semua Laporan</a></li>
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
                <li class="mb-2"><a href="{{ route('admin.facility.reports') }}" class="menu-link text-decoration-none {{ request()->routeIs('admin.facility.reports') ? 'active' : '' }}" style="display: block;"><i class="fa-solid fa-list-check"></i> Daftar Laporan Masuk</a></li>
                <li class="mb-2"><a href="{{ route('admin.facility.rooms') }}" class="menu-link text-decoration-none {{ request()->routeIs('admin.facility.rooms') ? 'active' : '' }}" style="display: block;"><i class="fa-solid fa-building"></i> Kelola Ruangan Kampus</a></li>
            </ul>
        </div>
    </li>
@endsection

@push('styles')
<style>
    .admin-card { background: #fff; border-radius: 16px; border: 1px solid #eef0f3; box-shadow: 0 4px 14px rgba(0,0,0,0.02); }
    .form-label { font-weight: 700; font-size: 0.75rem; color: #444; text-transform: uppercase; margin-bottom: 6px; letter-spacing:0.3px; }
    .custom-input { background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px 14px; font-size: 0.9rem; transition: 0.2s; }
    .custom-input:focus { background-color: #fff; border-color: #cc0000; box-shadow: 0 0 0 4px rgba(204,0,0,0.08); outline: none; }
    .table thead th { background-color: #f8f9fa; color: #475569; font-size: 0.7rem; font-weight:700; text-transform: uppercase; padding: 14px 10px; border: none; }
    .status-pill { padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; display: inline-block; text-align: center; white-space: nowrap;}
    .pill-available { background: #ecfdf5; color: #10b981; border: 1px solid #a7f3d0; }
    .pill-claimed { background: #f1f5f9; color: #475569; }
    .pill-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
    .pill-info { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .filter-box { background: #fff; border-radius: 14px; border: 1px solid #eee; padding: 16px; margin-bottom: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.01); }
    
    /* Responsive Grid Metrics Layaknya Flutter Style */
    .stat-widget-admin-lf { background: #ffffff; border-radius: 14px; border: 1px solid #efeefe; padding: 14px; display: flex; align-items: center; box-shadow: 0 4px 10px rgba(0,0,0,0.01); }
    .stat-icon-admin-lf { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px; font-size: 1.1rem; }
    .section-indicator { width: 3px; height: 16px; background-color: #cc0000; border-radius: 2px; display: inline-block; vertical-align: middle; margin-right: 6px; }
    .section-title-text { font-size: 0.9rem; font-weight: 700; color: #111; vertical-align: middle; text-transform: uppercase; }
</style>
@endpush

@section('content')
<div class="mb-4">
    <h3 class="fw-bold mb-1">Workspace <span class="text-danger">Manajemen Barang Hilang</span></h3>
    <p class="text-muted small m-0">Validasi input, atur jadwal pertemuan serah terima offline, serta kelola log kepemilikan inventaris.</p>
</div>

<div class="mb-4">
    <div class="mb-3">
        <span class="section-indicator"></span>
        <span class="section-title-text">Dashboard Statistik Inventaris</span>
    </div>
    <div class="row g-3">
        <div class="col-6 col-lg-3">
            <div class="stat-widget-admin-lf">
                <div class="stat-icon-admin-lf" style="background: #f1f5f9; color: #475569;"><i class="fa-solid fa-boxes-stacked"></i></div>
                <div>
                    <h4 class="fw-bold m-0 text-dark" style="line-height:1.1; font-size:1.35rem;">{{ $stats['total'] }}</h4>
                    <small class="text-muted fw-semibold small" style="font-size:0.65rem; text-transform: uppercase;">Total Registrasi</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-widget-admin-lf">
                <div class="stat-icon-admin-lf" style="background: #FFFBEB; color: #D97706;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div>
                    <h4 class="fw-bold m-0 text-warning" style="line-height:1.1; font-size:1.35rem;">{{ $stats['pending_verif'] }}</h4>
                    <small class="text-muted fw-semibold small" style="font-size:0.65rem; text-transform: uppercase;">Butuh Tindakan</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-widget-admin-lf">
                <div class="stat-icon-admin-lf" style="background: #ECFDF5; color: #10B981;"><i class="fa-solid fa-square-rss"></i></div>
                <div>
                    <h4 class="fw-bold m-0 text-success" style="line-height:1.1; font-size:1.35rem;">{{ $stats['available'] }}</h4>
                    <small class="text-muted fw-semibold small" style="font-size:0.65rem; text-transform: uppercase;">Tayang Aktif</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-widget-admin-lf">
                <div class="stat-icon-admin-lf" style="background: #EFF6FF; color: #3B82F6;"><i class="fa-solid fa-handshake-angle"></i></div>
                <div>
                    <h4 class="fw-bold m-0 text-primary" style="line-height:1.1; font-size:1.35rem;">{{ $stats['claimed'] }}</h4>
                    <small class="text-muted fw-semibold small" style="font-size:0.65rem; text-transform: uppercase;">Selesai Diambil</small>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 rounded-3 mb-3 small shadow-sm">{{ session('success') }}</div>
@endif

<div class="row g-3">
    <div class="col-xl-4 col-lg-5 d-none d-lg-block">
        <div class="admin-card p-4">
            <h6 class="fw-bold mb-3 text-dark text-uppercase" style="font-size:0.8rem; letter-spacing:0.5px;"><i class="fa-solid fa-circle-plus text-danger me-2"></i> Input Temuan Baru</h6>
            <form action="{{ route('lost-found.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="found">
                <div class="mb-3"><label class="form-label">Nama Judul Barang *</label><input type="text" name="name" class="form-control custom-input" placeholder="Misal: Kunci Motor Honda" required></div>
                <div class="mb-3">
                    <label class="form-label">Kategori *</label>
                    <select name="category" class="form-select custom-input" required>
                        <option value="electronic">Elektronik / Gawai</option><option value="document">Dokumen / KTM</option><option value="accessory">Aksesori / Pakaian</option>
                    </select>
                </div>
                <div class="mb-3"><label class="form-label">Lokasi Penemuan *</label><input type="text" name="location" class="form-control custom-input" placeholder="Misal: Lobby Gedung B" required></div>
                <div class="mb-3"><label class="form-label">Deskripsi Opsional</label><textarea name="description" class="form-control custom-input" rows="2" placeholder="Ciri spesifik barang..." style="resize:none;"></textarea></div>
                <div class="mb-4"><label class="form-label">Foto Valid Berkas *</label><input type="file" name="photo" class="form-control custom-input" accept="image/*" required></div>
                <button type="submit" class="btn btn-danger w-100 fw-bold rounded-pill py-2 text-uppercase" style="background: linear-gradient(135deg, #cc0000 0%, #aa0000 100%); border:none; font-size:0.8rem;">SIMPAN & SEBARKAN</button>
            </form>
        </div>
    </div>

    <div class="col-xl-8 col-lg-7 col-12">
        <div class="d-lg-none mb-3">
            <button class="btn btn-danger w-100 fw-bold rounded-pill py-2 shadow" data-bs-toggle="modal" data-bs-target="#mobileInputModal" style="background-color:#cc0000; border:none;"><i class="fa-solid fa-circle-plus me-2"></i> Input Barang Temuan Baru</button>
        </div>

        <div class="filter-box">
            <form action="{{ route('lost-found.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-6 col-12"><input type="text" name="search" class="form-control custom-input" placeholder="Cari nama barang di sini..." value="{{ request('search') }}"></div>
                <div class="col-md-4 col-9">
                    <select name="status" class="form-select custom-input" onchange="this.form.submit()">
                        <option value="">Semua Status Log</option>
                        <option value="pending_publish" {{ request('status') == 'pending_publish' ? 'selected' : '' }}>Laporan Mahasiswa</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Tayang Aktif (Tersedia)</option>
                        <option value="claim_pending" {{ request('status') == 'claim_pending' ? 'selected' : '' }}>Pengajuan Ambil</option>
                        <option value="claim_scheduled" {{ request('status') == 'claim_scheduled' ? 'selected' : '' }}>Tunggu Diambil</option>
                        <option value="claimed" {{ request('status') == 'claimed' ? 'selected' : '' }}>Arsip Selesai</option>
                    </select>
                </div>
                <div class="col-md-2 col-3"><a href="{{ route('lost-found.index') }}" class="btn btn-light w-100 border custom-input d-flex align-items-center justify-content-center" style="padding:10px;"><i class="fa-solid fa-rotate-left text-secondary"></i></a></div>
            </form>
        </div>

        <div class="admin-card p-0 p-md-3 overflow-hidden">
            <div class="table-responsive">
                <table class="table align-middle m-0">
                    <thead><tr><th class="ps-3">INFORMASI OBJEK BARANG</th><th>STATUS</th><th class="text-center pe-3">AKSI / OPERASI</th></tr></thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr>
                            <td class="ps-3 py-3">
                                <div class="d-flex align-items-center">
                                    <img src="{{ str_starts_with($item->photo, 'http') ? $item->photo : asset('storage/'.$item->photo) }}" class="rounded me-3 border" width="46" height="46" style="object-fit: cover; background:#f8f9fa;" onerror="this.src='https://via.placeholder.com/50'">
                                    <div>
                                        @if($item->type === 'lost') <span class="badge bg-warning text-dark mb-1" style="font-size: 0.58rem; font-weight:700;">KEHILANGAN</span>
                                        @else <span class="badge bg-danger mb-1" style="font-size: 0.58rem; font-weight:700;">TEMUAN</span> @endif
                                        <div class="fw-bold small text-dark">{{ $item->name }}</div>
                                        @if($item->type === 'found') <small class="text-muted d-block" style="font-size:0.75rem;"><i class="fa-solid fa-location-dot small"></i> {{ $item->location }}</small> @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($item->status === 'pending_publish') <span class="status-pill pill-warning">Cek Berkas</span>
                                @elseif($item->status === 'available') <span class="status-pill pill-available">Tayang</span>
                                @elseif($item->status === 'claim_pending') <span class="status-pill pill-warning">Butuh Jadwal</span>
                                @elseif($item->status === 'claim_scheduled') <span class="status-pill pill-info">Tunggu Diambil</span>
                                @else <span class="status-pill pill-claimed">Selesai</span> @endif
                            </td>
                            <td class="text-center pe-3">
                                <div class="d-flex flex-wrap justify-content-center gap-1">
                                    @if($item->status === 'pending_publish')
                                        <button class="btn btn-sm btn-warning fw-bold text-dark px-2" data-bs-toggle="modal" data-bs-target="#cekLaporanModal{{ $item->id }}" style="font-size:0.7rem;">Cek</button>
                                    @elseif($item->status === 'available')
                                        <button class="btn btn-sm btn-success fw-bold px-2" data-bs-toggle="modal" data-bs-target="#verifModal{{ $item->id }}" style="font-size:0.7rem;">Serah</button>
                                    @elseif($item->status === 'claim_pending')
                                        <button class="btn btn-sm btn-info text-white fw-bold px-2" data-bs-toggle="modal" data-bs-target="#jadwalModal{{ $item->id }}" style="font-size:0.7rem;">Jadwal</button>
                                    @elseif($item->status === 'claim_scheduled')
                                        <button class="btn btn-sm btn-success fw-bold px-2" data-bs-toggle="modal" data-bs-target="#serahModal{{ $item->id }}" style="font-size:0.7rem;">Selesai</button>
                                    @elseif($item->status === 'claimed')
                                        <button class="btn btn-sm btn-danger fw-bold px-2" data-bs-toggle="modal" data-bs-target="#reclaimModal{{ $item->id }}" style="font-size:0.7rem;">Klaim Ulang</button>
                                        <a href="{{ route('lost-found.proof', $item->id) }}" class="btn btn-sm btn-light border fw-bold px-2" style="font-size:0.7rem;">Bukti</a>
                                    @endif
                                    <a href="{{ route('lost-found.show', $item->id) }}" class="btn btn-sm btn-outline-primary fw-bold px-2" style="font-size:0.7rem;">Detail</a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3 px-3">{{ $items->appends(request()->query())->links() }}</div>
        </div>
    </div>
</div>
@endsection