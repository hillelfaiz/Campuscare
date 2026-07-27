import 'dart:convert';
import 'dart:io';
import 'package:google_sign_in/google_sign_in.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../core/api_client.dart';
import '../models/user_model.dart';

class AuthService {
  final ApiClient _apiClient = ApiClient();

  final GoogleSignIn _googleSignIn = GoogleSignIn(
    scopes: ['email'],
    serverClientId:
        '390215971304-fee8vj209k52p8dkj5vug7mgikvf9s1v.apps.googleusercontent.com',
  );

  Future<UserModel?> login(String email, String password) async {
    try {
      final response = await _apiClient.post('/login', {
        'email': email,
        'password': password,
      });

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        if (data['token'] == null || data['user'] == null) {
          throw Exception('Response login tidak valid');
        }

        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('token', data['token'].toString());

        return UserModel.fromJson(data['user']);
      } else {
        throw Exception(data['message'] ?? 'Email atau password salah');
      }
    } catch (e) {
      throw Exception('Gagal melakukan login: $e');
    }
  }

  /// Register sekarang hanya membuat akun dan mengirim OTP.
  /// Backend tidak mengembalikan token/user sebelum email diverifikasi.
  Future<Map<String, dynamic>> register({
    required String nama,
    required String email,
    required String password,
    required String nim,
    required String kelas,
  }) async {
    try {
      final response = await _apiClient.post('/register', {
        'nama': nama,
        'email': email,
        'password': password,
        'nim': nim,
        'kelas': kelas,
        'role': 'mahasiswa',
      });

      final data = jsonDecode(response.body);

      if ((response.statusCode == 201 || response.statusCode == 200) &&
          data['success'] == true) {
        return data;
      } else {
        String errorMessage = data['message'] ?? 'Gagal mendaftar';

        if (data['errors'] != null) {
          final errors = data['errors'] as Map<String, dynamic>;

          if (errors.isNotEmpty) {
            final firstError = errors.values.first;

            if (firstError is List && firstError.isNotEmpty) {
              errorMessage = firstError.first.toString();
            } else {
              errorMessage = firstError.toString();
            }
          }
        }

        throw Exception(errorMessage);
      }
    } catch (e) {
      throw Exception('Gagal melakukan pendaftaran: $e');
    }
  }

  Future<UserModel?> loginWithGoogle() async {
    try {
      final GoogleSignInAccount? googleUser = await _googleSignIn.signIn();

      if (googleUser == null) return null;

      final GoogleSignInAuthentication googleAuth =
          await googleUser.authentication;

      final response = await _apiClient.post('/auth/google', {
        'token': googleAuth.idToken,
        'email': googleUser.email,
        'nama': googleUser.displayName ?? googleUser.email,
      });

      final data = jsonDecode(response.body);

      if ((response.statusCode == 200 || response.statusCode == 201) &&
          data['success'] == true) {
        if (data['token'] == null || data['user'] == null) {
          throw Exception('Response Google login tidak valid');
        }

        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('token', data['token'].toString());

        return UserModel.fromJson(data['user']);
      } else {
        await _googleSignIn.signOut();
        throw Exception(data['message'] ?? 'Gagal login dengan Google');
      }
    } catch (e) {
      await _googleSignIn.signOut();
      throw Exception('Terjadi kesalahan Login Google: $e');
    }
  }

  Future<UserModel?> completeGoogleProfile({
    required String nama,
    required String nim,
    required String kelas,
  }) async {
    try {
      final response = await _apiClient.post('/profile/complete', {
        'nama': nama,
        'nim': nim,
        'kelas': kelas,
        'role': 'mahasiswa',
      });

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        final userJson = data['data'] ?? data['user'];

        if (userJson == null) {
          throw Exception('Response profil tidak valid');
        }

        return UserModel.fromJson(userJson);
      } else {
        throw Exception(data['message'] ?? 'Gagal melengkapi profil');
      }
    } catch (e) {
      throw Exception('Gagal melengkapi profil: $e');
    }
  }

  Future<UserModel?> checkToken() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');

      if (token == null || token.isEmpty) return null;

      final response = await _apiClient.get('/user');

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        final userJson = data['user'] ?? data;

        return UserModel.fromJson(userJson);
      } else {
        await prefs.remove('token');
      }
    } catch (_) {
      return null;
    }

    return null;
  }

  Future<void> logout() async {
    try {
      await _apiClient.post('/logout', {});
      await _googleSignIn.signOut();
    } catch (_) {
      // Abaikan error server, tetap hapus token lokal.
    } finally {
      final prefs = await SharedPreferences.getInstance();
      await prefs.remove('token');
    }
  }

  Future<UserModel?> updateProfile({
    required String nama,
    File? foto,
  }) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token') ?? '';

      final request = http.MultipartRequest(
        'POST',
        Uri.parse('${ApiClient.baseUrl}/profile'),
      );

      request.headers['Authorization'] = 'Bearer $token';
      request.headers['Accept'] = 'application/json';
      request.fields['nama'] = nama;

      if (foto != null) {
        request.files.add(
          await http.MultipartFile.fromPath('foto_profil', foto.path),
        );
      }

      final response = await request.send();
      final responseData = await response.stream.bytesToString();
      final data = jsonDecode(responseData);

      if (response.statusCode == 200 && data['user'] != null) {
        return UserModel.fromJson(data['user']);
      } else {
        throw Exception(data['message'] ?? 'Gagal update profil');
      }
    } catch (e) {
      throw Exception('Terjadi kesalahan saat update profil: $e');
    }
  }
}