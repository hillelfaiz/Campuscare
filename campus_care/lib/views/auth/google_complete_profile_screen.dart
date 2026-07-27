import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';

class GoogleCompleteProfileScreen extends StatefulWidget {
  const GoogleCompleteProfileScreen({super.key});

  @override
  State<GoogleCompleteProfileScreen> createState() =>
      _GoogleCompleteProfileScreenState();
}

class _GoogleCompleteProfileScreenState
    extends State<GoogleCompleteProfileScreen>
    with SingleTickerProviderStateMixin {
  final _namaController = TextEditingController();
  final _nimController = TextEditingController();
  final _kelasController = TextEditingController();
  bool _isLoading = false;

  late AnimationController _animController;
  late Animation<double> _cardFade;
  late Animation<Offset> _cardSlide;

  static const _red = Color(0xFFE4002B);
  static const _redDark = Color(0xFFA8001F);
  static const _redDeep = Color(0xFF2A000A);
  static const _surface = Color(0xFFF8F8F8);
  static const _borderColor = Color(0xFFF0F0F0);
  static const _labelColor = Color(0xFFBBBBBB);
  static const _textPrimary = Color(0xFF111111);
  static const _textMuted = Color(0xFFAAAAAA);

  @override
  void initState() {
    super.initState();
    SystemChrome.setSystemUIOverlayStyle(const SystemUiOverlayStyle(
      statusBarColor: Colors.transparent,
      statusBarIconBrightness: Brightness.light,
    ));
    final auth = Provider.of<AuthProvider>(context, listen: false);
    _namaController.text = auth.currentUser?.nama ?? '';
    _animController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 550),
    );
    _cardFade =
        CurvedAnimation(parent: _animController, curve: Curves.easeOut);
    _cardSlide = Tween<Offset>(
      begin: const Offset(0, 0.07),
      end: Offset.zero,
    ).animate(
        CurvedAnimation(parent: _animController, curve: Curves.easeOutCubic));
    _animController.forward();
  }

  @override
  void dispose() {
    _namaController.dispose();
    _nimController.dispose();
    _kelasController.dispose();
    _animController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (_namaController.text.trim().isEmpty) {
      _showSnackBar('Nama harus diisi', isError: true);
      return;
    }
    if (_nimController.text.isEmpty) {
      _showSnackBar('NIM harus diisi', isError: true);
      return;
    }
    setState(() => _isLoading = true);
    try {
      await Provider.of<AuthProvider>(context, listen: false)
          .completeGoogleProfile(
        nama: _namaController.text.trim(),
        nim: _nimController.text.trim(),
        kelas: _kelasController.text.trim(),
      );
      // AuthWrapper otomatis redirect setelah needsProfileCompletion = false
    } catch (e) {
      if (mounted) _showSnackBar(e.toString(), isError: true);
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  // Batalkan Google login & kembali ke LoginScreen
  Future<void> _cancel() async {
    await Provider.of<AuthProvider>(context, listen: false).logout();
    // AuthWrapper otomatis redirect ke LoginScreen karena currentUser = null
  }

  void _showSnackBar(String message, {bool isError = false}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message,
            style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600)),
        backgroundColor: isError ? _red : const Color(0xFF10B981),
        behavior: SnackBarBehavior.floating,
        shape:
            RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
        margin: const EdgeInsets.all(16),
      ),
    );
  }

  Widget _buildInput({
    required String label,
    required TextEditingController controller,
    required String hint,
    required IconData icon,
    bool isOptional = false,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
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
            if (isOptional) ...[
              const SizedBox(width: 6),
              Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 7, vertical: 2),
                decoration: BoxDecoration(
                  color: const Color(0xFFFFF0F2),
                  borderRadius: BorderRadius.circular(6),
                ),
                child: const Text(
                  'Opsional',
                  style: TextStyle(
                    fontSize: 9.5,
                    fontWeight: FontWeight.w700,
                    color: _red,
                  ),
                ),
              ),
            ],
          ],
        ),
        const SizedBox(height: 7),
        SizedBox(
          height: 50,
          child: TextField(
            controller: controller,
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
              prefixIcon:
                  Icon(icon, color: const Color(0xFFCCCCCC), size: 18),
              filled: true,
              fillColor: _surface,
              contentPadding:
                  const EdgeInsets.symmetric(horizontal: 16, vertical: 0),
              enabledBorder: OutlineInputBorder(
                borderRadius: BorderRadius.circular(12),
                borderSide:
                    const BorderSide(color: _borderColor, width: 1.5),
              ),
              focusedBorder: OutlineInputBorder(
                borderRadius: BorderRadius.circular(12),
                borderSide: const BorderSide(color: _red, width: 1.5),
              ),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildHero(String nama, String email) {
    return Container(
      width: double.infinity,
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          stops: [0.0, 0.4, 0.75, 1.0],
          colors: [_red, Color(0xFFB8001F), Color(0xFF6B0012), _redDeep],
        ),
      ),
      child: Stack(
        children: [
          Positioned.fill(child: CustomPaint(painter: _GridPainter())),
          Positioned(
            top: -50, right: -30,
            child: Container(
              width: 160, height: 160,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: Colors.white.withValues(alpha: 0.06),
              ),
            ),
          ),
          Positioned(
            bottom: 0, right: 0,
            child: SizedBox(
              width: 80, height: 80,
              child: CustomPaint(painter: _DotPainter()),
            ),
          ),
          SafeArea(
            bottom: false,
            child: Padding(
              padding: const EdgeInsets.fromLTRB(24, 14, 24, 22),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Tombol batal
                  GestureDetector(
                    onTap: _cancel,
                    child: Container(
                      width: 36, height: 36,
                      decoration: BoxDecoration(
                        color: Colors.white.withValues(alpha: 0.13),
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(
                          color: Colors.white.withValues(alpha: 0.18),
                        ),
                      ),
                      child: const Icon(
                        Icons.close_rounded,
                        size: 16,
                        color: Colors.white,
                      ),
                    ),
                  ),
                  const SizedBox(height: 14),
                  Container(
                    width: 46, height: 46,
                    decoration: BoxDecoration(
                      color: Colors.white.withValues(alpha: 0.15),
                      borderRadius: BorderRadius.circular(13),
                      border: Border.all(
                        color: Colors.white.withValues(alpha: 0.2),
                      ),
                    ),
                    child: const Icon(
                      Icons.assignment_ind_rounded,
                      size: 22,
                      color: Colors.white,
                    ),
                  ),
                  const SizedBox(height: 12),
                  const Text(
                    'Lengkapi Profil',
                    style: TextStyle(
                      fontSize: 24,
                      fontWeight: FontWeight.w800,
                      color: Colors.white,
                      letterSpacing: -0.7,
                    ),
                  ),
                  const SizedBox(height: 5),
                  // Tampilkan nama & email dari Google
                  Text(
                    nama.isNotEmpty ? nama : email,
                    style: TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.w600,
                      color: Colors.white.withValues(alpha: 0.55),
                      letterSpacing: 0.3,
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

  @override
  Widget build(BuildContext context) {
    final auth = Provider.of<AuthProvider>(context, listen: false);
    final nama = auth.currentUser?.nama ?? '';
    final email = auth.currentUser?.email ?? '';

    return Scaffold(
      backgroundColor: Colors.white,
      body: SingleChildScrollView(
        child: Column(
          children: [
            _buildHero(nama, email),

            FadeTransition(
              opacity: _cardFade,
              child: SlideTransition(
                position: _cardSlide,
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(22, 22, 22, 32),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Info box
                      Container(
                        padding: const EdgeInsets.all(13),
                        decoration: BoxDecoration(
                          color: const Color(0xFFFFF0F2),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                            color: const Color(0xFFFFCDD5),
                          ),
                        ),
                        child: Row(
                          children: [
                            const Icon(
                              Icons.info_outline_rounded,
                              color: _red,
                              size: 16,
                            ),
                            const SizedBox(width: 8),
                            Expanded(
                              child: Text(
                                'Akun Google berhasil terhubung. Lengkapi data mahasiswa untuk melanjutkan.',
                                style: TextStyle(
                                  fontSize: 11.5,
                                  color: _red.withValues(alpha: 0.8),
                                  fontWeight: FontWeight.w500,
                                  height: 1.4,
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),

                      const SizedBox(height: 22),

                      _buildInput(
                        label: 'Nama Lengkap',
                        controller: _namaController,
                        hint: 'Masukkan nama lengkap',
                        icon: Icons.person_outline_rounded,
                      ),

                      const SizedBox(height: 16),

                      _buildInput(
                        label: 'NIM',
                        controller: _nimController,
                        hint: 'Masukkan NIM Anda',
                        icon: Icons.badge_outlined,
                      ),

                      const SizedBox(height: 16),

                      _buildInput(
                        label: 'Kelas',
                        controller: _kelasController,
                        hint: 'Contoh: S1SIKJ-24-03',
                        icon: Icons.grid_view_rounded,
                        isOptional: true,
                      ),

                      const SizedBox(height: 28),

                      // Tombol lanjutkan
                      SizedBox(
                        width: double.infinity,
                        height: 50,
                        child: DecoratedBox(
                          decoration: BoxDecoration(
                            gradient: const LinearGradient(
                              colors: [_red, _redDark],
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
                                      color: Colors.white,
                                      strokeWidth: 2,
                                    ),
                                  )
                                : const Row(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      Text(
                                        'Lanjutkan',
                                        style: TextStyle(
                                          fontSize: 14,
                                          fontWeight: FontWeight.w700,
                                          color: Colors.white,
                                        ),
                                      ),
                                      SizedBox(width: 6),
                                      Icon(
                                        Icons.arrow_forward_rounded,
                                        size: 16,
                                        color: Colors.white,
                                      ),
                                    ],
                                  ),
                          ),
                        ),
                      ),

                      const SizedBox(height: 16),

                      // Tombol batal
                      SizedBox(
                        width: double.infinity,
                        height: 46,
                        child: TextButton(
                          onPressed: _isLoading ? null : _cancel,
                          style: TextButton.styleFrom(
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(13),
                              side: const BorderSide(
                                color: Color(0xFFEEEEEE),
                              ),
                            ),
                          ),
                          child: const Text(
                            'Batal, gunakan akun lain',
                            style: TextStyle(
                              fontSize: 13,
                              color: _textMuted,
                              fontWeight: FontWeight.w600,
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