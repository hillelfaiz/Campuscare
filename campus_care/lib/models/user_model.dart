  class UserModel {
    final int id;
    final String nama;
    final String email;
    final String? nim; // Opsional karena satpam/teknisi mungkin tidak punya NIM
    final String? kelas;
    final String role; // mahasiswa, satpam, teknisi_it, ob, admin
    final String? fotoProfilUrl;
    final String? token; // Token Sanctum untuk API

    UserModel({
      required this.id,
      required this.nama,
      required this.email,
      this.nim,
      this.kelas,
      required this.role,
      this.fotoProfilUrl,
      this.token,
    });

    // Mapping dari JSON (Response Laravel API) ke Object Flutter
    factory UserModel.fromJson(Map<String, dynamic> json) {
      return UserModel(
        id: json['id'],
        nama: json['nama'],
        email: json['email'],
        nim: json['nim'],
        kelas: json['kelas'],
        role: json['role'],
        fotoProfilUrl: json['foto_profil'],
        token: json['token'], // Biasanya token dikirim terpisah di response login
      );
    }

    // Mapping dari Object Flutter ke JSON
    Map<String, dynamic> toJson() {
      return {
        'id': id,
        'nama': nama,
        'email': email,
        'nim': nim,
        'kelas': kelas,
        'role': role,
        'foto_profil': fotoProfilUrl,
      };
    }
  }