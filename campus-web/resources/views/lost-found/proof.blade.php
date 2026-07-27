@extends('layouts.app')

@section('title', 'Bukti Serah Terima — ' . $item->name)
@section('role_text', 'Petugas Satpam')

@section('sidebar_menu')
    <li class="menu-item">
        <a href="#collapseLostFound" data-bs-toggle="collapse" class="menu-link d-flex justify-content-between align-items-center text-decoration-none active" aria-expanded="true">
            <span><i class="fa-solid fa-box-open"></i> Pelaporan Barang Hilang</span>
            <i class="fa-solid fa-chevron-down" style="font-size: 0.75rem;"></i>
        </a>
        <div class="collapse show" id="collapseLostFound">
            <ul class="list-unstyled ms-4 mt-2 mb-0">
                <li class="mb-2">
                    <a href="{{ route('lost-found.index') }}" class="menu-link text-decoration-none {{ request()->routeIs('lost-found.index') ? 'active' : '' }}" style="display: block;">Daftar Temuan</a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('lost-found.lost.index') }}" class="menu-link text-decoration-none {{ request()->routeIs('lost-found.lost.index') ? 'active' : '' }}" style="display: block;">Daftar Kehilangan</a>
                </li>
                
                @if(!Auth::user()->isSatpam())
                <li class="mb-2">
                    <a href="{{ route('lost-found.history.found') }}" class="menu-link text-decoration-none {{ request()->routeIs('lost-found.history.found') ? 'active' : '' }}" style="display: block;">Riwayat Lapor Temuan</a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('lost-found.history.lost') }}" class="menu-link text-decoration-none {{ request()->routeIs('lost-found.history.lost') ? 'active' : '' }}" style="display: block;">Riwayat Kehilangan</a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('lost-found.history') }}" class="menu-link text-decoration-none {{ request()->routeIs('lost-found.history') ? 'active' : '' }}" style="display: block;">Riwayat Klaim</a>
                </li>
                @endif
            </ul>
        </div>
    </li>

    <li class="menu-item mt-3">
        <a href="javascript:void(0)" class="menu-link opacity-50 d-flex justify-content-between align-items-center text-decoration-none" style="cursor: default;">
            <span><i class="fa-solid fa-screwdriver-wrench"></i> Fasilitas Rusak</span>
            <span class="badge rounded-pill bg-light text-dark border ms-1" style="font-size: 0.6rem;">Soon</span>
        </a>
    </li>
@endsection

@section('content')
<div class="container py-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('lost-found.index') }}" class="text-decoration-none text-muted small fw-bold mb-2 d-inline-block">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
            <h3 class="fw-bold mb-1">Bukti <span class="text-danger">Serah Terima</span></h3>
            <p class="text-muted small">Arsip lengkap verifikasi pengambilan barang temuan.</p>
        </div>
        <button onclick="window.print()" class="btn btn-light border fw-bold px-4 rounded-pill shadow-sm">
            <i class="fa-solid fa-print me-2"></i> Cetak Bukti
        </button>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
        {{-- HEADER BARANG + DESKRIPSI --}}
        <div class="p-4 border-bottom bg-light d-flex align-items-start">
            <img src="{{ str_starts_with($item->photo, 'http') ? $item->photo : 'http://127.0.0.1:8002/storage/'.$item->photo }}" class="rounded shadow-sm me-4 border" width="85" height="85" style="object-fit: cover; border-width: 3px !important; border-color: #fff !important;">
            <div class="flex-grow-1">
                <div class="text-danger fw-bold small mb-1">#LF-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</div>
                <h4 class="fw-bold mb-1">{{ $item->name }}</h4>
                <div class="text-muted small mb-2"><i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $item->location }}</div>
                
                @if($item->description)
                <div class="p-2 bg-white rounded border mt-2" style="font-size: 0.8rem; color: #555;">
                    <span class="fw-bold text-dark">Deskripsi Barang:</span> {{ $item->description }}
                </div>
                @endif
            </div>
        </div>

        <div class="p-4">
            {{-- DATA PENGAMBIL --}}
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <label class="text-muted small fw-bold text-uppercase d-block mb-1" style="letter-spacing: 1px;">Nama Pengambil</label>
                    <div class="fw-bold fs-5 text-dark">{{ $item->claim_name ?? '-' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="text-muted small fw-bold text-uppercase d-block mb-1" style="letter-spacing: 1px;">Jenis Identitas</label>
                    <div class="fw-bold fs-5 text-dark">{{ $item->claim_id_type ?? '-' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="text-muted small fw-bold text-uppercase d-block mb-1" style="letter-spacing: 1px;">Nomor ID / NIM</label>
                    <div class="fw-bold fs-5 text-dark">{{ $item->claim_id_number ?? '-' }}</div>
                </div>
            </div>

            {{-- TANGGAL DITEMUKAN & DIAMBIL --}}
            <div class="row mb-5 py-3 bg-light rounded-4 mx-0">
                <div class="col-md-6 mb-3 mb-md-0 border-end">
                    <label class="text-muted small fw-bold text-uppercase d-block mb-1"><i class="fa-solid fa-calendar-plus me-1"></i> Tanggal Ditemukan</label>
                    <div class="fw-bold text-dark">
                        {{ \Carbon\Carbon::parse($item->created_at)->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small fw-bold text-uppercase d-block mb-1"><i class="fa-solid fa-calendar-check me-1 text-success"></i> Tanggal Diambil</label>
                    <div class="fw-bold text-dark">
                        {{ $item->claimed_at ? \Carbon\Carbon::parse($item->claimed_at)->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') : '-' }} WIB
                    </div>
                </div>
            </div>

            {{-- FOTO BUKTI --}}
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="text-muted small fw-bold text-uppercase mb-3 d-block"><i class="fa-solid fa-id-card me-2 text-danger"></i> Foto Bukti Identitas</label>
                    <div class="bg-light p-2 rounded-4 border text-center shadow-sm">
                        @if($item->claim_proof_photo)
                            <img src="{{ str_starts_with($item->claim_proof_photo, 'http') ? $item->claim_proof_photo : 'http://127.0.0.1:8002/storage/'.$item->claim_proof_photo }}" class="img-fluid rounded shadow-sm" style="max-height: 300px;">
                        @else
                            <div class="py-5 text-muted small">Foto tidak tersedia</div>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small fw-bold text-uppercase mb-3 d-block"><i class="fa-solid fa-camera me-2 text-danger"></i> Foto Serah Terima</label>
                    <div class="bg-light p-2 rounded-4 border text-center shadow-sm">
                        @php $handover = $item->claim_handover_photo ?? $item->handover_photo; @endphp
                        @if($handover)
                            <img src="{{ str_starts_with($handover, 'http') ? $handover : 'http://127.0.0.1:8002/storage/'.$handover }}" class="img-fluid rounded shadow-sm" style="max-height: 300px;">
                        @else
                            <div class="py-5 text-muted small">Foto penyerahan tidak ditemukan</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- LOG RIWAYAT (SALAH ORANG) --}}
            @php
                $history = is_string($item->claim_history) ? json_decode($item->claim_history, true) : ($item->claim_history ?? []);
            @endphp

            @if(!empty($history))
            <div class="mt-5 pt-4 border-top">
                <h6 class="fw-bold text-danger mb-3"><i class="fa-solid fa-clock-rotate-left me-2"></i> Riwayat Klaim Sebelumnya (Salah Orang)</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="bg-light small">
                            <tr><th>Nama Pengambil</th><th>NIM/ID</th><th>Alasan Perubahan</th><th>Waktu</th></tr>
                        </thead>
                        <tbody class="small">
                            @foreach($history as $log)
                            <tr>
                                <td>{{ $log['claim_name'] }}</td>
                                <td>{{ $log['claim_id_number'] }}</td>
                                <td><span class="text-danger">{{ $log['note'] }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($log['logged_at'])->translatedFormat('d M Y, H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
        
        <div class="p-4 bg-light border-top">
            <div class="text-muted" style="font-size: 0.75rem;">
                <i class="fa-solid fa-circle-info me-1"></i> Data ini merupakan catatan resmi <strong>Campus Care</strong>. Dicetak otomatis pada {{ now()->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB.
            </div>
        </div>
    </div>
</div>
@endsection