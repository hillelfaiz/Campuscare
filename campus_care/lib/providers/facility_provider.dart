import 'dart:io';
import 'package:flutter/material.dart';
import '../models/asset_model.dart';
import '../models/facility_report_model.dart';
import '../models/room_model.dart';
import '../services/facility_service.dart';

class FacilityProvider with ChangeNotifier {
  final FacilityService _facilityService = FacilityService();

  List<FacilityReportModel> _reports = [];
  List<FacilityReportModel> get reports => _reports;

  List<CampusAssetModel> _assets = [];
  List<CampusAssetModel> get assets => _assets;

  List<RoomModel> _rooms = [];
  List<RoomModel> get rooms => _rooms;

  bool _isLoading = false;
  bool get isLoading => _isLoading;

  Future<void> fetchReports() async {
    _isLoading = true;
    notifyListeners();
    try {
      _reports = await _facilityService.getReports();
    } catch (e) {
      _reports = [];
    }
    _isLoading = false;
    notifyListeners();
  }

  // --- ROOMS ---

  Future<void> fetchRooms() async {
    _isLoading = true;
    notifyListeners();
    try {
      _rooms = await _facilityService.getRooms();
    } catch (e) {
      _rooms = [];
    }
    _isLoading = false;
    notifyListeners();
  }

  Future<List<RoomModel>> searchRooms(String keyword) async {
    try {
      return await _facilityService.searchRooms(keyword);
    } catch (e) {
      return [];
    }
  }

  Future<bool> createRoom(RoomModel room) async {
    final success = await _facilityService.createRoom(room);
    if (success) await fetchRooms();
    return success;
  }

  Future<bool> updateRoom(RoomModel room) async {
    final success = await _facilityService.updateRoom(room);
    if (success) await fetchRooms();
    return success;
  }

  Future<bool> deleteRoom(int id) async {
    final success = await _facilityService.deleteRoom(id);
    if (success) await fetchRooms();
    return success;
  }

  // --- ASSETS ---

  Future<void> fetchAssets() async {
    _isLoading = true;
    notifyListeners();
    try {
      _assets = await _facilityService.getAssets();
    } catch (e) {
      _assets = [];
    }
    _isLoading = false;
    notifyListeners();
  }

  Future<bool> createAsset(CampusAssetModel asset) async {
    final success = await _facilityService.createAsset(asset);
    if (success) await fetchAssets();
    return success;
  }

  Future<bool> updateAsset(CampusAssetModel asset) async {
    final success = await _facilityService.updateAsset(asset);
    if (success) await fetchAssets();
    return success;
  }

  Future<bool> deleteAsset(int id) async {
    final success = await _facilityService.deleteAsset(id);
    if (success) await fetchAssets();
    return success;
  }

  Future<CampusAssetModel?> getAssetByKode(String kode) async {
    return await _facilityService.getAssetByKode(kode);
  }

  // --- REPORTS ---

  Future<void> submitReport({
    required int roomId,
    required String namaFasilitas,
    required String deskripsi,
    required int reporterId,
    String? fotoPath,
  }) async {
    final newReport = FacilityReportModel(
      roomId: roomId,
      namaFasilitas: namaFasilitas,
      description: deskripsi,
      reporterId: reporterId,
    );

    File? fotoFile = fotoPath != null ? File(fotoPath) : null;
    final success = await _facilityService.submitReport(newReport, fotoFile);

    if (success) {
      await fetchReports();
    }
  }

  Future<void> updateReportStatus(int id, String newStatus) async {
    final success = await _facilityService.updateReportStatus(id, newStatus);
    if (success) {
      await fetchReports();
    }
  }
}
