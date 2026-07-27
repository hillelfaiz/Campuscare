class CampusAssetModel {
  final int id;
  final String kode; // Contoh: RKC-01
  final String nama; // Contoh: Proyektor Ruang Kelas
  final String lokasi; // Contoh: Gedung A
  final String kategori; // electronic, furniture, facility
  final String? deskripsi;

  CampusAssetModel({
    required this.id,
    required this.kode,
    required this.nama,
    required this.lokasi,
    required this.kategori,
    this.deskripsi,
  });

  factory CampusAssetModel.fromJson(Map<String, dynamic> json) {
    return CampusAssetModel(
      id: json['id'],
      kode: json['kode'],
      nama: json['nama'],
      lokasi: json['lokasi'],
      kategori: json['kategori'],
      deskripsi: json['deskripsi'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'kode': kode,
      'nama': nama,
      'lokasi': lokasi,
      'kategori': kategori,
      'deskripsi': deskripsi,
    };
  }
}