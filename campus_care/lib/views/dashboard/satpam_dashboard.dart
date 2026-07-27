import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';

import '../../providers/auth_provider.dart';
import '../../providers/facility_provider.dart';
import '../facility/report_list_screen.dart';
import 'profile_screen.dart';

class SatpamDashboard extends StatefulWidget {
  const SatpamDashboard({super.key});

  @override
  State<SatpamDashboard> createState() => _SatpamDashboardState();
}

class _SatpamDashboardState extends State<SatpamDashboard> {
  static const _red = Color(0xFFE4002B);
  static const _bg = Color(0xFFF7F7F5);
  static const _white = Colors.white;
  static const _border = Color(0xFFEFEFEF);
  static const _textPrimary = Color(0xFF111111);
  static const _textMuted = Color(0xFFAAAAAA);
  static const _dark = Color(0xFF111827);
  static const _darkSoft = Color(0xFF1F2937);

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
    final nama = auth.currentUser?.nama ?? 'Satpam';
    final photoUrl = auth.currentUser?.fotoProfilUrl;
    final initial = nama.isNotEmpty ? nama[0].toUpperCase() : 'S';

    return Scaffold(
      backgroundColor: _bg,
      appBar: _buildAppBar(context, photoUrl, initial),
      body: Consumer<FacilityProvider>(
        builder: (context, provider, _) {
          final reports = provider.reports;

          final total = reports.length;
          final menunggu = reports.where((r) => r.status == 'pending').length;
          final diproses =
              reports.where((r) => r.status == 'in_progress').length;
          final selesai =
              reports.where((r) => r.status == 'completed').length;

          final laporanMasuk = reports
              .where((r) => r.status == 'pending')
              .take(3)
              .toList();

          return ListView(
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 32),
            children: [
              _buildGreetingCard(nama, photoUrl, initial),

              const SizedBox(height: 16),

              if (provider.isLoading)
                const Padding(
                  padding: EdgeInsets.symmetric(vertical: 32),
                  child: Center(
                    child: CircularProgressIndicator(
                      color: _red,
                      strokeWidth: 2,
                    ),
                  ),
                )
              else ...[
                _buildDutyAlert(
                  pending: menunggu,
                  onTap: () async {
                    await Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (_) => const ReportListScreen(
                          role: 'satpam',
                          filterStatus: 'pending',
                        ),
                      ),
                    );

                    if (context.mounted) {
                      Provider.of<FacilityProvider>(context, listen: false)
                          .fetchReports();
                    }
                  },
                ),

                const SizedBox(height: 20),

                _buildSectionHeader('Ringkasan Tugas'),
                const SizedBox(height: 10),
                _buildStatsGrid(total, menunggu, diproses, selesai),

                const SizedBox(height: 20),

                _buildSectionHeader('Laporan Masuk'),
                const SizedBox(height: 10),

                if (laporanMasuk.isEmpty)
                  _buildEmptyState(
                    icon: Icons.task_alt_rounded,
                    message: 'Tidak ada laporan masuk',
                    subtitle: 'Laporan baru dari mahasiswa akan muncul di sini.',
                  )
                else
                  ...laporanMasuk.map(
                    (r) => Padding(
                      padding: const EdgeInsets.only(bottom: 8),
                      child: _buildReportCard(
                        namaFasilitas: r.title,
                        lokasi: r.location,
                        deskripsi: r.description,
                        pelapor: r.user?['nama'] ?? 'Tidak diketahui',
                        tanggal: (r.createdAt != null &&
                                r.createdAt!.length >= 10)
                            ? r.createdAt!.substring(0, 10)
                            : '-',
                      ),
                    ),
                  ),

                const SizedBox(height: 20),

                _buildSectionHeader('Aksi Cepat'),
                const SizedBox(height: 10),
                _buildQuickActionRow(context),
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

  // ─── GREETING CARD ────────────────────────────────────────────────────────
  Widget _buildGreetingCard(
    String nama,
    String? photoUrl,
    String initial,
  ) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: _dark,
        borderRadius: BorderRadius.circular(18),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.12),
            blurRadius: 16,
            offset: const Offset(0, 6),
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
                color: _white.withValues(alpha: 0.35),
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
                        bg: _white.withValues(alpha: 0.12),
                        fg: _white,
                      ),
                    )
                  : _buildInitialAvatar(
                      initial,
                      size: 18,
                      bg: _white.withValues(alpha: 0.12),
                      fg: _white,
                    ),
            ),
          ),
          const SizedBox(width: 13),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Selamat bertugas, $nama',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w800,
                    color: _white,
                    letterSpacing: -0.3,
                  ),
                ),
                const SizedBox(height: 3),
                Text(
                  'Cek dan tindak lanjuti laporan fasilitas hari ini.',
                  style: TextStyle(
                    fontSize: 12,
                    color: _white.withValues(alpha: 0.62),
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
                    color: _red.withValues(alpha: 0.16),
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(
                      color: _red.withValues(alpha: 0.35),
                    ),
                  ),
                  child: const Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(
                        Icons.security_rounded,
                        size: 12,
                        color: Color(0xFFFFB4C0),
                      ),
                      SizedBox(width: 5),
                      Text(
                        'Satpam',
                        style: TextStyle(
                          fontSize: 10.5,
                          color: Color(0xFFFFB4C0),
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

  // ─── DUTY ALERT ───────────────────────────────────────────────────────────
  Widget _buildDutyAlert({
    required int pending,
    required VoidCallback onTap,
  }) {
    final hasPending = pending > 0;

    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(18),
        child: Ink(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color:
                hasPending ? const Color(0xFFFFFBEB) : const Color(0xFFECFDF5),
            borderRadius: BorderRadius.circular(18),
            border: Border.all(
              color: hasPending
                  ? const Color(0xFFFDE68A)
                  : const Color(0xFFBBF7D0),
            ),
          ),
          child: Row(
            children: [
              Container(
                width: 46,
                height: 46,
                decoration: BoxDecoration(
                  color: hasPending
                      ? const Color(0xFFFEF3C7)
                      : const Color(0xFFD1FAE5),
                  borderRadius: BorderRadius.circular(14),
                ),
                child: Icon(
                  hasPending
                      ? Icons.report_problem_outlined
                      : Icons.check_circle_outline_rounded,
                  color: hasPending
                      ? const Color(0xFFF59E0B)
                      : const Color(0xFF10B981),
                  size: 24,
                ),
              ),
              const SizedBox(width: 13),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      hasPending
                          ? '$pending laporan perlu dicek'
                          : 'Tidak ada laporan perlu dicek',
                      style: TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w800,
                        color: hasPending
                            ? const Color(0xFF92400E)
                            : const Color(0xFF047857),
                        letterSpacing: -0.2,
                      ),
                    ),
                    const SizedBox(height: 3),
                    Text(
                      hasPending
                          ? 'Buka daftar laporan dan mulai tindak lanjuti.'
                          : 'Semua laporan sudah aman untuk saat ini.',
                      style: TextStyle(
                        fontSize: 11.5,
                        fontWeight: FontWeight.w500,
                        color: hasPending
                            ? const Color(0xFFB45309)
                            : const Color(0xFF059669),
                        height: 1.35,
                      ),
                    ),
                  ],
                ),
              ),
              Icon(
                Icons.chevron_right_rounded,
                color: hasPending
                    ? const Color(0xFFF59E0B)
                    : const Color(0xFF10B981),
              ),
            ],
          ),
        ),
      ),
    );
  }

  // ─── STATS GRID ───────────────────────────────────────────────────────────
  Widget _buildStatsGrid(
    int total,
    int menunggu,
    int diproses,
    int selesai,
  ) {
    return Column(
      children: [
        Row(
          children: [
            Expanded(
              child: _buildStatCard(
                label: 'Total',
                value: '$total',
                icon: Icons.assignment_outlined,
                color: _red,
                bg: const Color(0xFFFFF0F2),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: _buildStatCard(
                label: 'Menunggu',
                value: '$menunggu',
                icon: Icons.access_time_rounded,
                color: const Color(0xFFF59E0B),
                bg: const Color(0xFFFFFBEB),
              ),
            ),
          ],
        ),
        const SizedBox(height: 10),
        Row(
          children: [
            Expanded(
              child: _buildStatCard(
                label: 'Diproses',
                value: '$diproses',
                icon: Icons.autorenew_rounded,
                color: const Color(0xFF3B82F6),
                bg: const Color(0xFFEFF6FF),
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
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: _white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: _border),
      ),
      child: Row(
        children: [
          Container(
            width: 36,
            height: 36,
            decoration: BoxDecoration(
              color: bg,
              borderRadius: BorderRadius.circular(10),
            ),
            alignment: Alignment.center,
            child: Icon(icon, color: color, size: 17),
          ),
          const SizedBox(width: 10),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                value,
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.w800,
                  color: color,
                  letterSpacing: -0.5,
                ),
              ),
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
  Widget _buildEmptyState({
    required IconData icon,
    required String message,
    required String subtitle,
  }) {
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
            child: Icon(
              icon,
              size: 28,
              color: _textMuted,
            ),
          ),
          const SizedBox(height: 10),
          Text(
            message,
            textAlign: TextAlign.center,
            style: const TextStyle(
              fontSize: 13,
              color: _textMuted,
              fontWeight: FontWeight.w600,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            subtitle,
            textAlign: TextAlign.center,
            style: const TextStyle(
              fontSize: 11,
              color: _textMuted,
            ),
          ),
        ],
      ),
    );
  }

  // ─── REPORT CARD ──────────────────────────────────────────────────────────
  Widget _buildReportCard({
    required String namaFasilitas,
    required String lokasi,
    required String deskripsi,
    required String pelapor,
    required String tanggal,
  }) {
    return Container(
      padding: const EdgeInsets.all(14),
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
                width: 32,
                height: 32,
                decoration: BoxDecoration(
                  color: const Color(0xFFFFFBEB),
                  borderRadius: BorderRadius.circular(8),
                ),
                alignment: Alignment.center,
                child: const Icon(
                  Icons.access_time_rounded,
                  color: Color(0xFFF59E0B),
                  size: 15,
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: Text(
                  namaFasilitas,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                    fontSize: 13,
                    fontWeight: FontWeight.w700,
                    color: _textPrimary,
                  ),
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 8,
                  vertical: 3,
                ),
                decoration: BoxDecoration(
                  color: const Color(0xFFFFFBEB),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: const Text(
                  'Menunggu',
                  style: TextStyle(
                    fontSize: 10,
                    fontWeight: FontWeight.w700,
                    color: Color(0xFFF59E0B),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Row(
            children: [
              const Icon(
                Icons.location_on_outlined,
                size: 12,
                color: _textMuted,
              ),
              const SizedBox(width: 3),
              Expanded(
                child: Text(
                  lokasi,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                    fontSize: 11,
                    color: _textMuted,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 5),
          Text(
            deskripsi,
            maxLines: 2,
            overflow: TextOverflow.ellipsis,
            style: const TextStyle(
              fontSize: 12,
              color: Color(0xFF444444),
              height: 1.4,
            ),
          ),
          const SizedBox(height: 10),
          const Divider(
            height: 1,
            color: Color(0xFFF4F4F4),
          ),
          const SizedBox(height: 10),
          Row(
            children: [
              const Icon(
                Icons.person_outline_rounded,
                size: 12,
                color: _textMuted,
              ),
              const SizedBox(width: 4),
              Expanded(
                child: Text(
                  pelapor,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                    fontSize: 11,
                    color: _textMuted,
                  ),
                ),
              ),
              const Icon(
                Icons.calendar_today_outlined,
                size: 11,
                color: Color(0xFFCCCCCC),
              ),
              const SizedBox(width: 4),
              Text(
                tanggal,
                style: const TextStyle(
                  fontSize: 11,
                  color: Color(0xFFCCCCCC),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  // ─── QUICK ACTIONS ────────────────────────────────────────────────────────
  Widget _buildQuickActionRow(BuildContext context) {
    return Row(
      children: [
        Expanded(
          child: _buildQuickAction(
            label: 'Semua\nLaporan',
            icon: Icons.list_alt_rounded,
            color: _red,
            onTap: () => Navigator.push(
              context,
              MaterialPageRoute(
                builder: (_) => const ReportListScreen(role: 'satpam'),
              ),
            ),
          ),
        ),
        const SizedBox(width: 10),
        Expanded(
          child: _buildQuickAction(
            label: 'Perlu\nDicek',
            icon: Icons.warning_amber_rounded,
            color: const Color(0xFFF59E0B),
            onTap: () => Navigator.push(
              context,
              MaterialPageRoute(
                builder: (_) => const ReportListScreen(
                  role: 'satpam',
                  filterStatus: 'pending',
                ),
              ),
            ),
          ),
        ),
        const SizedBox(width: 10),
        Expanded(
          child: _buildQuickAction(
            label: 'Sudah\nSelesai',
            icon: Icons.task_alt_rounded,
            color: const Color(0xFF10B981),
            onTap: () => Navigator.push(
              context,
              MaterialPageRoute(
                builder: (_) => const ReportListScreen(
                  role: 'satpam',
                  filterStatus: 'completed',
                ),
              ),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildQuickAction({
    required String label,
    required IconData icon,
    required Color color,
    required VoidCallback onTap,
  }) {
    return Material(
      color: _white,
      borderRadius: BorderRadius.circular(14),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(14),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 14),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: _border),
          ),
          child: Column(
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: color.withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Icon(icon, color: color, size: 20),
              ),
              const SizedBox(height: 8),
              Text(
                label,
                textAlign: TextAlign.center,
                style: const TextStyle(
                  fontSize: 11,
                  fontWeight: FontWeight.w600,
                  color: _textPrimary,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}