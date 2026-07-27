import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../models/facility_report_model.dart';
import '../../providers/facility_provider.dart';
import '../../widgets/top_notification.dart';

class ReportDetailScreen extends StatefulWidget {
  final FacilityReportModel report;
  final String role; // 'mahasiswa' | 'satpam' | 'admin'

  const ReportDetailScreen({
    super.key,
    required this.report,
    required this.role,
  });

  @override
  State<ReportDetailScreen> createState() => _ReportDetailScreenState();
}

class _ReportDetailScreenState extends State<ReportDetailScreen> {
  // ─── Design System (sama persis kayak file lain) ──────────────────────────
  static const _red         = Color(0xFFE4002B);
  static const _redMid      = Color(0xFFB8001F);
  static const _redDark     = Color(0xFF6B0012);
  static const _redDeep     = Color(0xFF2A000A);
  static const _bg          = Color(0xFFF7F7F5);
  static const _white       = Colors.white;
  static const _border      = Color(0xFFEFEFEF);
  static const _textPrimary = Color(0xFF111111);
  static const _textMuted   = Color(0xFFAAAAAA);
  static const _surface     = Color(0xFFF8F8F8);

  // ─── State ────────────────────────────────────────────────────────────────
  final _feedbackController = TextEditingController();
  String? _selectedStatus;
  bool _isSubmitting = false;

  @override
  void initState() {
    super.initState();
    _selectedStatus = widget.report.status;
    // Pre-fill feedback kalau sudah ada catatan sebelumnya
    if (widget.report.catatanTeknisi != null) {
      _feedbackController.text = widget.report.catatanTeknisi!;
    }
  }

  @override
  void dispose() {
    _feedbackController.dispose();
    super.dispose();
  }

  // ─── Helpers ──────────────────────────────────────────────────────────────
  bool get _canEdit => widget.role == 'satpam' || widget.role == 'admin';

  _StatusStyle _getStatusStyle(String status) {
    switch (status) {
      case 'in_progress':
        return _StatusStyle(
          label: 'Sedang Diperbaiki',
          color: const Color(0xFF3B82F6),
          bg: const Color(0xFFEFF6FF),
          icon: Icons.autorenew_rounded,
        );
      case 'completed':
        return _StatusStyle(
          label: 'Sudah Diperbaiki',
          color: const Color(0xFF10B981),
          bg: const Color(0xFFECFDF5),
          icon: Icons.check_circle_outline_rounded,
        );
      default: // pending
        return _StatusStyle(
          label: 'Menunggu',
          color: const Color(0xFFF59E0B),
          bg: const Color(0xFFFFFBEB),
          icon: Icons.access_time_rounded,
        );
    }
  }

  String _formatDate(String? raw) {
    if (raw == null || raw.length < 10) return '-';
    return raw.substring(0, 10);
  }

  Future<void> _simpanPerubahan() async {
    if (_selectedStatus == null) return;

    setState(() => _isSubmitting = true);
    try {
      final provider = Provider.of<FacilityProvider>(context, listen: false);

      // Update status
      await provider.updateReportStatus(widget.report.id!, _selectedStatus!);

      // Update catatan kalau ada (jika provider support)
      // Kalau provider belum punya method updateFeedback, bisa tambah nanti
      // await provider.updateFeedback(widget.report.id!, _feedbackController.text.trim());

      if (mounted) {
        TopNotification.show(
          context,
          message: 'Laporan berhasil diperbarui',
          type: TopNotificationType.success,
        );
        Navigator.pop(context);
      }
    } catch (e) {
      if (mounted) {
        TopNotification.show(
          context,
          message: e.toString().replaceAll('Exception: ', ''),
          type: TopNotificationType.error,
        );
      }
    } finally {
      if (mounted) setState(() => _isSubmitting = false);
    }
  }

  // ─── BUILD ────────────────────────────────────────────────────────────────
  @override
  Widget build(BuildContext context) {
    final report = widget.report;
    final statusStyle = _getStatusStyle(report.status);

    return Scaffold(
      backgroundColor: _bg,
        body: SingleChildScrollView(
          child: Column(
            children: [
              _buildHeroHeader(report, statusStyle),

              Padding(
                padding: const EdgeInsets.fromLTRB(16, 16, 16, 32),
                child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Info Laporan
                  _buildSectionCard(
                    title: 'Informasi Laporan',
                    icon: Icons.info_outline_rounded,
                    child: Column(
                      children: [
                        _buildInfoRow(
                          icon: Icons.handyman_outlined,
                          label: 'Fasilitas',
                          value: report.namaFasilitas,
                        ),
                        _buildDivider(),
                        _buildInfoRow(
                          icon: Icons.location_on_outlined,
                          label: 'Lokasi',
                          value: report.location,
                        ),
                        _buildDivider(),
                        _buildInfoRow(
                          icon: Icons.person_outline_rounded,
                          label: 'Pelapor',
                          value: report.user?['nama'] ?? 'Tidak diketahui',
                        ),
                        _buildDivider(),
                        _buildInfoRow(
                          icon: Icons.calendar_today_outlined,
                          label: 'Tanggal',
                          value: _formatDate(report.createdAt),
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 14),

                  // Deskripsi Kerusakan
                  _buildSectionCard(
                    title: 'Deskripsi Kerusakan',
                    icon: Icons.description_outlined,
                    child: Text(
                      report.description,
                      style: const TextStyle(
                        fontSize: 13.5,
                        color: Color(0xFF333333),
                        height: 1.6,
                      ),
                    ),
                  ),

                  // Foto Kerusakan (kalau ada)
                  if (report.imageUrl != null && report.imageUrl!.isNotEmpty) ...[
                    const SizedBox(height: 14),
                    _buildSectionCard(
                      title: 'Foto Kerusakan',
                      icon: Icons.photo_outlined,
                      child: ClipRRect(
                        borderRadius: BorderRadius.circular(10),
                        child: Image.network(
                          report.imageUrl!,
                          width: double.infinity,
                          fit: BoxFit.cover,
                          errorBuilder: (_, _, _) => Container(
                            height: 100,
                            decoration: BoxDecoration(
                              color: _surface,
                              borderRadius: BorderRadius.circular(10),
                            ),
                            child: const Center(
                              child: Icon(Icons.broken_image_outlined,
                                  color: _textMuted),
                            ),
                          ),
                        ),
                      ),
                    ),
                  ],

                  const SizedBox(height: 14),

                  // ── Catatan / Feedback ───────────────────────────────────
                  // Satpam & admin: bisa edit | Mahasiswa: read-only kalau ada
                  if (_canEdit)
                    _buildFeedbackEditor()
                  else if (report.catatanTeknisi != null &&
                      report.catatanTeknisi!.isNotEmpty)
                    _buildFeedbackReadonly(report.catatanTeknisi!),

                  // ── Update Status (satpam & admin) ───────────────────────
                  if (_canEdit) ...[
                    const SizedBox(height: 14),
                    _buildStatusPicker(),
                    const SizedBox(height: 20),
                    _buildSimpanButton(),
                  ],
                ],
              ),
            ),
          ],
        )
      ),
    );
  }

  // ─── HERO HEADER ──────────────────────────────────────────────────────────
  Widget _buildHeroHeader(FacilityReportModel report, _StatusStyle statusStyle) {
    return Container(
      width: double.infinity,
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          stops: [0.0, 0.45, 1.0],
          colors: [_red, _redMid, _redDark],
        ),
      ),
      child: Stack(
        children: [
          Positioned.fill(
            child: CustomPaint(painter: _GridPainter()),
          ),
          Positioned(
            top: -45,
            right: -35,
            child: Container(
              width: 130,
              height: 130,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: _white.withValues(alpha: 0.06),
              ),
            ),
          ),
          SafeArea(
            bottom: false,
            child: Padding(
              padding: const EdgeInsets.fromLTRB(16, 10, 16, 18),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      GestureDetector(
                        onTap: () => Navigator.pop(context),
                        child: Container(
                          width: 36,
                          height: 36,
                          decoration: BoxDecoration(
                            color: _white.withValues(alpha: 0.13),
                            borderRadius: BorderRadius.circular(10),
                            border: Border.all(
                              color: _white.withValues(alpha: 0.18),
                            ),
                          ),
                          child: const Icon(
                            Icons.arrow_back_ios_new_rounded,
                            size: 15,
                            color: _white,
                          ),
                        ),
                      ),
                      const Spacer(),
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 10,
                          vertical: 5,
                        ),
                        decoration: BoxDecoration(
                          color: _white.withValues(alpha: 0.15),
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(
                            color: _white.withValues(alpha: 0.25),
                          ),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(
                              statusStyle.icon,
                              size: 13,
                              color: _white,
                            ),
                            const SizedBox(width: 5),
                            Text(
                              statusStyle.label,
                              style: const TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.w700,
                                color: _white,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),

                  const SizedBox(height: 18),

                  Text(
                    report.namaFasilitas,
                    style: const TextStyle(
                      fontSize: 21,
                      fontWeight: FontWeight.w800,
                      color: _white,
                      letterSpacing: -0.5,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),

                  const SizedBox(height: 7),

                  Row(
                    children: [
                      Icon(
                        Icons.location_on_outlined,
                        size: 14,
                        color: _white.withValues(alpha: 0.7),
                      ),
                      const SizedBox(width: 5),
                      Expanded(
                        child: Text(
                          report.location,
                          style: TextStyle(
                            fontSize: 12,
                            color: _white.withValues(alpha: 0.72),
                            fontWeight: FontWeight.w500,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  // ─── SECTION CARD ─────────────────────────────────────────────────────────
  Widget _buildSectionCard({
    required String title,
    required IconData icon,
    required Widget child,
  }) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: _white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: _border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 3, height: 14,
                decoration: BoxDecoration(
                  color: _red,
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
              const SizedBox(width: 8),
              Icon(icon, size: 15, color: _textMuted),
              const SizedBox(width: 6),
              Text(
                title,
                style: const TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w700,
                  color: _textMuted,
                  letterSpacing: 0.3,
                ),
              ),
            ],
          ),
          const SizedBox(height: 14),
          child,
        ],
      ),
    );
  }

  // ─── INFO ROW ─────────────────────────────────────────────────────────────
  Widget _buildInfoRow({
    required IconData icon,
    required String label,
    required String value,
  }) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, size: 15, color: _textMuted),
          const SizedBox(width: 10),
          SizedBox(
            width: 80,
            child: Text(
              label,
              style: const TextStyle(
                fontSize: 12,
                color: _textMuted,
                fontWeight: FontWeight.w500,
              ),
            ),
          ),
          const Text(
            ':  ',
            style: TextStyle(fontSize: 12, color: _textMuted),
          ),
          Expanded(
            child: Text(
              value,
              style: const TextStyle(
                fontSize: 12.5,
                color: _textPrimary,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDivider() => const Divider(height: 1, color: Color(0xFFF5F5F5));

  // ─── FEEDBACK EDITOR (satpam & admin) ─────────────────────────────────────
  Widget _buildFeedbackEditor() {
    return _buildSectionCard(
      title: 'Catatan / Feedback',
      icon: Icons.edit_note_rounded,
      child: TextField(
        controller: _feedbackController,
        maxLines: 4,
        style: const TextStyle(
          fontSize: 13.5,
          color: _textPrimary,
          fontWeight: FontWeight.w500,
        ),
        decoration: InputDecoration(
          hintText: 'Tulis catatan atau hasil pengecekan di sini...',
          hintStyle: const TextStyle(fontSize: 13, color: Color(0xFFCCCCCC)),
          filled: true,
          fillColor: _surface,
          contentPadding: const EdgeInsets.all(14),
          enabledBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(12),
            borderSide: const BorderSide(color: Color(0xFFEEEEEE), width: 1.5),
          ),
          focusedBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(12),
            borderSide: const BorderSide(color: _red, width: 1.5),
          ),
        ),
      ),
    );
  }

  // ─── FEEDBACK READ-ONLY (mahasiswa) ───────────────────────────────────────
  Widget _buildFeedbackReadonly(String catatan) {
    return _buildSectionCard(
      title: 'Catatan dari Satpam',
      icon: Icons.comment_outlined,
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: const Color(0xFFFFFBEB),
          borderRadius: BorderRadius.circular(10),
          border: Border.all(color: const Color(0xFFF59E0B).withValues(alpha: 0.3)),
        ),
        child: Text(
          catatan,
          style: const TextStyle(
            fontSize: 13.5,
            color: Color(0xFF78350F),
            height: 1.6,
            fontWeight: FontWeight.w500,
          ),
        ),
      ),
    );
  }

  // ─── STATUS PICKER (satpam & admin) ───────────────────────────────────────
  Widget _buildStatusPicker() {
    final statuses = [
      ('pending', 'Menunggu', Icons.access_time_rounded, const Color(0xFFF59E0B), const Color(0xFFFFFBEB)),
      ('in_progress', 'Sedang Diperbaiki', Icons.autorenew_rounded, const Color(0xFF3B82F6), const Color(0xFFEFF6FF)),
      ('completed', 'Sudah Diperbaiki', Icons.check_circle_outline_rounded, const Color(0xFF10B981), const Color(0xFFECFDF5)),
    ];

    return _buildSectionCard(
      title: 'Ubah Status',
      icon: Icons.swap_horiz_rounded,
      child: Column(
        children: statuses.map((s) {
          final (value, label, icon, color, bg) = s;
          final isSelected = _selectedStatus == value;

          return GestureDetector(
            onTap: () => setState(() => _selectedStatus = value),
            child: AnimatedContainer(
              duration: const Duration(milliseconds: 180),
              margin: const EdgeInsets.only(bottom: 8),
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
              decoration: BoxDecoration(
                color: isSelected ? bg : _surface,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(
                  color: isSelected ? color.withValues(alpha: 0.5) : const Color(0xFFEEEEEE),
                  width: isSelected ? 1.5 : 1,
                ),
              ),
              child: Row(
                children: [
                  Container(
                    width: 34, height: 34,
                    decoration: BoxDecoration(
                      color: isSelected ? color.withValues(alpha: 0.15) : const Color(0xFFEEEEEE),
                      borderRadius: BorderRadius.circular(9),
                    ),
                    alignment: Alignment.center,
                    child: Icon(icon,
                        color: isSelected ? color : _textMuted, size: 16),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Text(
                      label,
                      style: TextStyle(
                        fontSize: 13.5,
                        fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
                        color: isSelected ? color : _textPrimary,
                      ),
                    ),
                  ),
                  if (isSelected)
                    Icon(Icons.check_circle_rounded, color: color, size: 18),
                ],
              ),
            ),
          );
        }).toList(),
      ),
    );
  }

  // ─── TOMBOL SIMPAN ────────────────────────────────────────────────────────
  Widget _buildSimpanButton() {
    final statusChanged = _selectedStatus != widget.report.status;
    final feedbackChanged = _feedbackController.text.trim() !=
        (widget.report.catatanTeknisi ?? '');
    final hasChanges = statusChanged || feedbackChanged;

    return SizedBox(
      width: double.infinity,
      height: 50,
      child: DecoratedBox(
        decoration: BoxDecoration(
          gradient: hasChanges
              ? const LinearGradient(
                  colors: [_red, _redMid],
                  begin: Alignment.centerLeft,
                  end: Alignment.centerRight,
                )
              : const LinearGradient(
                  colors: [Color(0xFFDDDDDD), Color(0xFFCCCCCC)],
                ),
          borderRadius: BorderRadius.circular(13),
        ),
        child: ElevatedButton(
          onPressed: (hasChanges && !_isSubmitting) ? _simpanPerubahan : null,
          style: ElevatedButton.styleFrom(
            backgroundColor: Colors.transparent,
            shadowColor: Colors.transparent,
            disabledBackgroundColor: Colors.transparent,
            elevation: 0,
            shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(13)),
          ),
          child: _isSubmitting
              ? const SizedBox(
                  height: 20, width: 20,
                  child: CircularProgressIndicator(
                      color: _white, strokeWidth: 2),
                )
              : Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.save_rounded,
                        size: 16,
                        color: hasChanges ? _white : _textMuted),
                    const SizedBox(width: 8),
                    Text(
                      'Simpan Perubahan',
                      style: TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w700,
                        color: hasChanges ? _white : _textMuted,
                      ),
                    ),
                  ],
                ),
        ),
      ),
    );
  }
}

// ─── HELPER CLASS ─────────────────────────────────────────────────────────────
class _StatusStyle {
  final String label;
  final Color color;
  final Color bg;
  final IconData icon;

  const _StatusStyle({
    required this.label,
    required this.color,
    required this.bg,
    required this.icon,
  });
}

// ─── GRID PAINTER (sama kayak ReportFormScreen) ───────────────────────────────
class _GridPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = Colors.white.withValues(alpha: 0.04)
      ..strokeWidth = 1;
    const step = 32.0;
    for (double x = 0; x < size.width; x += step) {
      canvas.drawLine(Offset(x, 0), Offset(x, size.height), paint);
    }
    for (double y = 0; y < size.height; y += step) {
      canvas.drawLine(Offset(0, y), Offset(size.width, y), paint);
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}