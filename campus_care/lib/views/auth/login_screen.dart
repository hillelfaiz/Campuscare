import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';

import '../../providers/auth_provider.dart';
import '../../widgets/top_notification.dart';
import 'forgot_password_screen.dart';
import 'register_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen>
    with SingleTickerProviderStateMixin {
  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _passwordController = TextEditingController();

  bool _isLoading = false;
  bool _obscurePassword = true;

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

    SystemChrome.setSystemUIOverlayStyle(
      const SystemUiOverlayStyle(
        statusBarColor: Colors.transparent,
        statusBarIconBrightness: Brightness.light,
      ),
    );

    _animController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 600),
    );

    _cardFade = CurvedAnimation(
      parent: _animController,
      curve: Curves.easeOut,
    );

    _cardSlide = Tween<Offset>(
      begin: const Offset(0, 0.08),
      end: Offset.zero,
    ).animate(
      CurvedAnimation(
        parent: _animController,
        curve: Curves.easeOutCubic,
      ),
    );

    _animController.forward();
  }

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    _animController.dispose();
    super.dispose();
  }

  bool _isValidEmail(String email) {
    return RegExp(r'^[^@]+@[^@]+\.[^@]+').hasMatch(email);
  }

  Future<void> _login() async {
    if (_isLoading) return;

    final email = _emailController.text.trim();
    final password = _passwordController.text.trim();

    if (email.isEmpty || password.isEmpty) {
      TopNotification.show(
        context,
        message: 'Email dan password harus diisi',
        type: TopNotificationType.warning,
      );
      return;
    }

    if (!_isValidEmail(email)) {
      TopNotification.show(
        context,
        message: 'Format email belum valid',
        type: TopNotificationType.warning,
      );
      return;
    }

    FocusScope.of(context).unfocus();

    setState(() => _isLoading = true);

    try {
      final authProvider = Provider.of<AuthProvider>(context, listen: false);

      debugPrint('================ LOGIN DEBUG ================');
      debugPrint('EMAIL INPUT: $email');
      debugPrint('PASSWORD LENGTH: ${password.length}');
      debugPrint('PROSES LOGIN DIMULAI...');

      final success = await authProvider.login(email, password);

      debugPrint('LOGIN SUCCESS VALUE: $success');
      debugPrint('AUTH ERROR MESSAGE: ${authProvider.errorMessage}');
      debugPrint('=============================================');

      if (!mounted) return;

      if (!success) {
        TopNotification.show(
          context,
          message: authProvider.errorMessage ??
              'Login gagal. Email atau password salah.',
          type: TopNotificationType.error,
        );
        return;
      }

      TopNotification.show(
        context,
        message: 'Login berhasil',
        type: TopNotificationType.success,
      );
    } catch (e) {
      debugPrint('LOGIN EXCEPTION: $e');

      if (!mounted) return;

      TopNotification.show(
        context,
        message: 'Login gagal: $e',
        type: TopNotificationType.error,
      );
    } finally {
      if (mounted) {
        setState(() => _isLoading = false);
      }
    }
  }

  Future<void> _loginGoogle() async {
    if (_isLoading) return;

    FocusScope.of(context).unfocus();

    setState(() => _isLoading = true);

    try {
      final authProvider = Provider.of<AuthProvider>(context, listen: false);

      debugPrint('================ GOOGLE LOGIN DEBUG ================');
      debugPrint('PROSES LOGIN GOOGLE DIMULAI...');

      final success = await authProvider.loginWithGoogle();

      debugPrint('GOOGLE LOGIN SUCCESS VALUE: $success');
      debugPrint('AUTH ERROR MESSAGE: ${authProvider.errorMessage}');
      debugPrint('====================================================');

      if (!mounted) return;

      if (!success) {
        TopNotification.show(
          context,
          message: authProvider.errorMessage ??
              'Login Google gagal. Silakan coba lagi.',
          type: TopNotificationType.error,
        );
        return;
      }

      TopNotification.show(
        context,
        message: 'Login Google berhasil',
        type: TopNotificationType.success,
      );
    } catch (e) {
      debugPrint('GOOGLE LOGIN EXCEPTION: $e');

      if (!mounted) return;

      TopNotification.show(
        context,
        message: 'Login Google gagal: $e',
        type: TopNotificationType.error,
      );
    } finally {
      if (mounted) {
        setState(() => _isLoading = false);
      }
    }
  }

  Widget _buildInput({
    required String label,
    required TextEditingController controller,
    required String hint,
    required IconData icon,
    bool isPassword = false,
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
            obscureText: isPassword && _obscurePassword,
            keyboardType:
                isPassword ? TextInputType.text : TextInputType.emailAddress,
            textInputAction:
                isPassword ? TextInputAction.done : TextInputAction.next,
            enabled: !_isLoading,
            onSubmitted: (_) {
              if (isPassword && !_isLoading) {
                _login();
              }
            },
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
                fontWeight: FontWeight.w400,
              ),
              prefixIcon: Icon(
                icon,
                color: const Color(0xFFCCCCCC),
                size: 18,
              ),
              suffixIcon: isPassword
                  ? IconButton(
                      icon: Icon(
                        _obscurePassword
                            ? Icons.visibility_off_outlined
                            : Icons.visibility_outlined,
                        color: const Color(0xFFCCCCCC),
                        size: 18,
                      ),
                      onPressed: _isLoading
                          ? null
                          : () {
                              setState(() {
                                _obscurePassword = !_obscurePassword;
                              });
                            },
                    )
                  : null,
              filled: true,
              fillColor: _surface,
              contentPadding: const EdgeInsets.symmetric(
                horizontal: 16,
                vertical: 0,
              ),
              enabledBorder: OutlineInputBorder(
                borderRadius: BorderRadius.circular(12),
                borderSide: const BorderSide(
                  color: _borderColor,
                  width: 1.5,
                ),
              ),
              disabledBorder: OutlineInputBorder(
                borderRadius: BorderRadius.circular(12),
                borderSide: const BorderSide(
                  color: _borderColor,
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

  Widget _buildHero() {
    return Container(
      height: 240,
      width: double.infinity,
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          stops: [0.0, 0.4, 0.75, 1.0],
          colors: [
            _red,
            Color(0xFFB8001F),
            Color(0xFF6B0012),
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
            top: -60,
            right: -50,
            child: Container(
              width: 200,
              height: 200,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: Colors.white.withValues(alpha: 0.07),
              ),
            ),
          ),
          Positioned(
            top: 70,
            right: 90,
            child: Container(
              width: 100,
              height: 100,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: Colors.white.withValues(alpha: 0.05),
              ),
            ),
          ),
          Positioned(
            bottom: -90,
            left: -70,
            child: Container(
              width: 260,
              height: 260,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                border: Border.all(
                  color: Colors.white.withValues(alpha: 0.08),
                  width: 1,
                ),
              ),
            ),
          ),
          Positioned(
            bottom: 0,
            right: 0,
            child: SizedBox(
              width: 90,
              height: 90,
              child: CustomPaint(
                painter: _DotPainter(),
              ),
            ),
          ),
          Positioned(
            bottom: 34,
            left: 28,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                Transform.translate(
                  offset: const Offset(0, -6),
                  child: Container(
                    width: 56,
                    height: 56,
                    padding: const EdgeInsets.all(10),
                    decoration: BoxDecoration(
                      color: Colors.white.withValues(alpha: 0.30),
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(
                        color: Colors.white.withValues(alpha: 0.22),
                        width: 1,
                      ),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withValues(alpha: 0.08),
                          blurRadius: 10,
                          offset: const Offset(0, 4),
                        ),
                      ],
                    ),
                    child: Image.asset(
                      'assets/image/LOGO_TRANSPARAN.png',
                      fit: BoxFit.contain,
                    ),
                  ),
                ),
                const SizedBox(height: 10),
                const Text(
                  'Campus Care',
                  style: TextStyle(
                    fontSize: 26,
                    fontWeight: FontWeight.w800,
                    color: Colors.white,
                    letterSpacing: -0.8,
                    height: 1.1,
                  ),
                ),
                const SizedBox(height: 5),
                Text(
                  'FACILITY REPORT SYSTEM',
                  style: TextStyle(
                    fontSize: 10.5,
                    fontWeight: FontWeight.w600,
                    color: Colors.white.withValues(alpha: 0.62),
                    letterSpacing: 1.2,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  void _goToForgotPassword() {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => const ForgotPasswordScreen(),
      ),
    );
  }

  void _goToRegister() {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => const RegisterScreen(),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SingleChildScrollView(
        keyboardDismissBehavior: ScrollViewKeyboardDismissBehavior.onDrag,
        child: Column(
          children: [
            _buildHero(),
            FadeTransition(
              opacity: _cardFade,
              child: SlideTransition(
                position: _cardSlide,
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(26, 28, 26, 32),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      _buildInput(
                        label: 'Email',
                        controller: _emailController,
                        hint: 'Masukkan email',
                        icon: Icons.mail_outline_rounded,
                      ),
                      const SizedBox(height: 16),
                      _buildInput(
                        label: 'Kata Sandi',
                        controller: _passwordController,
                        hint: 'Masukkan kata sandi',
                        icon: Icons.lock_outline_rounded,
                        isPassword: true,
                      ),
                      Align(
                        alignment: Alignment.centerRight,
                        child: TextButton(
                          onPressed: _isLoading ? null : _goToForgotPassword,
                          style: TextButton.styleFrom(
                            padding: const EdgeInsets.only(top: 8),
                            minimumSize: Size.zero,
                            tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                          ),
                          child: const Text(
                            'Lupa kata sandi?',
                            style: TextStyle(
                              fontSize: 12.5,
                              fontWeight: FontWeight.w700,
                              color: _red,
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(height: 20),
                      SizedBox(
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
                            onPressed: _isLoading ? null : _login,
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
                                        'Masuk',
                                        style: TextStyle(
                                          fontSize: 14,
                                          fontWeight: FontWeight.w700,
                                          color: Colors.white,
                                          letterSpacing: 0.2,
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
                      const SizedBox(height: 24),
                      const Row(
                        children: [
                          Expanded(
                            child: Divider(
                              color: _borderColor,
                              thickness: 1,
                            ),
                          ),
                          Padding(
                            padding: EdgeInsets.symmetric(horizontal: 14),
                            child: Text(
                              'atau lanjutkan dengan',
                              style: TextStyle(
                                color: Color(0xFFCCCCCC),
                                fontSize: 11.5,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ),
                          Expanded(
                            child: Divider(
                              color: _borderColor,
                              thickness: 1,
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 20),
                      SizedBox(
                        width: double.infinity,
                        height: 50,
                        child: OutlinedButton(
                          onPressed: _isLoading ? null : _loginGoogle,
                          style: OutlinedButton.styleFrom(
                            backgroundColor: Colors.white,
                            elevation: 0,
                            side: const BorderSide(
                              color: _borderColor,
                              width: 1.5,
                            ),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(13),
                            ),
                          ),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Image.asset(
                                'assets/image/GUGEL.png',
                                width: 22,
                                height: 22,
                                fit: BoxFit.contain,
                              ),
                              const SizedBox(width: 10),
                              const Text(
                                'Masuk dengan Google',
                                style: TextStyle(
                                  color: Color(0xFF222222),
                                  fontSize: 13.5,
                                  fontWeight: FontWeight.w600,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                      const SizedBox(height: 32),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          const Text(
                            'Belum punya akun? ',
                            style: TextStyle(
                              color: _textMuted,
                              fontSize: 13,
                            ),
                          ),
                          GestureDetector(
                            onTap: _isLoading ? null : _goToRegister,
                            child: const Text(
                              'Daftar di sini',
                              style: TextStyle(
                                color: _red,
                                fontSize: 13,
                                fontWeight: FontWeight.w800,
                              ),
                            ),
                          ),
                        ],
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