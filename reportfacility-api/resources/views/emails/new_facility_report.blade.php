<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Fasilitas Baru</title>
</head>
<body>
    <h2>Laporan Fasilitas Baru Masuk</h2>

    <p>Ada laporan fasilitas baru yang masuk ke sistem Campus Care.</p>

    <p><strong>Fasilitas:</strong> {{ $report->nama_fasilitas }}</p>
    <p><strong>Lokasi:</strong>
        {{ $report->room?->nama_ruangan ?? '-' }} - {{ $report->room?->gedung ?? '-' }}
    </p>
    <p><strong>Deskripsi:</strong> {{ $report->deskripsi_kerusakan }}</p>
    <p><strong>Status:</strong> {{ $report->status }}</p>
    <p><strong>Pelapor:</strong> {{ $report->user?->nama ?? 'Tidak diketahui' }}</p>

    <br>
    <p>Silakan cek aplikasi Campus Care untuk menindaklanjuti laporan.</p>
</body>
</html>