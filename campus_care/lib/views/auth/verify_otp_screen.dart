import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

import '../../core/api_client.dart';
import '../../main.dart';
import '../../widgets/top_notification.dart';
import 'reset_password_screen.dart';

enum OtpMode {
  resetPassword,
  register,
}

class VerifyOtpScreen extends StatefulWidget {
  final String email;
  final OtpMode mode;

  const VerifyOtpScreen({
    super.key,
    required this.email,
    required this.mode,
  });

  @override
  State<VerifyOtpScreen> createState() => _VerifyOtpScreenState();
}

class _VerifyOtpScreenState extends State<VerifyOtpScreen> {
  final TextEditingController _codeController = TextEditingController();

  bool _isLoading = false;
  bool _isResending = false;

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

    SystemChrome.setSystemUIOverlayStyle(
      const SystemUiOverlayStyle(
        statusBarColor: Colors.transparent,
        statusBarIconBrightness: Brightness.light,
      ),
    );
  }

  @override
  void dispose() {
    _codeController.dispose();
    super.dispose();
  }

  bool get _isRegisterMode => widget.mode == OtpMode.register;

  String get _verifyEndpoint {
    if (_isRegisterMode) {
      return '/verify-register-otp';
    }
    return '/verify-reset-code';
  }

  String get _resendEndpoint {
    if (_isRegisterMode) {
      return '/resend-register-otp';
    }
    return '/forgot-password';
  }

  String get _heroTitle {
    if (_isRegisterMode) {
      return 'Verifikasi Email';
    }
    return 'Verifikasi OTP';
  }

  String get _bodyTitle {
    if (_isRegisterMode) {
      return 'Masukkan kode verifikasi';
    }
    return 'Masukkan kode OTP';
  }

  String get _bodySubtitle {
    if (_isRegisterMode) {
      return 'Kode verifikasi sudah dikirim ke email kamu. Masukkan kode 6 digit untuk mengaktifkan akun Campus Care.';
    }

    return 'Kode reset password sudah dikirim ke email kamu. Masukkan kode 6 digit untuk melanjutkan proses reset password.';
  }

  String _cleanMessage(dynamic message) {
    return message.toString().replaceAll('Exception:', '').trim();
  }

  Future<void> _verifyCode() async {
    final code = _codeController.text.trim();

    if (code.isEmpty) {
      TopNotification.show(
        context,
        message: 'Kode OTP harus diisi',
        type: TopNotificationType.warning,
      );
      return;
    }

    if (code.length != 6) {
      TopNotification.show(
        context,
        message: 'Kode OTP harus 6 digit',
        type: TopNotificationType.warning,
      );
      return;
    }

    setState(() => _isLoading = true);

    try {
      final response = await ApiClient().post(
        _verifyEndpoint,
        {
          'email': widget.email,
          'code': code,
        },
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        if (!mounted) return;

        TopNotification.show(
          context,
          message: data['message'] ?? 'Kode OTP berhasil diverifikasi',
          type: TopNotificationType.success,
        );

        if (_isRegisterMode) {
          Navigator.pushAndRemoveUntil(
            context,
            MaterialPageRoute(
              builder: (_) => const AuthWrapper(),
            ),
            (route) => false,
          );
        } else {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (_) => ResetPasswordScreen(
                email: widget.email,
                code: code,
              ),
            ),
          );
        }
      } else {
        TopNotification.show(
          context,
          message: data['message'] ?? 'Kode OTP salah',
          type: TopNotificationType.error,
        );
      }
    } catch (e) {
      if (!mounted) return;

      TopNotification.show(
        context,
        message: _cleanMessage(e),
        type: TopNotificationType.error,
      );
    } finally {
      if (mounted) {
        setState(() => _isLoading = false);
      }
    }
  }

  Future<void> _resendCode() async {
    setState(() => _isResending = true);

    try {
      final response = await ApiClient().post(
        _resendEndpoint,
        {
          'email': widget.email,
        },
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        if (!mounted) return;

        _codeController.clear();

        TopNotification.show(
          context,
          message: data['message'] ?? 'Kode OTP baru berhasil dikirim',
          type: TopNotificationType.success,
        );
      } else {
        TopNotification.show(
          context,
          message: data['message'] ?? 'Gagal mengirim ulang kode OTP',
          type: TopNotificationType.error,
        );
      }
    } catch (e) {
      if (!mounted) return;

      TopNotification.show(
        context,
        message: _cleanMessage(e),
        type: TopNotificationType.error,
      );
    } finally {
      if (mounted) {
        setState(() => _isResending = false);
      }
    }
  }

  Widget _buildHero() {
    return Container(
      height: 185,
      width: double.infinity,
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          stops: [0.0, 0.45, 1.0],
          colors: [
            _red,
            _redDark,
            _redDeep,
          ],
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
            top: -55,
            right: -45,
            child: Container(
              width: 190,
              height: 190,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: Colors.white.withValues(alpha: 0.07),
              ),
            ),
          ),
          Positioned(
            bottom: -90,
            left: -70,
            child: Container(
              width: 240,
              height: 240,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                border: Border.all(
                  color: Colors.white.withValues(alpha: 0.08),
                ),
              ),
            ),
          ),
          Positioned(
            bottom: 0,
            right: 0,
            child: SizedBox(
              width: 82,
              height: 82,
              child: CustomPaint(
                painter: _DotPainter(),
              ),
            ),
          ),
          SafeArea(
            bottom: false,
            child: Padding(
              padding: const EdgeInsets.fromLTRB(22, 10, 22, 22),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  GestureDetector(
                    onTap: () => Navigator.pop(context),
                    child: Container(
                      width: 38,
                      height: 38,
                      decoration: BoxDecoration(
                        color: Colors.white.withValues(alpha: 0.14),
                        borderRadius: BorderRadius.circular(11),
                        border: Border.all(
                          color: Colors.white.withValues(alpha: 0.18),
                        ),
                      ),
                      child: const Icon(
                        Icons.arrow_back_ios_new_rounded,
                        color: Colors.white,
                        size: 16,
                      ),
                    ),
                  ),
                  const Spacer(),
                  Row(
                    children: [
                      Container(
                        width: 48,
                        height: 48,
                        decoration: BoxDecoration(
                          color: Colors.white.withValues(alpha: 0.16),
                          borderRadius: BorderRadius.circular(15),
                          border: Border.all(
                            color: Colors.white.withValues(alpha: 0.20),
                          ),
                        ),
                        child: Icon(
                          _isRegisterMode
                              ? Icons.mark_email_read_outlined
                              : Icons.verified_user_outlined,
                          color: Colors.white,
                          size: 26,
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Text(
                          _heroTitle,
                          style: const TextStyle(
                            fontSize: 24,
                            fontWeight: FontWeight.w800,
                            color: Colors.white,
                            letterSpacing: -0.7,
                          ),
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

  Widget _buildEmailCard() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(horizontal: 13, vertical: 12),
      decoration: BoxDecoration(
        color: const Color(0xFFFFF0F2),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(
          color: const Color(0xFFFFCDD5),
        ),
      ),
      child: Row(
        children: [
          Container(
            width: 34,
            height: 34,
            decoration: BoxDecoration(
              color: Colors.white.withValues(alpha: 0.8),
              borderRadius: BorderRadius.circular(10),
            ),
            child: const Icon(
              Icons.mail_outline_rounded,
              color: _red,
              size: 17,
            ),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: Text(
              widget.email,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.w700,
                color: _redDark,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildOtpInput() {
    return TextField(
      controller: _codeController,
      keyboardType: TextInputType.number,
      maxLength: 6,
      textAlign: TextAlign.center,
      inputFormatters: [
        FilteringTextInputFormatter.digitsOnly,
      ],
      style: const TextStyle(
        fontSize: 24,
        letterSpacing: 8,
        color: _textPrimary,
        fontWeight: FontWeight.w800,
      ),
      decoration: InputDecoration(
        counterText: '',
        hintText: '000000',
        hintStyle: const TextStyle(
          fontSize: 24,
          letterSpacing: 8,
          color: Color(0xFFDDDDDD),
          fontWeight: FontWeight.w800,
        ),
        filled: true,
        fillColor: _surface,
        contentPadding: const EdgeInsets.symmetric(
          horizontal: 16,
          vertical: 14,
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(
            color: _borderColor,
            width: 1.5,
          ),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(
            color: _red,
            width: 1.5,
          ),
        ),
      ),
    );
  }

  Widget _buildInfoCard() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: const Color(0xFFFFF8E7),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(
          color: const Color(0xFFFDE68A),
        ),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Icon(
            Icons.info_outline_rounded,
            color: Color(0xFFF59E0B),
            size: 18,
          ),
          const SizedBox(width: 10),
          Expanded(
            child: Text(
              _isRegisterMode
                  ? 'Cek inbox atau folder spam. Akun belum aktif sebelum kode diverifikasi.'
                  : 'Cek inbox atau folder spam. Gunakan kode terbaru jika melakukan kirim ulang.',
              style: const TextStyle(
                fontSize: 11.8,
                color: Color(0xFF92400E),
                fontWeight: FontWeight.w500,
                height: 1.4,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildVerifyButton() {
    return SizedBox(
      width: double.infinity,
      height: 50,
      child: DecoratedBox(
        decoration: BoxDecoration(
          gradient: const LinearGradient(
            begin: Alignment.centerLeft,
            end: Alignment.centerRight,
            colors: [_red, _redDark],
          ),
          borderRadius: BorderRadius.circular(13),
        ),
        child: ElevatedButton(
          onPressed: _isLoading ? null : _verifyCode,
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
                  width: 20,
                  height: 20,
                  child: CircularProgressIndicator(
                    color: Colors.white,
                    strokeWidth: 2,
                  ),
                )
              : Text(
                  _isRegisterMode ? 'Aktifkan Akun' : 'Verifikasi Kode',
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 14,
                    fontWeight: FontWeight.w700,
                  ),
                ),
        ),
      ),
    );
  }

  Widget _buildResendButton() {
    return Center(
      child: TextButton(
        onPressed: _isResending ? null : _resendCode,
        child: Text(
          _isResending ? 'Mengirim ulang...' : 'Kirim ulang kode OTP',
          style: const TextStyle(
            color: _red,
            fontSize: 12.5,
            fontWeight: FontWeight.w700,
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,

      body: SingleChildScrollView(
        child: Column(
          children: [
            _buildHero(),

            Padding(
              padding: const EdgeInsets.fromLTRB(26, 30, 26, 32),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    _bodyTitle,
                    style: const TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w800,
                      color: _textPrimary,
                      letterSpacing: -0.4,
                    ),
                  ),

                  const SizedBox(height: 6),

                  Text(
                    _bodySubtitle,
                    style: const TextStyle(
                      fontSize: 12.5,
                      color: _textMuted,
                      height: 1.45,
                      fontWeight: FontWeight.w500,
                    ),
                  ),

                  const SizedBox(height: 16),

                  _buildEmailCard(),

                  const SizedBox(height: 18),

                  _buildInfoCard(),

                  const SizedBox(height: 24),

                  const Text(
                    'KODE OTP',
                    style: TextStyle(
                      fontSize: 10.5,
                      fontWeight: FontWeight.w700,
                      color: _labelColor,
                      letterSpacing: 0.9,
                    ),
                  ),

                  const SizedBox(height: 7),

                  _buildOtpInput(),

                  const SizedBox(height: 24),

                  _buildVerifyButton(),

                  const SizedBox(height: 14),

                  _buildResendButton(),
                ],
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
    final paint = Paint()
      ..color = Colors.white.withValues(alpha: 0.18);

    const spacing = 12.0;
    const radius = 1.5;

    for (double x = spacing; x < size.width; x += spacing) {
      for (double y = spacing; y < size.height; y += spacing) {
        canvas.drawCircle(
          Offset(x, y),
          radius,
          paint,
        );
      }
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}