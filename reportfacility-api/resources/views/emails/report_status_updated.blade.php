<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Status Laporan Diperbarui</title>
</head>
<body>
    <h2>Status Laporan Kamu Diperbarui</h2>

    <p>Halo {{ $report->user?->nama ?? 'Mahasiswa' }},</p>

    <p>Status laporan fasilitas kamu sudah diperbarui.</p>

    <p><strong>Fasilitas:</strong> {{ $report->nama_fasilitas }}</p>
    <p><strong>Lokasi:</strong>
        {{ $report->room?->nama_ruangan ?? '-' }} - {{ $report->room?->gedung ?? '-' }}
    </p>
    <p><strong>Status terbaru:</strong> {{ $report->status }}</p>

    @if($report->catatan_teknisi)
        <p><strong>Feedback Petugas:</strong> {{ $report->catatan_teknisi }}</p>
    @endif

    <br>
    <p>Terima kasih sudah menggunakan Campus Care.</p>
</body>
</html>