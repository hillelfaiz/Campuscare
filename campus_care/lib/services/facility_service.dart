import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../core/api_client.dart';
import '../models/asset_model.dart';
import '../models/facility_report_model.dart';
import '../models/room_model.dart';

class FacilityService {
  final ApiClient _apiClient = ApiClient();

  // --- ROOMS MANAGEMENT ---

  Future<List<RoomModel>> getRooms() async {
    try {
      final response = await _apiClient.get('/rooms');
      if (response.statusCode == 200) {
        final List<dynamic> data = jsonDecode(response.body)['data'];
        return data.map((json) => RoomModel.fromJson(json)).toList();
      }
      return [];
    } catch (e) {
      throw Exception('Gagal memuat data ruangan: $e');
    }
  }

  Future<List<RoomModel>> searchRooms(String keyword) async {
    if (keyword.isEmpty) return [];
    try {
      final response = await _apiClient.get('/rooms/search?q=$keyword');
      if (response.statusCode == 200) {
        final List<dynamic> data = jsonDecode(response.body)['data'];
        return data.map((json) => RoomModel.fromJson(json)).toList();
      }
      return [];
    } catch (e) {
      throw Exception('Gagal mencari ruangan: $e');
    }
  }

  Future<bool> createRoom(RoomModel room) async {
    try {
      final response = await _apiClient.post('/rooms', room.toJson());
      print('STATUS: ${response.statusCode}');
      print('BODY: ${response.body}');
      return response.statusCode == 201 || response.statusCode == 200;
    } catch (e) {
      print('ERROR: $e');
      throw Exception('Gagal membuat ruangan: $e');
    }
  }

  Future<bool> updateRoom(RoomModel room) async {
    try {
      final response =
          await _apiClient.put('/rooms/${room.id}', room.toJson());
      return response.statusCode == 200;
    } catch (e) {
      throw Exception('Gagal update ruangan: $e');
    }
  }

  Future<bool> deleteRoom(int id) async {
    try {
      final response = await _apiClient.delete('/rooms/$id');
      return response.statusCode == 200;
    } catch (e) {
      throw Exception('Gagal menghapus ruangan: $e');
    }
  }

  // --- ASSETS MANAGEMENT ---

  Future<CampusAssetModel?> getAssetByKode(String kode) async {
    try {
      final response = await _apiClient.get('/campus-assets/$kode');
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return CampusAssetModel.fromJson(data['data']);
      }
      return null;
    } catch (e) {
      throw Exception('Gagal mengambil data aset: $e');
    }
  }

  Future<List<CampusAssetModel>> getAssets() async {
    try {
      final response = await _apiClient.get('/campus-assets');
      if (response.statusCode == 200) {
        final List<dynamic> data = jsonDecode(response.body)['data'];
        return data.map((json) => CampusAssetModel.fromJson(json)).toList();
      }
      return [];
    } catch (e) {
      throw Exception('Gagal memuat data aset: $e');
    }
  }

  Future<bool> createAsset(CampusAssetModel asset) async {
    try {
      final response =
          await _apiClient.post('/campus-assets', asset.toJson());
      return response.statusCode == 201 || response.statusCode == 200;
    } catch (e) {
      throw Exception('Gagal membuat aset: $e');
    }
  }

  Future<bool> updateAsset(CampusAssetModel asset) async {
    try {
      final response = await _apiClient.put(
          '/campus-assets/${asset.id}', asset.toJson());
      return response.statusCode == 200;
    } catch (e) {
      throw Exception('Gagal update aset: $e');
    }
  }

  Future<bool> deleteAsset(int id) async {
    try {
      final response = await _apiClient.delete('/campus-assets/$id');
      return response.statusCode == 200;
    } catch (e) {
      throw Exception('Gagal menghapus aset: $e');
    }
  }

  // --- REPORTS ---

  Future<List<FacilityReportModel>> getReports() async {
    try {
      final response = await _apiClient.get('/facility-reports');
      if (response.statusCode == 200) {
        final List<dynamic> data = jsonDecode(response.body)['data'];
        return data.map((json) => FacilityReportModel.fromJson(json)).toList();
      }
      return [];
    } catch (e) {
      throw Exception('Gagal memuat daftar laporan: $e');
    }
  }

  Future<bool> submitReport(
    FacilityReportModel report,
    File? fotoKerusakan,
  ) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token') ?? '';

      final request = http.MultipartRequest(
        'POST',
        Uri.parse('${ApiClient.baseUrl}/facility-reports'),
      );

      request.headers['Authorization'] = 'Bearer $token';
      request.headers['Accept'] = 'application/json';

      request.fields['room_id'] = report.roomId?.toString() ?? '';
      request.fields['nama_fasilitas'] = report.namaFasilitas;
      request.fields['deskripsi_kerusakan'] = report.description;
      request.fields['assigned_to'] = report.assignedTo ?? '';

      if (fotoKerusakan != null) {
        request.files.add(
          await http.MultipartFile.fromPath('foto', fotoKerusakan.path),
        );
      }

      final streamedResponse = await request.send();
      final response = await http.Response.fromStream(streamedResponse);

      if (response.statusCode == 200 || response.statusCode == 201) {
        return true;
      }

      throw Exception(response.body);
    } catch (e) {
      throw Exception('Terjadi kesalahan saat mengirim laporan: $e');
    }
  }

  Future<bool> updateReportStatus(
    int id,
    String status, {
    String? catatanTeknisi,
  }) async {
    try {
      // FIX: hapus '?' yang invalid, gunakan conditional map entry
      final body = <String, dynamic>{'status': status};
      if (catatanTeknisi != null) {
        body['catatan_teknisi'] = catatanTeknisi;
      }

      final response =
          await _apiClient.put('/facility-reports/$id', body);
      return response.statusCode == 200;
    } catch (e) {
      throw Exception('Gagal update status: $e');
    }
  }
}