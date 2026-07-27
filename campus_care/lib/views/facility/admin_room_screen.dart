import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import '../../providers/facility_provider.dart';
import 'admin_room_form_screen.dart';

class AdminRoomScreen extends StatefulWidget {
  const AdminRoomScreen({super.key});

  @override
  State<AdminRoomScreen> createState() => _AdminRoomScreenState();
}

class _AdminRoomScreenState extends State<AdminRoomScreen> {
  static const _red         = Color(0xFFE4002B);
  static const _redMid      = Color(0xFFB8001F);
  static const _redDark     = Color(0xFF6B0012);
  static const _redDeep     = Color(0xFF2A000A);
  static const _bg          = Color(0xFFF7F7F5);
  static const _white       = Colors.white;
  static const _border      = Color(0xFFEFEFEF);
  static const _textPrimary = Color(0xFF111111);
  static const _textMuted   = Color(0xFFAAAAAA);

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<FacilityProvider>(context, listen: false).fetchRooms();
    });
  }

  void _showDeleteDialog(BuildContext ctx, int id, String namaRuangan) {
    showDialog(
      context: ctx,
      builder: (dialogCtx) => AlertDialog(
        backgroundColor: _white,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: const Text('Hapus Ruangan?',
            style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: _textPrimary)),
        content: RichText(
          text: TextSpan(
            style: const TextStyle(fontSize: 13, color: _textMuted, height: 1.5),
            children: [
              const TextSpan(text: 'Ruangan '),
              TextSpan(text: '"$namaRuangan"',
                  style: const TextStyle(fontWeight: FontWeight.w700, color: _textPrimary)),
              const TextSpan(text: ' akan dihapus secara permanen.'),
            ],
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(dialogCtx),
            child: const Text('Batal',
                style: TextStyle(color: _textMuted, fontWeight: FontWeight.w600)),
          ),
          ElevatedButton(
            onPressed: () async {
              Navigator.pop(dialogCtx);
              try {
                final success = await Provider.of<FacilityProvider>(
                    ctx, listen: false).deleteRoom(id);
                if (success && ctx.mounted) {
                  _showSnackBar(ctx, 'Ruangan berhasil dihapus');
                }
              } catch (e) {
                if (ctx.mounted) {
                  _showSnackBar(ctx, 'Gagal menghapus: $e', isError: true);
                }
              }
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: _red, foregroundColor: _white, elevation: 0,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
            ),
            child: const Text('Hapus', style: TextStyle(fontWeight: FontWeight.w700)),
          ),
        ],
      ),
    );
  }

  void _showSnackBar(BuildContext ctx, String message, {bool isError = false}) {
    ScaffoldMessenger.of(ctx).showSnackBar(SnackBar(
      content: Text(message,
          style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600)),
      backgroundColor: isError ? _red : const Color(0xFF10B981),
      behavior: SnackBarBehavior.floating,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
      margin: const EdgeInsets.all(16),
    ));
  }

  @override
  Widget build(BuildContext context) {
    SystemChrome.setSystemUIOverlayStyle(const SystemUiOverlayStyle(
      statusBarColor: Colors.transparent,
      statusBarIconBrightness: Brightness.light,
    ));

    return Scaffold(
      backgroundColor: _bg,
      body: Column(
        children: [
          // ── Hero header (konsisten dengan tema) ──
          Container(
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                stops: [0.0, 0.4, 0.75, 1.0],
                colors: [_red, _redMid, _redDark, _redDeep],
              ),
            ),
            child: Stack(
              children: [
                Positioned.fill(child: CustomPaint(painter: _GridPainter())),
                Positioned(top: -40, right: -30,
                    child: Container(width: 120, height: 120,
                        decoration: BoxDecoration(shape: BoxShape.circle,
                            color: _white.withValues(alpha: 0.06)))),
                Positioned(bottom: 0, right: 0,
                    child: SizedBox(width: 70, height: 70,
                        child: CustomPaint(painter: _DotPainter()))),
                SafeArea(
                  bottom: false,
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(16, 10, 16, 20),
                    child: Row(
                      children: [
                        // Back button
                        GestureDetector(
                          onTap: () => Navigator.pop(context),
                          child: Container(
                            width: 36, height: 36,
                            decoration: BoxDecoration(
                              color: _white.withValues(alpha: 0.13),
                              borderRadius: BorderRadius.circular(10),
                              border: Border.all(color: _white.withValues(alpha: 0.18)),
                            ),
                            child: const Icon(Icons.arrow_back_ios_new_rounded,
                                size: 16, color: _white),
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text('Kelola Ruangan',
                                  style: TextStyle(fontSize: 18,
                                      fontWeight: FontWeight.w800, color: _white,
                                      letterSpacing: -0.4)),
                              Text('Daftar ruangan kampus',
                                  style: TextStyle(fontSize: 11,
                                      color: _white.withValues(alpha: 0.55))),
                            ],
                          ),
                        ),
                        // Tombol tambah di header
                        GestureDetector(
                          onTap: () => Navigator.push(context,
                              MaterialPageRoute(
                                  builder: (_) => const AdminRoomFormScreen())),
                          child: Container(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 14, vertical: 8),
                            decoration: BoxDecoration(
                              color: _white.withValues(alpha: 0.18),
                              borderRadius: BorderRadius.circular(10),
                              border: Border.all(color: _white.withValues(alpha: 0.25)),
                            ),
                            child: const Row(mainAxisSize: MainAxisSize.min, children: [
                              Icon(Icons.add_rounded, color: _white, size: 16),
                              SizedBox(width: 4),
                              Text('Tambah', style: TextStyle(
                                  color: _white, fontSize: 12,
                                  fontWeight: FontWeight.w700)),
                            ]),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),
          ),

          // ── List ruangan ──
          Expanded(
            child: Consumer<FacilityProvider>(
              builder: (context, provider, _) {
                if (provider.isLoading) {
                  return const Center(
                      child: CircularProgressIndicator(color: _red, strokeWidth: 2));
                }

                if (provider.rooms.isEmpty) {
                  return Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Container(
                          padding: const EdgeInsets.all(20),
                          decoration: const BoxDecoration(
                              color: Color(0xFFF5F5F5), shape: BoxShape.circle),
                          child: const Icon(Icons.meeting_room_outlined,
                              size: 36, color: _textMuted),
                        ),
                        const SizedBox(height: 14),
                        const Text('Belum ada ruangan',
                            style: TextStyle(fontSize: 15,
                                fontWeight: FontWeight.w600, color: _textPrimary)),
                        const SizedBox(height: 6),
                        const Text('Tap tombol Tambah untuk mendaftarkan ruangan',
                            style: TextStyle(fontSize: 12, color: _textMuted)),
                      ],
                    ),
                  );
                }

                return ListView.builder(
                  padding: const EdgeInsets.fromLTRB(16, 16, 16, 32),
                  itemCount: provider.rooms.length,
                  itemBuilder: (context, index) {
                    final room = provider.rooms[index];
                    return Container(
                      margin: const EdgeInsets.only(bottom: 10),
                      decoration: BoxDecoration(
                        color: _white,
                        borderRadius: BorderRadius.circular(14),
                        border: Border.all(color: _border),
                      ),
                      child: Padding(
                        padding: const EdgeInsets.all(14),
                        child: Row(
                          children: [
                            // Icon ruangan
                            Container(
                              width: 44, height: 44,
                              decoration: BoxDecoration(
                                color: const Color(0xFFEFF6FF),
                                borderRadius: BorderRadius.circular(12),
                              ),
                              alignment: Alignment.center,
                              child: const Icon(Icons.meeting_room_outlined,
                                  color: Color(0xFF3B82F6), size: 22),
                            ),
                            const SizedBox(width: 12),
                            // Info
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(room.namaRuangan,
                                      style: const TextStyle(fontSize: 14,
                                          fontWeight: FontWeight.w700,
                                          color: _textPrimary)),
                                  const SizedBox(height: 3),
                                  Row(children: [
                                    const Icon(Icons.location_on_outlined,
                                        size: 12, color: _textMuted),
                                    const SizedBox(width: 3),
                                    Text('${room.gedung} · Lantai ${room.lantai}',
                                        style: const TextStyle(
                                            fontSize: 11, color: _textMuted)),
                                  ]),
                                ],
                              ),
                            ),
                            // Action buttons
                            Row(mainAxisSize: MainAxisSize.min, children: [
                              GestureDetector(
                                onTap: () => Navigator.push(context,
                                    MaterialPageRoute(
                                        builder: (_) =>
                                            AdminRoomFormScreen(room: room))),
                                child: Container(
                                  width: 34, height: 34,
                                  decoration: BoxDecoration(
                                    color: const Color(0xFFEFF6FF),
                                    borderRadius: BorderRadius.circular(9),
                                  ),
                                  alignment: Alignment.center,
                                  child: const Icon(Icons.edit_outlined,
                                      color: Color(0xFF3B82F6), size: 16),
                                ),
                              ),
                              const SizedBox(width: 8),
                              GestureDetector(
                                onTap: () => _showDeleteDialog(
                                    context, room.id, room.namaRuangan),
                                child: Container(
                                  width: 34, height: 34,
                                  decoration: BoxDecoration(
                                    color: const Color(0xFFFFF0F2),
                                    borderRadius: BorderRadius.circular(9),
                                  ),
                                  alignment: Alignment.center,
                                  child: const Icon(Icons.delete_outline_rounded,
                                      color: _red, size: 16),
                                ),
                              ),
                            ]),
                          ],
                        ),
                      ),
                    );
                  },
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}

class _GridPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = Colors.white.withValues(alpha: 0.04)..strokeWidth = 1;
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

class _DotPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()..color = Colors.white.withValues(alpha: 0.18);
    const spacing = 12.0;
    for (double x = spacing; x < size.width; x += spacing) {
      for (double y = spacing; y < size.height; y += spacing) {
        canvas.drawCircle(Offset(x, y), 1.5, paint);
      }
    }
  }
  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}