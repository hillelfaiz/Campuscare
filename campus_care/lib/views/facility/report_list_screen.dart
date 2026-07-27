import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../providers/facility_provider.dart';
import '../../models/facility_report_model.dart';
import 'report_detail_screen.dart';

class ReportListScreen extends StatefulWidget {
  final String role;
  final String? filterStatus;

  const ReportListScreen({
    super.key,
    required this.role,
    this.filterStatus,
  });

  @override
  State<ReportListScreen> createState() => _ReportListScreenState();
}

class _ReportListScreenState extends State<ReportListScreen> {
  static const _red = Color(0xFFE4002B);
  static const _bg = Color(0xFFF7F7F5);
  static const _white = Colors.white;
  static const _border = Color(0xFFEFEFEF);
  static const _textPrimary = Color(0xFF111111);
  static const _textMuted = Color(0xFFAAAAAA);

  String _currentFilter = 'all';
  String _searchQuery = '';

  bool get _isInternalRole {
    return widget.role == 'admin' || widget.role == 'satpam';
  }

  String get _screenTitle {
    if (widget.role == 'mahasiswa') {
      return 'Laporan Publik';
    }

    if (widget.role == 'satpam') {
      return 'Daftar Tugas Laporan';
    }

    return 'Daftar Laporan';
  }

  String get _emptySubtitle {
    if (widget.role == 'mahasiswa') {
      return 'Belum ada laporan publik dari mahasiswa.';
    }

    return 'Belum ada laporan yang sesuai kriteria.';
  }

  @override
  void initState() {
    super.initState();

    if (widget.filterStatus != null) {
      _currentFilter = widget.filterStatus!;
    }

    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<FacilityProvider>(context, listen: false).fetchReports();
    });
  }

  void _goToDetail(FacilityReportModel report) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => ReportDetailScreen(
          report: report,
          role: widget.role,
        ),
      ),
    ).then((_) {
      if (mounted) {
        Provider.of<FacilityProvider>(context, listen: false).fetchReports();
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: _bg,
      appBar: AppBar(
        backgroundColor: _white,
        elevation: 0,
        scrolledUnderElevation: 0.5,
        iconTheme: const IconThemeData(color: _textPrimary),
        title: Text(
          _screenTitle,
          style: const TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.w700,
            color: _textPrimary,
            letterSpacing: -0.3,
          ),
        ),
      ),
      body: Consumer<FacilityProvider>(
        builder: (context, provider, _) {
          var filteredReports = provider.reports;

          // Mahasiswa melihat semua laporan publik.
          // Jadi tidak difilter berdasarkan reporterId.

          if (_currentFilter != 'all') {
            filteredReports = filteredReports
                .where((r) => r.status == _currentFilter)
                .toList();
          }

          if (_searchQuery.trim().isNotEmpty) {
            final query = _searchQuery.toLowerCase().trim();

            filteredReports = filteredReports.where((report) {
              final title = report.title.toLowerCase();
              final description = report.description.toLowerCase();
              final location = report.location.toLowerCase();
              final status = report.statusLabel.toLowerCase();
              final feedback =
                  (report.catatanTeknisi ?? '').toString().toLowerCase();
              final pelapor =
                  (report.user?['nama'] ?? '').toString().toLowerCase();

              return title.contains(query) ||
                  description.contains(query) ||
                  location.contains(query) ||
                  status.contains(query) ||
                  feedback.contains(query) ||
                  pelapor.contains(query);
            }).toList();
          }

          if (_isInternalRole) {
            filteredReports = [...filteredReports];

            filteredReports.sort((a, b) {
              final aPriority = _priorityRank(a.priorityLevel);
              final bPriority = _priorityRank(b.priorityLevel);

              if (aPriority != bPriority) {
                return bPriority.compareTo(aPriority);
              }

              final aCount = a.uniqueReporterCount ?? 1;
              final bCount = b.uniqueReporterCount ?? 1;

              if (aCount != bCount) {
                return bCount.compareTo(aCount);
              }

              return 0;
            });
          }

          return Column(
            children: [
              _buildSearchBox(),
              _buildFilterSection(),
              Expanded(
                child: provider.isLoading
                    ? const Center(
                        child: CircularProgressIndicator(
                          color: _red,
                          strokeWidth: 2,
                        ),
                      )
                    : filteredReports.isEmpty
                        ? _buildEmptyState()
                        : RefreshIndicator(
                            color: _red,
                            onRefresh: () => provider.fetchReports(),
                            child: ListView.builder(
                              padding:
                                  const EdgeInsets.fromLTRB(16, 4, 16, 32),
                              itemCount: filteredReports.length,
                              itemBuilder: (context, index) {
                                final report = filteredReports[index];
                                return _buildReportCard(report);
                              },
                            ),
                          ),
              ),
            ],
          );
        },
      ),
    );
  }

  Widget _buildSearchBox() {
    return Container(
      margin: const EdgeInsets.fromLTRB(16, 14, 16, 0),
      decoration: BoxDecoration(
        color: _white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: _border),
      ),
      child: TextField(
        onChanged: (value) {
          setState(() {
            _searchQuery = value;
          });
        },
        style: const TextStyle(
          fontSize: 13,
          color: _textPrimary,
          fontWeight: FontWeight.w500,
        ),
        decoration: InputDecoration(
          hintText: widget.role == 'mahasiswa'
              ? 'Cari laporan publik...'
              : 'Cari laporan, lokasi, pelapor...',
          hintStyle: const TextStyle(
            fontSize: 13,
            color: _textMuted,
            fontWeight: FontWeight.w500,
          ),
          prefixIcon: const Icon(
            Icons.search_rounded,
            color: _textMuted,
            size: 20,
          ),
          suffixIcon: _searchQuery.isNotEmpty
              ? IconButton(
                  onPressed: () {
                    setState(() {
                      _searchQuery = '';
                    });
                  },
                  icon: const Icon(
                    Icons.close_rounded,
                    color: _textMuted,
                    size: 18,
                  ),
                )
              : null,
          border: InputBorder.none,
          contentPadding: const EdgeInsets.symmetric(
            horizontal: 14,
            vertical: 13,
          ),
        ),
      ),
    );
  }

  Widget _buildFilterSection() {
    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      child: Row(
        children: [
          _buildFilterChip('Semua', 'all'),
          const SizedBox(width: 8),
          _buildFilterChip('Menunggu', 'pending'),
          const SizedBox(width: 8),
          _buildFilterChip('Diproses', 'in_progress'),
          const SizedBox(width: 8),
          _buildFilterChip('Selesai', 'completed'),
        ],
      ),
    );
  }

  Widget _buildFilterChip(String label, String value) {
    final isSelected = _currentFilter == value;

    return GestureDetector(
      onTap: () => setState(() => _currentFilter = value),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
        decoration: BoxDecoration(
          color: isSelected ? _red : _white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: isSelected ? _red : _border),
        ),
        child: Text(
          label,
          style: TextStyle(
            fontSize: 13,
            fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
            color: isSelected ? _white : _textMuted,
          ),
        ),
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 28),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(16),
              decoration: const BoxDecoration(
                color: Color(0xFFEFEFEF),
                shape: BoxShape.circle,
              ),
              child: const Icon(
                Icons.inbox_rounded,
                size: 40,
                color: _textMuted,
              ),
            ),
            const SizedBox(height: 16),
            const Text(
              'Tidak ada laporan',
              style: TextStyle(
                fontSize: 15,
                fontWeight: FontWeight.w600,
                color: _textPrimary,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              _emptySubtitle,
              textAlign: TextAlign.center,
              style: const TextStyle(
                fontSize: 13,
                color: _textMuted,
                height: 1.35,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildReportCard(FacilityReportModel report) {
    final statusStyle = _getStatusStyle(report.statusLabel);

    final tanggal = (report.createdAt != null && report.createdAt!.length >= 10)
        ? report.createdAt!.substring(0, 10)
        : '-';

    final pelapor = report.user?['nama'] ?? 'Tidak diketahui';

    return GestureDetector(
      onTap: () => _goToDetail(report),
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: _white,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: _border),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.015),
              blurRadius: 8,
              offset: const Offset(0, 3),
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _buildCardHeader(report, statusStyle),
            const SizedBox(height: 12),
            Text(
              report.description,
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(
                fontSize: 13,
                color: Color(0xFF444444),
                height: 1.4,
              ),
            ),
            _buildFeedbackPreview(report),
            if (_isInternalRole) ...[
              const SizedBox(height: 12),
              _buildInternalPrioritySection(report),
            ],
            const SizedBox(height: 12),
            const Divider(height: 1, color: Color(0xFFF0F0F0)),
            const SizedBox(height: 12),
            _buildFooter(pelapor, tanggal),
          ],
        ),
      ),
    );
  }

  Widget _buildCardHeader(
    FacilityReportModel report,
    _StatusStyle statusStyle,
  ) {
    return Row(
      children: [
        Container(
          width: 36,
          height: 36,
          decoration: BoxDecoration(
            color: statusStyle.backgroundColor,
            borderRadius: BorderRadius.circular(10),
          ),
          child: Icon(
            statusStyle.icon,
            color: statusStyle.color,
            size: 18,
          ),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                report.title,
                style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w700,
                  color: _textPrimary,
                ),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
              ),
              const SizedBox(height: 2),
              Row(
                children: [
                  const Icon(
                    Icons.location_on_outlined,
                    size: 12,
                    color: _textMuted,
                  ),
                  const SizedBox(width: 4),
                  Expanded(
                    child: Text(
                      report.location,
                      style: const TextStyle(
                        fontSize: 11,
                        color: _textMuted,
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
        const SizedBox(width: 8),
        Row(
          children: [
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
              decoration: BoxDecoration(
                color: statusStyle.backgroundColor,
                borderRadius: BorderRadius.circular(20),
              ),
              child: Text(
                report.statusLabel,
                style: TextStyle(
                  fontSize: 10,
                  fontWeight: FontWeight.w700,
                  color: statusStyle.color,
                ),
              ),
            ),
            const SizedBox(width: 6),
            const Icon(
              Icons.chevron_right_rounded,
              size: 18,
              color: Color(0xFFCCCCCC),
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildFeedbackPreview(FacilityReportModel report) {
    final feedback = report.catatanTeknisi?.trim();

    if (feedback == null || feedback.isEmpty) {
      return const SizedBox.shrink();
    }

    return Container(
      width: double.infinity,
      margin: const EdgeInsets.only(top: 12),
      padding: const EdgeInsets.all(11),
      decoration: BoxDecoration(
        color: const Color(0xFFF0FDF4),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: const Color(0xFFBBF7D0)),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Icon(
            Icons.feedback_outlined,
            size: 16,
            color: Color(0xFF16A34A),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: Text(
              feedback,
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(
                fontSize: 11.5,
                color: Color(0xFF166534),
                fontWeight: FontWeight.w600,
                height: 1.35,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInternalPrioritySection(FacilityReportModel report) {
    final priority = report.priorityLevel ?? 'low';
    final priorityStyle = _getPriorityStyle(priority);

    final uniqueReporter = report.uniqueReporterCount ?? 1;
    final reportCount = report.reportCount ?? 1;
    final similarity = report.similarityScore;

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(11),
      decoration: BoxDecoration(
        color: const Color(0xFFFAFAFA),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: const Color(0xFFF0F0F0)),
      ),
      child: Wrap(
        spacing: 8,
        runSpacing: 8,
        crossAxisAlignment: WrapCrossAlignment.center,
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 9, vertical: 5),
            decoration: BoxDecoration(
              color: priorityStyle.backgroundColor,
              borderRadius: BorderRadius.circular(20),
            ),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Icon(
                  priorityStyle.icon,
                  size: 12,
                  color: priorityStyle.color,
                ),
                const SizedBox(width: 4),
                Text(
                  'Priority: ${priority.toUpperCase()}',
                  style: TextStyle(
                    fontSize: 10,
                    fontWeight: FontWeight.w800,
                    color: priorityStyle.color,
                  ),
                ),
              ],
            ),
          ),
          _buildMiniInfo(
            icon: Icons.groups_rounded,
            text: '$uniqueReporter pelapor unik',
          ),
          _buildMiniInfo(
            icon: Icons.copy_rounded,
            text: '$reportCount laporan serupa',
          ),
          if (similarity != null)
            _buildMiniInfo(
              icon: Icons.compare_arrows_rounded,
              text: '$similarity% similar',
            ),
        ],
      ),
    );
  }

  Widget _buildMiniInfo({
    required IconData icon,
    required String text,
  }) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(
          icon,
          size: 12,
          color: _textMuted,
        ),
        const SizedBox(width: 4),
        Text(
          text,
          style: const TextStyle(
            fontSize: 10.5,
            color: _textMuted,
            fontWeight: FontWeight.w600,
          ),
        ),
      ],
    );
  }

  Widget _buildFooter(String pelapor, String tanggal) {
    return Row(
      children: [
        const Icon(
          Icons.person_outline_rounded,
          size: 14,
          color: _textMuted,
        ),
        const SizedBox(width: 6),
        Expanded(
          child: Text(
            pelapor,
            style: const TextStyle(
              fontSize: 11,
              color: _textMuted,
              fontWeight: FontWeight.w500,
            ),
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
          ),
        ),
        const Icon(
          Icons.calendar_today_outlined,
          size: 12,
          color: Color(0xFFCCCCCC),
        ),
        const SizedBox(width: 4),
        Text(
          tanggal,
          style: const TextStyle(
            fontSize: 11,
            color: Color(0xFFCCCCCC),
            fontWeight: FontWeight.w500,
          ),
        ),
      ],
    );
  }

  _StatusStyle _getStatusStyle(String statusLabel) {
    switch (statusLabel) {
      case 'Sedang Diperbaiki':
        return _StatusStyle(
          color: const Color(0xFF3B82F6),
          backgroundColor: const Color(0xFFEFF6FF),
          icon: Icons.autorenew_rounded,
        );
      case 'Sudah Diperbaiki':
        return _StatusStyle(
          color: const Color(0xFF10B981),
          backgroundColor: const Color(0xFFECFDF5),
          icon: Icons.check_circle_outline_rounded,
        );
      default:
        return _StatusStyle(
          color: const Color(0xFFF59E0B),
          backgroundColor: const Color(0xFFFFFBEB),
          icon: Icons.access_time_rounded,
        );
    }
  }

  _PriorityStyle _getPriorityStyle(String priority) {
    switch (priority) {
      case 'high':
        return _PriorityStyle(
          color: const Color(0xFFDC2626),
          backgroundColor: const Color(0xFFFEE2E2),
          icon: Icons.priority_high_rounded,
        );
      case 'medium':
        return _PriorityStyle(
          color: const Color(0xFFD97706),
          backgroundColor: const Color(0xFFFFF7ED),
          icon: Icons.warning_amber_rounded,
        );
      default:
        return _PriorityStyle(
          color: const Color(0xFF16A34A),
          backgroundColor: const Color(0xFFDCFCE7),
          icon: Icons.low_priority_rounded,
        );
    }
  }

  int _priorityRank(String? priority) {
    switch (priority) {
      case 'high':
        return 3;
      case 'medium':
        return 2;
      case 'low':
      default:
        return 1;
    }
  }
}

class _StatusStyle {
  final Color color;
  final Color backgroundColor;
  final IconData icon;

  _StatusStyle({
    required this.color,
    required this.backgroundColor,
    required this.icon,
  });
}

class _PriorityStyle {
  final Color color;
  final Color backgroundColor;
  final IconData icon;

  _PriorityStyle({
    required this.color,
    required this.backgroundColor,
    required this.icon,
  });
}