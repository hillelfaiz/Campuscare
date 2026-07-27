import 'dart:async';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:image_picker/image_picker.dart';
import 'package:provider/provider.dart';
import '../../providers/facility_provider.dart';
import '../../providers/auth_provider.dart';
import '../../models/room_model.dart';
import '../../widgets/top_notification.dart';

class ReportFormScreen extends StatefulWidget {
  const ReportFormScreen({super.key});

  @override
  State<ReportFormScreen> createState() => _ReportFormScreenState();
}

class _ReportFormScreenState extends State<ReportFormScreen>
    with SingleTickerProviderStateMixin {
  final _ruanganController  = TextEditingController();
  final _fasilitasController = TextEditingController();
  final _deskripsiController = TextEditingController();
  final _layerLink           = LayerLink();
  final _ruanganFocusNode    = FocusNode();

  OverlayEntry? _overlayEntry;
  RoomModel?    _selectedRoom;
  List<RoomModel> _roomSuggestions = [];
  bool  _isSearchingRoom = false;
  Timer? _debounce;
  File?  _fotoKerusakan;
  final  _picker = ImagePicker();
  bool  _isSubmitting = false;

  late AnimationController _animController;
  late Animation<double>   _fade;
  late Animation<Offset>   _slide;

  static const _red         = Color(0xFFE4002B);
  static const _redMid      = Color(0xFFB8001F);
  static const _redDark     = Color(0xFF6B0012);
  static const _redDeep     = Color(0xFF2A000A);
  static const _surface     = Color(0xFFF8F8F8);
  static const _border      = Color(0xFFF0F0F0);
  static const _labelColor  = Color(0xFFBBBBBB);
  static const _textPrimary = Color(0xFF111111);
  static const _textMuted   = Color(0xFFAAAAAA);
  static const _white       = Colors.white;

  @override
  void initState() {
    super.initState();
    SystemChrome.setSystemUIOverlayStyle(const SystemUiOverlayStyle(
      statusBarColor: Colors.transparent,
      statusBarIconBrightness: Brightness.light,
    ));
    _animController = AnimationController(
        vsync: this, duration: const Duration(milliseconds: 500));
    _fade = CurvedAnimation(parent: _animController, curve: Curves.easeOut);
    _slide = Tween<Offset>(
        begin: const Offset(0, 0.06), end: Offset.zero)
        .animate(CurvedAnimation(parent: _animController, curve: Curves.easeOutCubic));
    _animController.forward();

    _ruanganFocusNode.addListener(() {
      if (_ruanganFocusNode.hasFocus) {
        _showOverlay();
      } else {
        Future.delayed(const Duration(milliseconds: 150), _hideOverlay);
      }
    });
  }

  @override
  void dispose() {
    _ruanganController.dispose();
    _fasilitasController.dispose();
    _deskripsiController.dispose();
    _ruanganFocusNode.dispose();
    _debounce?.cancel();
    _hideOverlay();
    _animController.dispose();
    super.dispose();
  }

  // ─── SEARCH LOGIC (tidak diubah, sudah benar) ─────────────────────────────
  void _onSearchChanged(String query) {
    if (_debounce?.isActive ?? false) _debounce!.cancel();
    setState(() => _selectedRoom = null);

    if (query.length < 2) {
      setState(() { _roomSuggestions = []; _isSearchingRoom = false; });
      _rebuildOverlay();
      return;
    }

    setState(() => _isSearchingRoom = true);
    _rebuildOverlay();

    _debounce = Timer(const Duration(milliseconds: 300), () async {
      try {
        final results = await Provider.of<FacilityProvider>(
            context, listen: false).searchRooms(query);
        if (mounted) {
          setState(() { _roomSuggestions = results; _isSearchingRoom = false; });
          _rebuildOverlay();
        }
      } catch (_) {
        if (mounted) {
          setState(() => _isSearchingRoom = false);
          _rebuildOverlay();
        }
      }
    });
  }

  void _showOverlay() {
    if (_overlayEntry != null) return;
    final renderBox = context.findRenderObject() as RenderBox;
    final width = renderBox.size.width - 44; // padding 22 * 2

    _overlayEntry = OverlayEntry(
      builder: (_) => Positioned(
        width: width,
        child: CompositedTransformFollower(
          link: _layerLink,
          showWhenUnlinked: false,
          offset: const Offset(0, 54),
          child: Material(
            elevation: 6,
            borderRadius: BorderRadius.circular(14),
            shadowColor: Colors.black.withValues(alpha: 0.1),
            color: _white,
            child: _buildSuggestionsList(),
          ),
        ),
      ),
    );
    Overlay.of(context).insert(_overlayEntry!);
  }

  void _hideOverlay() {
    _overlayEntry?.remove();
    _overlayEntry = null;
  }

  void _rebuildOverlay() => _overlayEntry?.markNeedsBuild();

  Widget _buildSuggestionsList() {
    if (_isSearchingRoom) {
      return const Padding(
        padding: EdgeInsets.all(16),
        child: Center(child: SizedBox(width: 20, height: 20,
            child: CircularProgressIndicator(strokeWidth: 2, color: _red))),
      );
    }
    if (_roomSuggestions.isEmpty && _ruanganController.text.length >= 2) {
      return Padding(
        padding: const EdgeInsets.all(16),
        child: Row(children: [
          const Icon(Icons.search_off_rounded, size: 16, color: _textMuted),
          const SizedBox(width: 8),
          const Text('Ruangan tidak ditemukan',
              style: TextStyle(fontSize: 13, color: _textMuted)),
        ]),
      );
    }
    if (_roomSuggestions.isEmpty) return const SizedBox.shrink();

    return ConstrainedBox(
      constraints: const BoxConstraints(maxHeight: 220),
      child: ListView.separated(
        padding: const EdgeInsets.symmetric(vertical: 6),
        shrinkWrap: true,
        itemCount: _roomSuggestions.length,
        separatorBuilder: (_, _) =>
            const Divider(height: 1, color: Color(0xFFF5F5F5), indent: 16),
        itemBuilder: (_, index) {
          final room = _roomSuggestions[index];
          return InkWell(
            onTap: () {
              setState(() {
                _selectedRoom = room;
                _ruanganController.text = room.displayLabel;
              });
              _ruanganFocusNode.unfocus();
            },
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
              child: Row(children: [
                Container(width: 32, height: 32,
                    decoration: BoxDecoration(color: const Color(0xFFEFF6FF),
                        borderRadius: BorderRadius.circular(8)),
                    alignment: Alignment.center,
                    child: const Icon(Icons.meeting_room_outlined,
                        color: Color(0xFF3B82F6), size: 16)),
                const SizedBox(width: 10),
                Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(room.namaRuangan, style: const TextStyle(
                          fontSize: 13, fontWeight: FontWeight.w700,
                          color: _textPrimary)),
                      Text('${room.gedung} · Lantai ${room.lantai}',
                          style: const TextStyle(fontSize: 11, color: _textMuted)),
                    ])),
              ]),
            ),
          );
        },
      ),
    );
  }

  // ─── FOTO ─────────────────────────────────────────────────────────────────
  Future<void> _pickImage(ImageSource source) async {
    final XFile? image = await _picker.pickImage(source: source);
    if (image != null) setState(() => _fotoKerusakan = File(image.path));
  }

  void _showImageSourceSheet() {
    showModalBottomSheet(
      context: context,
      backgroundColor: _white,
      shape: const RoundedRectangleBorder(
          borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (_) => Padding(
        padding: const EdgeInsets.fromLTRB(22, 12, 22, 32),
        child: Column(mainAxisSize: MainAxisSize.min, children: [
          Container(width: 36, height: 4,
              decoration: BoxDecoration(color: const Color(0xFFEEEEEE),
                  borderRadius: BorderRadius.circular(2))),
          const SizedBox(height: 20),
          const Text('Pilih Sumber Foto',
              style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700,
                  color: _textPrimary)),
          const SizedBox(height: 20),
          Row(children: [
            Expanded(child: GestureDetector(
              onTap: () { Navigator.pop(context); _pickImage(ImageSource.camera); },
              child: Container(
                padding: const EdgeInsets.symmetric(vertical: 16),
                decoration: BoxDecoration(color: const Color(0xFFFFF0F2),
                    borderRadius: BorderRadius.circular(14)),
                child: const Column(children: [
                  Icon(Icons.camera_alt_rounded, color: _red, size: 28),
                  SizedBox(height: 6),
                  Text('Kamera', style: TextStyle(fontSize: 12,
                      fontWeight: FontWeight.w700, color: _red)),
                ]),
              ),
            )),
            const SizedBox(width: 12),
            Expanded(child: GestureDetector(
              onTap: () { Navigator.pop(context); _pickImage(ImageSource.gallery); },
              child: Container(
                padding: const EdgeInsets.symmetric(vertical: 16),
                decoration: BoxDecoration(color: const Color(0xFFEFF6FF),
                    borderRadius: BorderRadius.circular(14)),
                child: const Column(children: [
                  Icon(Icons.photo_library_rounded,
                      color: Color(0xFF3B82F6), size: 28),
                  SizedBox(height: 6),
                  Text('Galeri', style: TextStyle(fontSize: 12,
                      fontWeight: FontWeight.w700, color: Color(0xFF3B82F6))),
                ]),
              ),
            )),
          ]),
        ]),
      ),
    );
  }

  // ─── SUBMIT ───────────────────────────────────────────────────────────────
  Future<void> _kirimLaporan() async {
    if (_selectedRoom == null) {
      _showSnackBar('Pilih ruangan dari rekomendasi', isError: true);
      return;
    }
    if (_fasilitasController.text.trim().isEmpty) {
      _showSnackBar('Nama fasilitas tidak boleh kosong', isError: true);
      return;
    }
    if (_deskripsiController.text.trim().isEmpty) {
      _showSnackBar('Deskripsi tidak boleh kosong', isError: true);
      return;
    }

    setState(() => _isSubmitting = true);
    try {
      final reporterId =
          Provider.of<AuthProvider>(context, listen: false).currentUser?.id ?? 0;
      await Provider.of<FacilityProvider>(context, listen: false).submitReport(
        roomId: _selectedRoom!.id,
        namaFasilitas: _fasilitasController.text.trim(),
        deskripsi: _deskripsiController.text.trim(),
        reporterId: reporterId,
        fotoPath: _fotoKerusakan?.path,
      );
      if (mounted) {
        _showSnackBar('Laporan berhasil dikirim!');
        Navigator.pop(context);
      }
    } catch (e) {
      if (mounted) {
        _showSnackBar(
          e.toString().replaceAll('Exception: ', ''),
          isError: true,
        );
      }
    } finally {
      if (mounted) setState(() => _isSubmitting = false);
    }
  }

    void _showSnackBar(String message, {bool isError = false}) {
      TopNotification.show(
        context,
        message: message.replaceAll('Exception: ', ''),
        type: isError
            ? TopNotificationType.error
            : TopNotificationType.success,
      );
    }

  // ─── INPUT FIELD BUILDER ──────────────────────────────────────────────────
  Widget _buildLabel(String text, {bool isOptional = false}) {
    return Row(children: [
      Text(text.toUpperCase(),
          style: const TextStyle(fontSize: 10.5, fontWeight: FontWeight.w700,
              color: _labelColor, letterSpacing: 0.9)),
      if (isOptional) ...[
        const SizedBox(width: 6),
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 2),
          decoration: BoxDecoration(color: const Color(0xFFFFF0F2),
              borderRadius: BorderRadius.circular(6)),
          child: const Text('Opsional',
              style: TextStyle(fontSize: 9.5, fontWeight: FontWeight.w700, color: _red)),
        ),
      ],
    ]);
  }

  InputDecoration _inputDecor(String hint, {IconData? icon}) {
    return InputDecoration(
      hintText: hint,
      hintStyle: const TextStyle(fontSize: 14, color: Color(0xFFCCCCCC)),
      prefixIcon: icon != null
          ? Icon(icon, color: const Color(0xFFCCCCCC), size: 18)
          : null,
      filled: true, fillColor: _surface,
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: _border, width: 1.5)),
      focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: _red, width: 1.5)),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: _white,
      body: FadeTransition(
        opacity: _fade,
        child: SingleChildScrollView(
          child: Column(
            children: [
              // ── Hero header ──
              SizedBox(
                width: double.infinity,
                child: Container(
                  width: double.infinity,
                  constraints: const BoxConstraints(
                    minHeight: 220,
                  ),
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
                      Positioned.fill(
                        child: CustomPaint(
                          painter: _GridPainter(),
                        ),
                      ),
                      Positioned(
                        top: -40,
                        right: -30,
                        child: Container(
                          width: 120,
                          height: 120,
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            color: _white.withValues(alpha: 0.06),
                          ),
                        ),
                      ),
                      Positioned(
                        bottom: 0,
                        right: 0,
                        child: SizedBox(
                          width: 70,
                          height: 70,
                          child: CustomPaint(
                            painter: _DotPainter(),
                          ),
                        ),
                      ),
                      SafeArea(
                        bottom: false,
                        child: Padding(
                          padding: const EdgeInsets.fromLTRB(22, 14, 22, 24),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              GestureDetector(
                                onTap: () => Navigator.pop(context),
                                child: Container(
                                  width: 40,
                                  height: 40,
                                  decoration: BoxDecoration(
                                    color: _white.withValues(alpha: 0.13),
                                    borderRadius: BorderRadius.circular(12),
                                    border: Border.all(
                                      color: _white.withValues(alpha: 0.18),
                                    ),
                                  ),
                                  child: const Icon(
                                    Icons.arrow_back_ios_new_rounded,
                                    size: 16,
                                    color: _white,
                                  ),
                                ),
                              ),
                              const SizedBox(height: 16),
                              Container(
                                width: 48,
                                height: 48,
                                decoration: BoxDecoration(
                                  color: _white.withValues(alpha: 0.15),
                                  borderRadius: BorderRadius.circular(14),
                                  border: Border.all(
                                    color: _white.withValues(alpha: 0.2),
                                  ),
                                ),
                                alignment: Alignment.center,
                                child: const Icon(
                                  Icons.report_problem_outlined,
                                  size: 24,
                                  color: _white,
                                ),
                              ),
                              const SizedBox(height: 16),
                              const Text(
                                'Lapor Fasilitas',
                                style: TextStyle(
                                  fontSize: 24,
                                  fontWeight: FontWeight.w800,
                                  color: _white,
                                  letterSpacing: -0.6,
                                ),
                              ),
                              const SizedBox(height: 6),
                              Text(
                                'Isi form berikut untuk melaporkan kerusakan',
                                style: TextStyle(
                                  fontSize: 12,
                                  color: _white.withValues(alpha: 0.65),
                                  fontWeight: FontWeight.w500,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),

              // ── Form body ──
              SlideTransition(
                position: _slide,
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(22, 22, 22, 32),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // ── Ruangan dengan search ──
                      _buildLabel('Area'),
                      const SizedBox(height: 7),
                      CompositedTransformTarget(
                        link: _layerLink,
                        child: TextField(
                          controller: _ruanganController,
                          focusNode: _ruanganFocusNode,
                          onChanged: _onSearchChanged,
                          style: const TextStyle(
                            fontSize: 14,
                            color: _textPrimary,
                            fontWeight: FontWeight.w500,
                          ),
                          decoration: _inputDecor(
                            'Cari nama ruangan atau gedung...',
                            icon: Icons.search_rounded,
                          ),
                        ),
                      ),

                      if (_selectedRoom != null) ...[
                        const SizedBox(height: 8),
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 10,
                            vertical: 6,
                          ),
                          decoration: BoxDecoration(
                            color: const Color(0xFFECFDF5),
                            borderRadius: BorderRadius.circular(8),
                            border: Border.all(
                              color: const Color(0xFF10B981)
                                  .withValues(alpha: 0.3),
                            ),
                          ),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              const Icon(
                                Icons.check_circle_rounded,
                                color: Color(0xFF10B981),
                                size: 14,
                              ),
                              const SizedBox(width: 6),
                              Text(
                                _selectedRoom!.displayLabel,
                                style: const TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w600,
                                  color: Color(0xFF10B981),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],

                      const SizedBox(height: 18),

                      // ── Nama fasilitas ──
                      _buildLabel('Nama Fasilitas'),
                      const SizedBox(height: 7),
                      TextField(
                        controller: _fasilitasController,
                        style: const TextStyle(
                          fontSize: 14,
                          color: _textPrimary,
                          fontWeight: FontWeight.w500,
                        ),
                        decoration: _inputDecor(
                          'Contoh: Proyektor, AC, Kursi',
                          icon: Icons.handyman_outlined,
                        ),
                      ),

                      const SizedBox(height: 18),

                      // ── Deskripsi ──
                      _buildLabel('Deskripsi Kerusakan'),
                      const SizedBox(height: 7),
                      TextField(
                        controller: _deskripsiController,
                        maxLines: 4,
                        style: const TextStyle(
                          fontSize: 14,
                          color: _textPrimary,
                          fontWeight: FontWeight.w500,
                        ),
                        decoration: _inputDecor(
                          'Jelaskan detail kerusakan yang terjadi...',
                        ),
                      ),

                      const SizedBox(height: 18),

                      // ── Foto ──
                      _buildLabel('Foto Kerusakan', isOptional: true),
                      const SizedBox(height: 7),
                      GestureDetector(
                        onTap: _showImageSourceSheet,
                        child: Container(
                          height: 140,
                          width: double.infinity,
                          decoration: BoxDecoration(
                            color: _surface,
                            borderRadius: BorderRadius.circular(14),
                            border: Border.all(color: _border, width: 1.5),
                          ),
                          child: _fotoKerusakan != null
                              ? Stack(
                                  children: [
                                    ClipRRect(
                                      borderRadius: BorderRadius.circular(13),
                                      child: Image.file(
                                        _fotoKerusakan!,
                                        width: double.infinity,
                                        height: double.infinity,
                                        fit: BoxFit.cover,
                                      ),
                                    ),
                                    Positioned(
                                      top: 8,
                                      right: 8,
                                      child: GestureDetector(
                                        onTap: _showImageSourceSheet,
                                        child: Container(
                                          padding: const EdgeInsets.all(6),
                                          decoration: BoxDecoration(
                                            color: Colors.black
                                                .withValues(alpha: 0.5),
                                            borderRadius:
                                                BorderRadius.circular(8),
                                          ),
                                          child: const Icon(
                                            Icons.edit_rounded,
                                            color: _white,
                                            size: 14,
                                          ),
                                        ),
                                      ),
                                    ),
                                  ],
                                )
                              : Column(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Container(
                                      padding: const EdgeInsets.all(12),
                                      decoration: BoxDecoration(
                                        color: const Color(0xFFFFF0F2),
                                        borderRadius: BorderRadius.circular(12),
                                      ),
                                      child: const Icon(
                                        Icons.add_a_photo_outlined,
                                        size: 24,
                                        color: _red,
                                      ),
                                    ),
                                    const SizedBox(height: 8),
                                    const Text(
                                      'Tap untuk menambah foto',
                                      style: TextStyle(
                                        fontSize: 12,
                                        color: _textMuted,
                                        fontWeight: FontWeight.w500,
                                      ),
                                    ),
                                    const SizedBox(height: 2),
                                    const Text(
                                      'Kamera atau galeri',
                                      style: TextStyle(
                                        fontSize: 10.5,
                                        color: Color(0xFFCCCCCC),
                                      ),
                                    ),
                                  ],
                                ),
                        ),
                      ),

                      const SizedBox(height: 32),

                      // ── Tombol kirim ──
                      SizedBox(
                        width: double.infinity,
                        height: 50,
                        child: DecoratedBox(
                          decoration: BoxDecoration(
                            gradient: const LinearGradient(
                              colors: [_red, _redMid],
                              begin: Alignment.centerLeft,
                              end: Alignment.centerRight,
                            ),
                            borderRadius: BorderRadius.circular(13),
                          ),
                          child: ElevatedButton(
                            onPressed: _isSubmitting ? null : _kirimLaporan,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.transparent,
                              shadowColor: Colors.transparent,
                              disabledBackgroundColor: Colors.transparent,
                              elevation: 0,
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(13),
                              ),
                            ),
                            child: _isSubmitting
                                ? const SizedBox(
                                    height: 20,
                                    width: 20,
                                    child: CircularProgressIndicator(
                                      color: _white,
                                      strokeWidth: 2,
                                    ),
                                  )
                                : const Row(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      Icon(
                                        Icons.send_rounded,
                                        size: 16,
                                        color: _white,
                                      ),
                                      SizedBox(width: 8),
                                      Text(
                                        'Kirim Laporan',
                                        style: TextStyle(
                                          fontSize: 14,
                                          fontWeight: FontWeight.w700,
                                          color: _white,
                                             ),
                                      ),
                                    ],
                                  ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _GridPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = Colors.white.withValues(alpha: 0.04)
      ..strokeWidth = 1;

    const step = 32.0;

    for (double x = 0; x < size.width; x += step) {
      canvas.drawLine(
        Offset(x, 0),
        Offset(x, size.height),
        paint,
      );
    }

    for (double y = 0; y < size.height; y += step) {
      canvas.drawLine(
        Offset(0, y),
        Offset(size.width, y),
        paint,
      );
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}

class _DotPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = Colors.white.withValues(alpha: 0.18);

    const spacing = 12.0;

    for (double x = spacing; x < size.width; x += spacing) {
      for (double y = spacing; y < size.height; y += spacing) {
        canvas.drawCircle(
          Offset(x, y),
          1.5,
          paint,
        );
      }
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}