import 'dart:io';
import 'dart:typed_data';

import 'package:crop_your_image/crop_your_image.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:image_picker/image_picker.dart';
import 'package:provider/provider.dart';

import '../../providers/auth_provider.dart';
import '../../main.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen>
    with SingleTickerProviderStateMixin {
  final TextEditingController _namaController = TextEditingController();
  final ImagePicker _picker = ImagePicker();

  File? _newProfileImage;
  bool _isLoading = false;
  bool _isEditing = false;

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
  static const _textMuted = Color(0xFFAAAAAA);
  static const _white = Colors.white;

  @override
  void initState() {
    super.initState();

    SystemChrome.setSystemUIOverlayStyle(
      const SystemUiOverlayStyle(
        statusBarColor: Colors.transparent,
        statusBarIconBrightness: Brightness.light,
      ),
    );

    _animController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 400),
    );

    _fade = CurvedAnimation(
      parent: _animController,
      curve: Curves.easeOut,
    );

    _animController.forward();

    WidgetsBinding.instance.addPostFrameCallback((_) {
      final user = Provider.of<AuthProvider>(context, listen: false).currentUser;
      if (user != null) {
        _namaController.text = user.nama;
      }
    });
  }

  @override
  void dispose() {
    _namaController.dispose();
    _animController.dispose();
    super.dispose();
  }

  Future<void> _pickImage(ImageSource source) async {
    try {
      final XFile? image = await _picker.pickImage(
        source: source,
        imageQuality: 85,
      );

      if (image == null) return;

      final bytes = await image.readAsBytes();

      if (!mounted) return;

      final Uint8List? croppedBytes = await Navigator.push(
        context,
        MaterialPageRoute(
          builder: (_) => ProfileCropScreen(imageBytes: bytes),
        ),
      );

      if (croppedBytes == null) return;

      final tempDir = Directory.systemTemp;
      final file = File(
        '${tempDir.path}/profile_${DateTime.now().millisecondsSinceEpoch}.png',
      );

      await file.writeAsBytes(croppedBytes);

      if (!mounted) return;

      setState(() {
        _newProfileImage = file;
      });
    } catch (e) {
      if (!mounted) return;
      _showSnackBar('Gagal memilih foto: $e', isError: true);
    }
  }

  Future<void> _simpanProfil() async {
    if (_namaController.text.trim().isEmpty) {
      _showSnackBar('Nama tidak boleh kosong', isError: true);
      return;
    }

    setState(() => _isLoading = true);

    try {
      await Provider.of<AuthProvider>(context, listen: false).updateProfile(
        nama: _namaController.text.trim(),
        foto: _newProfileImage,
      );

      if (!mounted) return;

      setState(() {
        _isEditing = false;
        _newProfileImage = null;
      });

      _showSnackBar('Profil berhasil diperbarui');
    } catch (e) {
      if (!mounted) return;
      _showSnackBar(e.toString(), isError: true);
    } finally {
      if (mounted) {
        setState(() => _isLoading = false);
      }
    }
  }

  Future<void> _logout() async {
    final auth = Provider.of<AuthProvider>(context, listen: false);

    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: _white,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
        ),
        title: const Text(
          'Keluar dari akun?',
          style: TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.w800,
            color: _textPrimary,
          ),
        ),
        content: const Text(
          'Kamu akan keluar dari sesi ini.',
          style: TextStyle(
            fontSize: 13,
            color: _textMuted,
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: const Text(
              'Batal',
              style: TextStyle(
                color: _textMuted,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(ctx, true),
            style: ElevatedButton.styleFrom(
              backgroundColor: _red,
              foregroundColor: _white,
              elevation: 0,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(10),
              ),
            ),
            child: const Text(
              'Keluar',
              style: TextStyle(
                fontWeight: FontWeight.w700,
              ),
            ),
          ),
        ],
      ),
    );

    if (confirm != true) return;

    await auth.logout();

    if (!mounted) return;

    Navigator.of(context, rootNavigator: true).pushAndRemoveUntil(
      MaterialPageRoute(
        builder: (_) => const AuthWrapper(),
      ),
      (route) => false,
    );
  }

  void _showSnackBar(String message, {bool isError = false}) {
    final cleanMessage = message.replaceAll('Exception:', '').trim();

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(
          cleanMessage.isNotEmpty ? cleanMessage : 'Terjadi kesalahan.',
          style: const TextStyle(
            fontSize: 13,
            fontWeight: FontWeight.w600,
          ),
        ),
        backgroundColor: isError ? _red : const Color(0xFF10B981),
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(10),
        ),
        margin: const EdgeInsets.all(16),
      ),
    );
  }

  Widget _buildHero(String nama, String? photoUrl, String role) {
    final initial = nama.isNotEmpty ? nama[0].toUpperCase() : '?';

    return Container(
      width: double.infinity,
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
            child: CustomPaint(painter: _GridPainter()),
          ),
          Positioned(
            top: -50,
            right: -30,
            child: Container(
              width: 160,
              height: 160,
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
              width: 80,
              height: 80,
              child: CustomPaint(painter: _DotPainter()),
            ),
          ),
          SafeArea(
            bottom: false,
            child: Column(
              children: [
                Padding(
                  padding: const EdgeInsets.fromLTRB(16, 10, 16, 0),
                  child: Row(
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
                      const SizedBox(width: 12),
                      const Text(
                        'Profil Saya',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w700,
                          color: _white,
                          letterSpacing: -0.2,
                        ),
                      ),
                      const Spacer(),
                      GestureDetector(
                        onTap: () => setState(() {
                          _isEditing = !_isEditing;
                          if (!_isEditing) _newProfileImage = null;
                        }),
                        child: Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 7,
                          ),
                          decoration: BoxDecoration(
                            color: _white.withValues(alpha: 0.15),
                            borderRadius: BorderRadius.circular(10),
                            border: Border.all(
                              color: _white.withValues(alpha: 0.2),
                            ),
                          ),
                          child: Text(
                            _isEditing ? 'Batal' : 'Edit',
                            style: const TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.w700,
                              color: _white,
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 20),
                Stack(
                  alignment: Alignment.bottomRight,
                  children: [
                    Container(
                      width: 90,
                      height: 90,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        border: Border.all(
                          color: _white.withValues(alpha: 0.5),
                          width: 3,
                        ),
                      ),
                      child: ClipOval(
                        child: _newProfileImage != null
                            ? Image.file(
                                _newProfileImage!,
                                fit: BoxFit.cover,
                              )
                            : photoUrl != null && photoUrl.isNotEmpty
                                ? Image.network(
                                    photoUrl,
                                    fit: BoxFit.cover,
                                    errorBuilder: (_, _, _) =>
                                        _buildInitialCircle(initial),
                                  )
                                : _buildInitialCircle(initial),
                      ),
                    ),
                    if (_isEditing)
                      GestureDetector(
                        onTap: () => _pickImage(ImageSource.gallery),
                        child: Container(
                          width: 30,
                          height: 30,
                          decoration: BoxDecoration(
                            color: _white,
                            shape: BoxShape.circle,
                            border: Border.all(
                              color: _border,
                              width: 1.5,
                            ),
                          ),
                          child: const Icon(
                            Icons.camera_alt_rounded,
                            color: _red,
                            size: 16,
                          ),
                        ),
                      ),
                  ],
                ),
                const SizedBox(height: 12),
                Text(
                  nama,
                  style: const TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.w800,
                    color: _white,
                    letterSpacing: -0.4,
                  ),
                ),
                const SizedBox(height: 5),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 12,
                    vertical: 4,
                  ),
                  decoration: BoxDecoration(
                    color: _white.withValues(alpha: 0.18),
                    border: Border.all(
                      color: _white.withValues(alpha: 0.2),
                    ),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Text(
                    _getRoleLabel(role),
                    style: const TextStyle(
                      fontSize: 11,
                      color: _white,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
                const SizedBox(height: 24),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInitialCircle(String initial) {
    return Container(
      color: _white.withValues(alpha: 0.15),
      alignment: Alignment.center,
      child: Text(
        initial,
        style: const TextStyle(
          fontSize: 32,
          fontWeight: FontWeight.w800,
          color: _white,
        ),
      ),
    );
  }

  String _getRoleLabel(String role) {
    switch (role) {
      case 'admin':
        return '⚙️ Admin';
      case 'satpam':
        return '🛡️ Satpam';
      default:
        return '🎓 Mahasiswa';
    }
  }

  Widget _buildInfoItem({
    required IconData icon,
    required String label,
    required String value,
  }) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 12),
      child: Row(
        children: [
          Container(
            width: 38,
            height: 38,
            decoration: BoxDecoration(
              color: _surface,
              borderRadius: BorderRadius.circular(10),
            ),
            alignment: Alignment.center,
            child: Icon(icon, color: _labelColor, size: 18),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  label.toUpperCase(),
                  style: const TextStyle(
                    fontSize: 9.5,
                    fontWeight: FontWeight.w700,
                    color: _labelColor,
                    letterSpacing: 0.8,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  value.isNotEmpty ? value : '-',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w600,
                    color: _textPrimary,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEditField({
    required String label,
    required TextEditingController controller,
    required String hint,
    required IconData icon,
    bool readOnly = false,
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
        SizedBox(
          height: 50,
          child: TextField(
            controller: controller,
            readOnly: readOnly,
            style: TextStyle(
              fontSize: 14,
              color: readOnly ? _textMuted : _textPrimary,
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
              fillColor: readOnly ? const Color(0xFFF5F5F5) : _surface,
              contentPadding: const EdgeInsets.symmetric(
                horizontal: 16,
                vertical: 0,
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
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildSectionLabel(String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Row(
        children: [
          Container(
            width: 3,
            height: 14,
            decoration: BoxDecoration(
              color: _red,
              borderRadius: BorderRadius.circular(2),
            ),
          ),
          const SizedBox(width: 8),
          Text(
            text,
            style: const TextStyle(
              fontSize: 13,
              fontWeight: FontWeight.w700,
              color: _textPrimary,
              letterSpacing: -0.2,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildLogoutButton() {
    return SizedBox(
      width: double.infinity,
      height: 50,
      child: OutlinedButton.icon(
        onPressed: _logout,
        icon: const Icon(
          Icons.logout_rounded,
          size: 17,
          color: _red,
        ),
        label: const Text(
          'Keluar dari Akun',
          style: TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.w700,
            color: _red,
          ),
        ),
        style: OutlinedButton.styleFrom(
          side: const BorderSide(
            color: _red,
            width: 1.2,
          ),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(13),
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<AuthProvider>(
      builder: (context, auth, _) {
        final user = auth.currentUser;

        if (user == null) {
          return const AuthWrapper();
        }

        return Scaffold(
          backgroundColor: _white,
          body: FadeTransition(
            opacity: _fade,
            child: SingleChildScrollView(
              child: Column(
                children: [
                  _buildHero(
                    user.nama,
                    user.fotoProfilUrl,
                    user.role,
                  ),
                  Padding(
                    padding: const EdgeInsets.fromLTRB(22, 22, 22, 32),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        if (!_isEditing) ...[
                          _buildSectionLabel('Informasi Akun'),
                          const SizedBox(height: 4),
                          Container(
                            decoration: BoxDecoration(
                              color: _white,
                              borderRadius: BorderRadius.circular(14),
                              border: Border.all(color: _border),
                            ),
                            padding: const EdgeInsets.symmetric(horizontal: 16),
                            child: Column(
                              children: [
                                _buildInfoItem(
                                  icon: Icons.mail_outline_rounded,
                                  label: 'Email',
                                  value: user.email,
                                ),
                                const Divider(
                                  height: 1,
                                  color: Color(0xFFF5F5F5),
                                ),
                                if (user.nim != null &&
                                    user.nim!.isNotEmpty) ...[
                                  _buildInfoItem(
                                    icon: Icons.badge_outlined,
                                    label: 'NIM',
                                    value: user.nim!,
                                  ),
                                  const Divider(
                                    height: 1,
                                    color: Color(0xFFF5F5F5),
                                  ),
                                ],
                                if (user.kelas != null &&
                                    user.kelas!.isNotEmpty)
                                  _buildInfoItem(
                                    icon: Icons.grid_view_rounded,
                                    label: 'Kelas',
                                    value: user.kelas!,
                                  ),
                              ],
                            ),
                          ),
                          const SizedBox(height: 24),
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
                              child: ElevatedButton.icon(
                                onPressed: () =>
                                    setState(() => _isEditing = true),
                                icon: const Icon(
                                  Icons.edit_outlined,
                                  size: 16,
                                  color: _white,
                                ),
                                label: const Text(
                                  'Edit Profil',
                                  style: TextStyle(
                                    fontSize: 14,
                                    fontWeight: FontWeight.w700,
                                    color: _white,
                                  ),
                                ),
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: Colors.transparent,
                                  shadowColor: Colors.transparent,
                                  elevation: 0,
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(13),
                                  ),
                                ),
                              ),
                            ),
                          ),
                          const SizedBox(height: 14),
                          _buildLogoutButton(),
                        ] else ...[
                          _buildSectionLabel('Edit Informasi'),
                          const SizedBox(height: 14),
                          _buildEditField(
                            label: 'Nama Lengkap',
                            controller: _namaController,
                            hint: 'Masukkan nama lengkap',
                            icon: Icons.person_outline_rounded,
                          ),
                          const SizedBox(height: 14),
                          _buildEditField(
                            label: 'Email',
                            controller: TextEditingController(text: user.email),
                            hint: '',
                            icon: Icons.mail_outline_rounded,
                            readOnly: true,
                          ),
                          const SizedBox(height: 24),
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
                                onPressed: _isLoading ? null : _simpanProfil,
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
                                    : const Row(
                                        mainAxisAlignment:
                                            MainAxisAlignment.center,
                                        children: [
                                          Icon(
                                            Icons.save_rounded,
                                            size: 16,
                                            color: _white,
                                          ),
                                          SizedBox(width: 6),
                                          Text(
                                            'Simpan Perubahan',
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
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
        );
      },
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

class ProfileCropScreen extends StatefulWidget {
  final Uint8List imageBytes;

  const ProfileCropScreen({
    super.key,
    required this.imageBytes,
  });

  @override
  State<ProfileCropScreen> createState() => _ProfileCropScreenState();
}

class _ProfileCropScreenState extends State<ProfileCropScreen> {
  final CropController _controller = CropController();
  bool _isCropping = false;

  static const _red = Color(0xFFE4002B);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black,
      appBar: AppBar(
        backgroundColor: _red,
        foregroundColor: Colors.white,
        title: const Text(
          'Atur Foto Profil',
          style: TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.w700,
          ),
        ),
        actions: [
          TextButton(
            onPressed: _isCropping
                ? null
                : () {
                    setState(() => _isCropping = true);
                    _controller.crop();
                  },
            child: const Text(
              'Simpan',
              style: TextStyle(
                color: Colors.white,
                fontWeight: FontWeight.w700,
              ),
            ),
          ),
        ],
      ),
      body: Stack(
        children: [
          Center(
            child: Crop(
              image: widget.imageBytes,
              controller: _controller,
              aspectRatio: 1,
              withCircleUi: true,
              interactive: true,
              fixCropRect: true,
              onCropped: (result) {
                switch (result) {
                  case CropSuccess(:final croppedImage):
                    Navigator.pop(context, croppedImage);
                    break;
                  case CropFailure(:final cause):
                    setState(() => _isCropping = false);
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(
                        content: Text('Gagal crop foto: $cause'),
                      ),
                    );
                    break;
                }
              },
            ),
          ),
          Positioned(
            left: 20,
            right: 20,
            bottom: 28,
            child: Container(
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                color: Colors.white.withValues(alpha: 0.12),
                borderRadius: BorderRadius.circular(14),
                border: Border.all(
                  color: Colors.white.withValues(alpha: 0.15),
                ),
              ),
              child: const Text(
                'Geser dan zoom foto agar posisi wajah pas di lingkaran.',
                textAlign: TextAlign.center,
                style: TextStyle(
                  color: Colors.white,
                  fontSize: 12,
                  fontWeight: FontWeight.w500,
                ),
              ),
            ),
          ),
          if (_isCropping)
            Container(
              color: Colors.black.withValues(alpha: 0.45),
              child: const Center(
                child: CircularProgressIndicator(color: Colors.white),
              ),
            ),
        ],
      ),
    );
  }
}