class RoomModel {
  final int id;
  final String namaRuangan;
  final String gedung;
  final String lantai;

  RoomModel({
    required this.id,
    required this.namaRuangan,
    required this.gedung,
    required this.lantai,
  });

  factory RoomModel.fromJson(Map<String, dynamic> json) {
    return RoomModel(
      id: json['id'],
      namaRuangan: json['nama_ruangan'],
      gedung: json['gedung'],
      lantai: json['lantai'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'nama_ruangan': namaRuangan,
      'gedung': gedung,
      'lantai': lantai,
    };
  }

  // Display label helper
  String get displayLabel => '$namaRuangan - $gedung Lt. $lantai';
}
