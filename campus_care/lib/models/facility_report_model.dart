class FacilityReportModel {
  final int? id;
  final int? reporterId;
  final int? roomId;
  final String namaFasilitas;
  final String description;
  final String? imageUrl;
  final String status;
  final String? catatanTeknisi;
  final String? assignedTo;
  final String? createdAt;
  final Map<String, dynamic>? user;
  final Map<String, dynamic>? room;
  final String? priorityLevel;
  final int? reportCount;
  final int? uniqueReporterCount;
  final int? similarityScore;

  FacilityReportModel({
    this.id,
    this.reporterId,
    this.roomId,
    required this.namaFasilitas,
    required this.description,
    this.imageUrl,
    this.status = 'pending',
    this.catatanTeknisi,
    this.assignedTo,
    this.createdAt,
    this.user,
    this.room,
    this.priorityLevel,
    this.reportCount,
    this.uniqueReporterCount,
    this.similarityScore,
  });

  String get statusLabel {
    switch (status) {
      case 'in_progress':
        return 'Sedang Diperbaiki';
      case 'completed':
        return 'Sudah Diperbaiki';
      case 'pending':
      default:
        return 'Menunggu';
    }
  }

  String get priorityLabel {
    switch (priorityLevel) {
      case 'high':
        return 'Tinggi';
      case 'medium':
        return 'Sedang';
      case 'low':
      default:
        return 'Rendah';
    }
  }

  static String statusToApi(String label) {
    switch (label) {
      case 'Sedang Diperbaiki':
        return 'in_progress';
      case 'Sudah Diperbaiki':
        return 'completed';
      case 'Menunggu':
      default:
        return 'pending';
    }
  }

  String get title => namaFasilitas;

  String get location {
    if (room != null) {
      final namaRuangan = room!['nama_ruangan'] ?? '-';
      final gedung = room!['gedung'] ?? '-';
      return '$namaRuangan - $gedung';
    }

    return '-';
  }

  factory FacilityReportModel.fromJson(Map<String, dynamic> json) {
    return FacilityReportModel(
      id: json['id'],
      reporterId: json['reporter_id'],
      roomId: json['room_id'],
      namaFasilitas: json['nama_fasilitas'] ?? '-',
      description: json['deskripsi_kerusakan'] ?? json['description'] ?? '-',
      imageUrl: json['foto'] ?? json['image'],
      status: json['status'] ?? 'pending',
      catatanTeknisi: json['catatan_teknisi'],
      assignedTo: json['assigned_to'],
      createdAt: json['created_at'],
      user: json['user'],
      room: json['room'],
      priorityLevel: json['priority_level'],
      reportCount: json['report_count'],
      uniqueReporterCount: json['unique_reporter_count'],
      similarityScore: json['similarity_score'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      if (id != null) 'id': id,
      if (roomId != null) 'room_id': roomId,
      'nama_fasilitas': namaFasilitas,
      'deskripsi_kerusakan': description,
      'status': status,
      if (catatanTeknisi != null) 'catatan_teknisi': catatanTeknisi,
      if (assignedTo != null) 'assigned_to': assignedTo,
    };
  }
}