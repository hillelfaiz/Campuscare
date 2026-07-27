import 'dart:io';
import 'package:flutter/material.dart';

import '../models/user_model.dart';
import '../services/auth_service.dart';

class AuthProvider with ChangeNotifier {
  UserModel? _currentUser;
  UserModel? get currentUser => _currentUser;

  bool _isLoading = true;
  bool get isLoading => _isLoading;

  String? _errorMessage;
  String? get errorMessage => _errorMessage;

  bool _needsProfileCompletion = false;
  bool get needsProfileCompletion => _needsProfileCompletion;

  final AuthService _authService = AuthService();

  Future<void> checkAuth() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _currentUser = await _authService.checkToken();

      if (_currentUser != null) {
        _needsProfileCompletion =
            _currentUser!.nim == null || _currentUser!.nim!.isEmpty;
      } else {
        _needsProfileCompletion = false;
      }
    } catch (_) {
      _currentUser = null;
      _needsProfileCompletion = false;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> login(String email, String password) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _currentUser = await _authService.login(email, password);
      _needsProfileCompletion = false;

      _isLoading = false;
      notifyListeners();

      return _currentUser != null;
    } catch (e) {
      _currentUser = null;
      _needsProfileCompletion = false;
      _errorMessage = _cleanErrorMessage(e.toString());

      _isLoading = false;
      notifyListeners();

      return false;
    }
  }

  Future<void> register({
    required String nama,
    required String email,
    required String password,
    required String nim,
    required String kelas,
  }) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      await _authService.register(
        nama: nama,
        email: email,
        password: password,
        nim: nim,
        kelas: kelas,
      );

      _currentUser = null;
      _needsProfileCompletion = false;
    } catch (e) {
      _currentUser = null;
      _needsProfileCompletion = false;
      _errorMessage = _cleanErrorMessage(e.toString());
      rethrow;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> loginWithGoogle() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _currentUser = await _authService.loginWithGoogle();

      if (_currentUser != null) {
        _needsProfileCompletion =
            _currentUser!.nim == null || _currentUser!.nim!.isEmpty;
      } else {
        _needsProfileCompletion = false;
      }

      _isLoading = false;
      notifyListeners();

      return _currentUser != null;
    } catch (e) {
      _currentUser = null;
      _needsProfileCompletion = false;
      _errorMessage = _cleanErrorMessage(e.toString());

      _isLoading = false;
      notifyListeners();

      return false;
    }
  }

  Future<void> completeGoogleProfile({
    required String nama,
    required String nim,
    required String kelas,
  }) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final updatedUser = await _authService.completeGoogleProfile(
        nama: nama,
        nim: nim,
        kelas: kelas,
      );

      _currentUser = updatedUser;
      _needsProfileCompletion = false;
    } catch (e) {
      _errorMessage = _cleanErrorMessage(e.toString());
      rethrow;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> logout() async {
    try {
      await _authService.logout();
    } catch (_) {
      // Kalau token sudah invalid / expired, tetap logout dari sisi aplikasi.
    }

    _currentUser = null;
    _needsProfileCompletion = false;
    _errorMessage = null;
    _isLoading = false;
    notifyListeners();
  }

  Future<void> updateProfile({
    required String nama,
    File? foto,
  }) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final updatedUser = await _authService.updateProfile(
        nama: nama,
        foto: foto,
      );

      _currentUser = updatedUser;
    } catch (e) {
      _errorMessage = _cleanErrorMessage(e.toString());
      rethrow;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  String _cleanErrorMessage(String error) {
    var message = error
        .replaceAll('Exception:', '')
        .replaceAll('Gagal melakukan login:', '')
        .replaceAll('Gagal melakukan pendaftaran:', '')
        .replaceAll('Terjadi kesalahan Login Google:', '')
        .replaceAll('Gagal melengkapi profil:', '')
        .replaceAll('Terjadi kesalahan saat update profil:', '')
        .replaceAll('Unauthenticated.', '')
        .replaceAll('The given data was invalid.', '')
        .trim();

    final lowerMessage = message.toLowerCase();

    if (lowerMessage.contains('socketexception') ||
        lowerMessage.contains('connection') ||
        lowerMessage.contains('timeout') ||
        lowerMessage.contains('failed host lookup') ||
        lowerMessage.contains('server lambat') ||
        lowerMessage.contains('tidak dapat dijangkau')) {
      return 'Server tidak dapat dijangkau. Periksa koneksi atau IP backend.';
    }

    if (lowerMessage.contains('invalid credentials') ||
        lowerMessage.contains('password salah') ||
        lowerMessage.contains('email atau password salah')) {
      return 'Email atau password salah. Silakan coba lagi.';
    }

    if (message.isEmpty) {
      return 'Terjadi kesalahan. Silakan coba lagi.';
    }

    return message;
  }
}