import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import '../../models/room_model.dart';
import '../../providers/facility_provider.dart';
import '../../widgets/top_notification.dart';

class AdminRoomFormScreen extends StatefulWidget {
  final RoomModel? room;

  const AdminRoomFormScreen({super.key, this.room});

  @override
  State<AdminRoomFormScreen> createState() => _AdminRoomFormScreenState();
}

class _AdminRoomFormScreenState extends State<AdminRoomFormScreen>
    with SingleTickerProviderStateMixin {
  final _formKey = GlobalKey<FormState>();
  final _namaController = TextEditingController();
  final _gedungController = TextEditingController();
  final _lantaiController = TextEditingController();

  bool _isLoading = false;

  late AnimationController _animController;
  late Animation<double> _fade;

  static const _red = Color(0xFFE4002B);
  static const _redMid = Color(0xFFB8001F);
  static const _redDark = Color(0xFF6B0012);
  static const _redDeep = Color(0xFF2A000A);
  static const _surface = Color(0xFFF8F8F8);
  static const _border = Color(0xFFF0F0F0);
  static const _labelColor = Color(0xFFBBBBBB);
  static const _textPrimary = Color(0xFF111111);
  static const _white = Colors.white;

  bool get _isEdit => widget.room != null;

  @override
  void initState() {
    super.initState();

    SystemChrome.setSystemUIOverlayStyle(
      const SystemUiOverlayStyle(
        statusBarColor: Colors.transparent,
        statusBarIconBrightness: Brightness.light,
      ),
    );

    if (_isEdit) {
      _namaController.text = widget.room!.namaRuangan;
      _gedungController.text = widget.room!.gedung;
      _lantaiController.text = widget.room!.lantai;
    }

    _animController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 400),
    );

    _fade = CurvedAnimation(
      parent: _animController,
      curve: Curves.easeOut,
    );

    _animController.forward();
  }

  @override
  void dispose() {
    _namaController.dispose();
    _gedungController.dispose();
    _lantaiController.dispose();
    _animController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isLoading = true);

    final roomData = RoomModel(
      id: widget.room?.id ?? 0,
      namaRuangan: _namaController.text.trim(),
      gedung: _gedungController.text.trim(),
      lantai: _lantaiController.text.trim(),
    );

    try {
      final provider = Provider.of<FacilityProvider>(context, listen: false);

      final success = _isEdit
          ? await provider.updateRoom(roomData)
          : await provider.createRoom(roomData);

      if (!mounted) return;

      if (success) {
        TopNotification.show(
          context,
          message: 'Ruangan berhasil ditambahkan',
          type: TopNotificationType.success,
        );

        Navigator.pop(context, true);
      } else {
       TopNotification.show(
        context,
        message: 'Gagal menyimpan data',
        type: TopNotificationType.error,
      );
      }
    } catch (e) {
      if (!mounted) return;

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error: $e'),
          backgroundColor: _red,
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(10),
          ),
          margin: const EdgeInsets.all(16),
        ),
      );
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  Widget _buildHero() {
    return Container(
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
              padding: const EdgeInsets.fromLTRB(16, 10, 16, 22),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
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
                        size: 16,
                        color: _white,
                      ),
                    ),
                  ),
                  const SizedBox(height: 14),
                  Container(
                    width: 44,
                    height: 44,
                    decoration: BoxDecoration(
                      color: _white.withValues(alpha: 0.15),
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(
                        color: _white.withValues(alpha: 0.2),
                      ),
                    ),
                    alignment: Alignment.center,
                    child: Icon(
                      _isEdit
                          ? Icons.edit_rounded
                          : Icons.add_home_work_rounded,
                      size: 22,
                      color: _white,
                    ),
                  ),
                  const SizedBox(height: 12),
                  Text(
                    _isEdit ? 'Edit Ruangan' : 'Tambah Ruangan',
                    style: const TextStyle(
                      fontSize: 22,
                      fontWeight: FontWeight.w800,
                      color: _white,
                      letterSpacing: -0.6,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    _isEdit
                        ? 'Perbarui data ruangan kampus'
                        : 'Daftarkan ruangan baru ke sistem',
                    style: TextStyle(
                      fontSize: 11,
                      color: _white.withValues(alpha: 0.55),
                      letterSpacing: 0.2,
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildField({
    required String label,
    required TextEditingController controller,
    required String hint,
    required IconData icon,
    TextInputType keyboardType = TextInputType.text,
    String? Function(String?)? validator,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label.toUpperCase(),
          style: const TextStyle(
            fontSize: 10.5,
            fontWeight: FontWeight.w700,
            color: _labelColor,
            letterSpacing: 0.9,
          ),
        ),
        const SizedBox(height: 7),
        TextFormField(
          controller: controller,
          keyboardType: keyboardType,
          validator: validator,
          style: const TextStyle(
            fontSize: 14,
            color: _textPrimary,
            fontWeight: FontWeight.w500,
          ),
          decoration: InputDecoration(
            hintText: hint,
            hintStyle: const TextStyle(
              fontSize: 14,
              color: Color(0xFFCCCCCC),
            ),
            prefixIcon: Icon(
              icon,
              color: const Color(0xFFCCCCCC),
              size: 18,
            ),
            filled: true,
            fillColor: _surface,
            contentPadding: const EdgeInsets.symmetric(
              horizontal: 16,
              vertical: 14,
            ),
            enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(
                color: _border,
                width: 1.5,
              ),
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(
                color: _red,
                width: 1.5,
              ),
            ),
            errorBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(
                color: _red,
                width: 1.5,
              ),
            ),
            focusedErrorBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(
                color: _red,
                width: 1.5,
              ),
            ),
          ),
        ),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: _white,
      body: FadeTransition(
        opacity: _fade,
        child: Column(
          children: [
            _buildHero(),
            Expanded(
              child: Form(
                key: _formKey,
                child: SingleChildScrollView(
                  padding: const EdgeInsets.fromLTRB(22, 24, 22, 32),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      _buildField(
                        label: 'Nama Ruangan',
                        controller: _namaController,
                        hint: 'Contoh: RKC-01, Lab Komputer A',
                        icon: Icons.meeting_room_outlined,
                        validator: (value) {
                          if (value == null || value.trim().isEmpty) {
                            return 'Wajib diisi';
                          }
                          return null;
                        },
                      ),
                      const SizedBox(height: 16),
                      _buildField(
                        label: 'Gedung',
                        controller: _gedungController,
                        hint: 'Contoh: Gedung A, Gedung FTE',
                        icon: Icons.apartment_rounded,
                        validator: (value) {
                          if (value == null || value.trim().isEmpty) {
                            return 'Wajib diisi';
                          }
                          return null;
                        },
                      ),
                      const SizedBox(height: 16),
                      _buildField(
                        label: 'Lantai',
                        controller: _lantaiController,
                        hint: 'Contoh: 1, 2, 3',
                        icon: Icons.layers_outlined,
                        keyboardType: TextInputType.number,
                        validator: (value) {
                          if (value == null || value.trim().isEmpty) {
                            return 'Wajib diisi';
                          }
                          return null;
                        },
                      ),
                      const SizedBox(height: 32),
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
                            onPressed: _isLoading ? null : _submit,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.transparent,
                              shadowColor: Colors.transparent,
                              disabledBackgroundColor: Colors.transparent,
                              elevation: 0,
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(13),
                              ),
                            ),
                            child: _isLoading
                                ? const SizedBox(
                                    height: 20,
                                    width: 20,
                                    child: CircularProgressIndicator(
                                      color: _white,
                                      strokeWidth: 2,
                                    ),
                                  )
                                : Row(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      Icon(
                                        _isEdit
                                            ? Icons.save_rounded
                                            : Icons.add_circle_outline_rounded,
                                        color: _white,
                                        size: 16,
                                      ),
                                      const SizedBox(width: 6),
                                      Text(
                                        _isEdit
                                            ? 'Simpan Perubahan'
                                            : 'Tambah Ruangan',
                                        style: const TextStyle(
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
            ),
          ],
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
    final paint = Paint()..color = Colors.white.withValues(alpha: 0.18);

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