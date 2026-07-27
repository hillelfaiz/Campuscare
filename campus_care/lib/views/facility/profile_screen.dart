import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';

import '../../models/facility_report_model.dart';
import '../../providers/auth_provider.dart';
import '../../providers/facility_provider.dart';
import '../facility/report_form_screen.dart';
import '../facility/report_list_screen.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  static const _red = Color(0xFFE4002B);
  static const _redMid = Color(0xFFB8001F);
  static const _bg = Color(0xFFF7F7F5);
  static const _white = Colors.white;
  static const _border = Color(0xFFEFEFEF);
  static const _textPrimary = Color(0xFF111111);
  static const _textMuted = Color(0xFFAAAAAA);

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<FacilityProvider>(context, listen: false).fetchReports();
    });
  }

  @override
  Widget build(BuildContext context) {
    SystemChrome.setSystemUIOverlayStyle(
      const SystemUiOverlayStyle(
        statusBarColor: Colors.transparent,
        statusBarIconBrightness: Brightness.dark,
      ),
    );

    final auth = Provider.of<AuthProvider>(context);
    final nama = auth.currentUser?.nama ?? 'Mahasiswa';
    final nim = auth.currentUser?.nim ?? '-';
    final photoUrl = auth.currentUser?.fotoProfilUrl;
    final initial = nama.isNotEmpty ? nama[0].toUpperCase() : 'M';
    final userId = auth.currentUser?.id;

    return Scaffold(
      backgroundColor: _bg,
      appBar: _buildAppBar(context, photoUrl, initial),
      body: Consumer<FacilityProvider>(
        builder: (context, provider, _) {
          final List<FacilityReportModel> myReports = userId != null
              ? provider.reports.where((r) => r.reporterId == userId).toList()
              : <FacilityReportModel>[];

          final total = myReports.length;
          final diproses =
              myReports.where((r) => r.status == 'in_progress').length;
          final selesai =
              myReports.where((r) => r.status == 'completed').length;
          final terbaru = myReports.take(3).toList();

          return ListView(
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 32),
            children: [
              _buildCompactGreeting(nama, nim, photoUrl, initial),

              const SizedBox(height: 18),

              _buildActionIntro(),

              const SizedBox(height: 10),

              _buildPrimaryActionCard(
                onTap: () async {
                  await Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (_) => const ReportFormScreen(),
                    ),
                  );

                  if (context.mounted) {
                    Provider.of<FacilityProvider>(context, listen: false)
                        .fetchReports();
                  }
                },
              ),

              const SizedBox(height: 20),

              if (provider.isLoading)
                const Padding(
                  padding: EdgeInsets.symmetric(vertical: 24),
                  child: Center(
                    child: CircularProgressIndicator(
                      color: _red,
                      strokeWidth: 2,
                    ),
                  ),
                )
              else ...[
                _buildSectionHeader('Ringkasan Laporan'),
                const SizedBox(height: 10),
                _buildStatsRow(total, diproses, selesai),

                const SizedBox(height: 20),

                _buildSectionHeader('Akses Cepat'),
                const SizedBox(height: 10),
                _buildMenuCard(
                  title: 'Riwayat Laporan',
                  subtitle: 'Lihat status dan perkembangan laporan kamu',
                  icon: Icons.history_rounded,
                  onTap: () async {
                    await Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (_) =>
                            const ReportListScreen(role: 'mahasiswa'),
                      ),
                    );

                    if (context.mounted) {
                      Provider.of<FacilityProvider>(context, listen: false)
                          .fetchReports();
                    }
                  },
                ),

                const SizedBox(height: 20),

                _buildSectionHeader('Laporan Terbaru'),
                const SizedBox(height: 10),
                if (terbaru.isEmpty)
                  _buildEmptyState()
                else
                  ...terbaru.map(
                    (r) => Padding(
                      padding: const EdgeInsets.only(bottom: 8),
                      child: _buildRecentReportCard(
                        namaFasilitas: r.title,
                        deskripsi: r.description,
                        status: r.statusLabel,
                        tanggal: (r.createdAt != null &&
                                r.createdAt!.length >= 10)
                            ? r.createdAt!.substring(0, 10)
                            : '-',
                      ),
                    ),
                  ),
              ],
            ],
          );
        },
      ),
    );
  }

  // ─── APP BAR ──────────────────────────────────────────────────────────────
  PreferredSizeWidget _buildAppBar(
    BuildContext context,
    String? photoUrl,
    String initial,
  ) {
    return AppBar(
      backgroundColor: _white,
      elevation: 0,
      scrolledUnderElevation: 0.5,
      shadowColor: Colors.black.withValues(alpha: 0.06),
      titleSpacing: 16,
      title: Row(
        children: [
          Container(
            width: 34,
            height: 34,
            padding: const EdgeInsets.all(5),
            decoration: BoxDecoration(
              color: const Color(0xFFFFF0F2),
              borderRadius: BorderRadius.circular(9),
              border: Border.all(
                color: const Color(0xFFFFCDD5),
                width: 1,
              ),
            ),
            child: Image.asset(
              'assets/image/LOGO_TRANSPARAN.png',
              fit: BoxFit.contain,
            ),
          ),
          const SizedBox(width: 10),
          const Text(
            'Campus Care',
            style: TextStyle(
              fontSize: 15,
              fontWeight: FontWeight.w800,
              color: _textPrimary,
              letterSpacing: -0.3,
            ),
          ),
        ],
      ),
      actions: [
        GestureDetector(
          onTap: () => Navigator.push(
            context,
            MaterialPageRoute(builder: (_) => const ProfileScreen()),
          ),
          child: Container(
            margin: const EdgeInsets.only(right: 16),
            width: 36,
            height: 36,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              border: Border.all(
                color: const Color(0xFFFFCDD5),
                width: 2,
              ),
            ),
            child: ClipOval(
              child: photoUrl != null && photoUrl.isNotEmpty
                  ? Image.network(
                      photoUrl,
                      fit: BoxFit.cover,
                      errorBuilder: (_, _, _) => _buildInitialAvatar(
                        initial,
                        size: 13,
                        bg: const Color(0xFFFFF0F2),
                        fg: _red,
                      ),
                    )
                  : _buildInitialAvatar(
                      initial,
                      size: 13,
                      bg: const Color(0xFFFFF0F2),
                      fg: _red,
                    ),
            ),
          ),
        ),
      ],
    );
  }

  // ─── AVATAR INISIAL ───────────────────────────────────────────────────────
  Widget _buildInitialAvatar(
    String initial, {
    required double size,
    required Color bg,
    required Color fg,
  }) {
    return Container(
      color: bg,
      alignment: Alignment.center,
      child: Text(
        initial,
        style: TextStyle(
          fontSize: size,
          fontWeight: FontWeight.w800,
          color: fg,
        ),
      ),
    );
  }

  // ─── GREETING COMPACT ─────────────────────────────────────────────────────
  Widget _buildCompactGreeting(
    String nama,
    String nim,
    String? photoUrl,
    String initial,
  ) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: _white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: _border),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.025),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            width: 54,
            height: 54,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              border: Border.all(
                color: const Color(0xFFFFCDD5),
                width: 2,
              ),
            ),
            child: ClipOval(
              child: photoUrl != null && photoUrl.isNotEmpty
                  ? Image.network(
                      photoUrl,
                      fit: BoxFit.cover,
                      errorBuilder: (_, _, _) => _buildInitialAvatar(
                        initial,
                        size: 18,
                        bg: const Color(0xFFFFF0F2),
                        fg: _red,
                      ),
                    )
                  : _buildInitialAvatar(
                      initial,
                      size: 18,
                      bg: const Color(0xFFFFF0F2),
                      fg: _red,
                    ),
            ),
          ),
          const SizedBox(width: 13),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Halo, $nama 👋',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w800,
                    color: _textPrimary,
                    letterSpacing: -0.3,
                  ),
                ),
                const SizedBox(height: 3),
                Text(
                  'NIM: $nim',
                  style: const TextStyle(
                    fontSize: 12,
                    color: _textMuted,
                    fontWeight: FontWeight.w500,
                  ),
                ),
                const SizedBox(height: 9),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 9,
                    vertical: 4,
                  ),
                  decoration: BoxDecoration(
                    color: const Color(0xFFFFF0F2),
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(
                      color: const Color(0xFFFFCDD5),
                    ),
                  ),
                  child: const Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(
                        Icons.school_rounded,
                        size: 12,
                        color: _red,
                      ),
                      SizedBox(width: 5),
                      Text(
                        'Mahasiswa',
                        style: TextStyle(
                          fontSize: 10.5,
                          color: _red,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  // ─── ACTION INTRO ─────────────────────────────────────────────────────────
  Widget _buildActionIntro() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Hari ini ada laporan apa nichh?',
          style: TextStyle(
            fontSize: 15,
            fontWeight: FontWeight.w800,
            color: _textPrimary,
            letterSpacing: -0.3,
          ),
        ),
        const SizedBox(height: 4),
        Text(
          'Laporkan kendala fasilitas kampus agar segera ditindaklanjuti.',
          style: TextStyle(
            fontSize: 12,
            color: _textMuted,
            fontWeight: FontWeight.w500,
            height: 1.35,
          ),
        ),
      ],
    );
  }

  // ─── PRIMARY ACTION ───────────────────────────────────────────────────────
  Widget _buildPrimaryActionCard({
    required VoidCallback onTap,
  }) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(20),
        child: Ink(
          width: double.infinity,
          padding: const EdgeInsets.all(18),
          decoration: BoxDecoration(
            gradient: const LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [_red, _redMid],
            ),
            borderRadius: BorderRadius.circular(20),
            boxShadow: [
              BoxShadow(
                color: _red.withValues(alpha: 0.18),
                blurRadius: 20,
                offset: const Offset(0, 9),
              ),
            ],
          ),
          child: Stack(
            children: [
              Positioned(
                top: -32,
                right: -24,
                child: Container(
                  width: 105,
                  height: 105,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: _white.withValues(alpha: 0.08),
                  ),
                ),
              ),
              Positioned(
                bottom: -38,
                right: 32,
                child: Container(
                  width: 85,
                  height: 85,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    border: Border.all(
                      color: _white.withValues(alpha: 0.10),
                      width: 1,
                    ),
                  ),
                ),
              ),
              Row(
                children: [
                  Container(
                    width: 52,
                    height: 52,
                    decoration: BoxDecoration(
                      color: _white.withValues(alpha: 0.18),
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(
                        color: _white.withValues(alpha: 0.24),
                      ),
                    ),
                    child: const Icon(
                      Icons.report_problem_outlined,
                      color: _white,
                      size: 25,
                    ),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'Lapor Fasilitas Rusak',
                          style: TextStyle(
                            fontSize: 15.5,
                            fontWeight: FontWeight.w800,
                            color: _white,
                            letterSpacing: -0.2,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          'Buat laporan baru dengan cepat',
                          style: TextStyle(
                            fontSize: 12,
                            color: _white.withValues(alpha: 0.76),
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ],
                    ),
                  ),
                  Container(
                    width: 34,
                    height: 34,
                    decoration: BoxDecoration(
                      color: _white.withValues(alpha: 0.18),
                      borderRadius: BorderRadius.circular(11),
                    ),
                    child: const Icon(
                      Icons.arrow_forward_rounded,
                      color: _white,
                      size: 18,
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  // ─── STATS ROW ────────────────────────────────────────────────────────────
  Widget _buildStatsRow(int total, int diproses, int selesai) {
    return Row(
      children: [
        Expanded(
          child: _buildStatCard(
            label: 'Total',
            value: '$total',
            icon: Icons.assignment_outlined,
            color: const Color(0xFF3B82F6),
            bg: const Color(0xFFEFF6FF),
          ),
        ),
        const SizedBox(width: 10),
        Expanded(
          child: _buildStatCard(
            label: 'Diproses',
            value: '$diproses',
            icon: Icons.autorenew_rounded,
            color: const Color(0xFFF59E0B),
            bg: const Color(0xFFFFFBEB),
          ),
        ),
        const SizedBox(width: 10),
        Expanded(
          child: _buildStatCard(
            label: 'Selesai',
            value: '$selesai',
            icon: Icons.check_circle_outline_rounded,
            color: const Color(0xFF10B981),
            bg: const Color(0xFFECFDF5),
          ),
        ),
      ],
    );
  }

  Widget _buildStatCard({
    required String label,
    required String value,
    required IconData icon,
    required Color color,
    required Color bg,
  }) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 13, horizontal: 12),
      decoration: BoxDecoration(
        color: _white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: _border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 28,
            height: 28,
            decoration: BoxDecoration(
              color: bg,
              borderRadius: BorderRadius.circular(8),
            ),
            alignment: Alignment.center,
            child: Icon(icon, color: color, size: 15),
          ),
          const SizedBox(height: 8),
          Text(
            value,
            style: const TextStyle(
              fontSize: 21,
              fontWeight: FontWeight.w800,
              color: _textPrimary,
              letterSpacing: -0.5,
            ),
          ),
          const SizedBox(height: 2),
          Text(
            label,
            style: const TextStyle(
              fontSize: 10.5,
              color: _textMuted,
              fontWeight: FontWeight.w600,
            ),
          ),
        ],
      ),
    );
  }

  // ─── SECTION HEADER ───────────────────────────────────────────────────────
  Widget _buildSectionHeader(String title) {
    return Row(
      children: [
        Container(
          width: 3,
          height: 16,
          decoration: BoxDecoration(
            color: _red,
            borderRadius: BorderRadius.circular(2),
          ),
        ),
        const SizedBox(width: 8),
        Text(
          title,
          style: const TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.w700,
            color: _textPrimary,
            letterSpacing: -0.2,
          ),
        ),
      ],
    );
  }

  // ─── EMPTY STATE ──────────────────────────────────────────────────────────
  Widget _buildEmptyState() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(vertical: 28, horizontal: 18),
      decoration: BoxDecoration(
        color: _white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: _border),
      ),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(14),
            decoration: const BoxDecoration(
              color: Color(0xFFF5F5F5),
              shape: BoxShape.circle,
            ),
            child: const Icon(
              Icons.inbox_rounded,
              size: 28,
              color: _textMuted,
            ),
          ),
          const SizedBox(height: 10),
          const Text(
            'Belum ada laporan',
            style: TextStyle(
              fontSize: 13,
              color: _textMuted,
              fontWeight: FontWeight.w600,
            ),
          ),
          const SizedBox(height: 4),
          const Text(
            'Laporan terbaru kamu akan muncul di sini.',
            textAlign: TextAlign.center,
            style: TextStyle(
              fontSize: 11,
              color: _textMuted,
            ),
          ),
        ],
      ),
    );
  }

  // ─── MENU CARD ────────────────────────────────────────────────────────────
  Widget _buildMenuCard({
    required String title,
    required String subtitle,
    required IconData icon,
    required VoidCallback onTap,
  }) {
    return Material(
      color: _white,
      borderRadius: BorderRadius.circular(14),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(14),
        child: Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: _border),
          ),
          child: Row(
            children: [
              Container(
                width: 44,
                height: 44,
                decoration: BoxDecoration(
                  color: const Color(0xFFFFF0F2),
                  borderRadius: BorderRadius.circular(12),
                ),
                alignment: Alignment.center,
                child: Icon(icon, color: _red, size: 22),
              ),
              const SizedBox(width: 13),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: const TextStyle(
                        fontSize: 13.5,
                        fontWeight: FontWeight.w700,
                        color: _textPrimary,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      subtitle,
                      style: const TextStyle(
                        fontSize: 11.5,
                        color: _textMuted,
                      ),
                    ),
                  ],
                ),
              ),
              Container(
                width: 26,
                height: 26,
                decoration: BoxDecoration(
                  color: const Color(0xFFF5F5F5),
                  borderRadius: BorderRadius.circular(8),
                ),
                alignment: Alignment.center,
                child: const Icon(
                  Icons.chevron_right_rounded,
                  color: Color(0xFFCCCCCC),
                  size: 18,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  // ─── RECENT REPORT CARD ───────────────────────────────────────────────────
  Widget _buildRecentReportCard({
    required String namaFasilitas,
    required String deskripsi,
    required String status,
    required String tanggal,
  }) {
    late Color statusColor;
    late Color statusBg;
    late IconData statusIcon;

    switch (status) {
      case 'Sedang Diperbaiki':
        statusColor = const Color(0xFF3B82F6);
        statusBg = const Color(0xFFEFF6FF);
        statusIcon = Icons.autorenew_rounded;
        break;
      case 'Sudah Diperbaiki':
        statusColor = const Color(0xFF10B981);
        statusBg = const Color(0xFFECFDF5);
        statusIcon = Icons.check_circle_outline_rounded;
        break;
      default:
        statusColor = const Color(0xFFF59E0B);
        statusBg = const Color(0xFFFFFBEB);
        statusIcon = Icons.access_time_rounded;
    }

    return Container(
      padding: const EdgeInsets.all(13),
      decoration: BoxDecoration(
        color: _white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: _border),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 36,
            height: 36,
            decoration: BoxDecoration(
              color: statusBg,
              borderRadius: BorderRadius.circular(10),
            ),
            alignment: Alignment.center,
            child: Icon(statusIcon, color: statusColor, size: 17),
          ),
          const SizedBox(width: 11),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  namaFasilitas,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                    fontSize: 12.5,
                    fontWeight: FontWeight.w700,
                    color: _textPrimary,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  deskripsi,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                    fontSize: 11,
                    color: _textMuted,
                  ),
                ),
                const SizedBox(height: 8),
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 8,
                        vertical: 3,
                      ),
                      decoration: BoxDecoration(
                        color: statusBg,
                        borderRadius: BorderRadius.circular(20),
                      ),
                      child: Text(
                        status,
                        style: TextStyle(
                          fontSize: 10,
                          fontWeight: FontWeight.w700,
                          color: statusColor,
                        ),
                      ),
                    ),
                    const Spacer(),
                    Text(
                      tanggal,
                      style: const TextStyle(
                        fontSize: 10.5,
                        color: Color(0xFFCCCCCC),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}