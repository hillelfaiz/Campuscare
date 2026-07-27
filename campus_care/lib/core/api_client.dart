import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ApiClient {
  static String get baseUrl {
    return 'http://192.168.18.42:8001/api';
  }

  Future<Map<String, String>> _getHeaders() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token') ?? '';

    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      if (token.isNotEmpty) 'Authorization': 'Bearer $token',
    };
  }

  Future<http.Response> get(String endpoint) async {
    return await http
        .get(
          Uri.parse('$baseUrl$endpoint'),
          headers: await _getHeaders(),
        )
        .timeout(const Duration(seconds: 15), onTimeout: () {
      throw Exception(
        'Koneksi timeout: Server lambat atau tidak dapat dijangkau.',
      );
    });
  }

  Future<http.Response> post(String endpoint, Map<String, dynamic> body) async {
    return await http
        .post(
          Uri.parse('$baseUrl$endpoint'),
          headers: await _getHeaders(),
          body: jsonEncode(body),
        )
        .timeout(const Duration(seconds: 15), onTimeout: () {
      throw Exception(
        'Koneksi timeout: Server lambat atau tidak dapat dijangkau.',
      );
    });
  }

  Future<http.Response> put(String endpoint, Map<String, dynamic> body) async {
    return await http
        .put(
          Uri.parse('$baseUrl$endpoint'),
          headers: await _getHeaders(),
          body: jsonEncode(body),
        )
        .timeout(const Duration(seconds: 15), onTimeout: () {
      throw Exception(
        'Koneksi timeout: Server lambat atau tidak dapat dijangkau.',
      );
    });
  }

  Future<http.Response> delete(String endpoint) async {
    return await http
        .delete(
          Uri.parse('$baseUrl$endpoint'),
          headers: await _getHeaders(),
        )
        .timeout(const Duration(seconds: 15), onTimeout: () {
      throw Exception(
        'Koneksi timeout: Server lambat atau tidak dapat dijangkkau.',
      );
    });
  }
}