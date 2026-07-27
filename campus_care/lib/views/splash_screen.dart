import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../main.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  static const _red = Color(0xFFE4002B);
  static const _redDark = Color(0xFFA8001F);
  static const _redDeep = Color(0xFF2A000A);

  @override
  void initState() {
    super.initState();

    SystemChrome.setSystemUIOverlayStyle(const SystemUiOverlayStyle(
      statusBarColor: Colors.transparent,
      statusBarIconBrightness: Brightness.light,
    ));

    Timer(const Duration(seconds: 2), () {
      if (!mounted) return;

      Navigator.pushReplacement(
        context,
        MaterialPageRoute(
          builder: (_) => const AuthWrapper(),
        ),
      );
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Image.asset(
              'assets/image/LOGO_TRANSPARAN.png',
              width: 125,
              height: 125,
              fit: BoxFit.contain,
            ),
            const SizedBox(height: 22),
            const Text(
              'Campus Care',
              style: TextStyle(
                color: Color(0xFF111111),
                fontSize: 28,
                fontWeight: FontWeight.w800,
                letterSpacing: -0.8,
              ),
            ),
            const SizedBox(height: 6),
            const Text(
              'FACILITY REPORT SYSTEM',
              style: TextStyle(
                color: Color(0xFF888888),
                fontSize: 12,
                fontWeight: FontWeight.w600,
                letterSpacing: 1.1,
              ),
            ),
            const SizedBox(height: 36),
            const SizedBox(
              width: 26,
              height: 26,
              child: CircularProgressIndicator(
                color: Color(0xFFE4002B),
                strokeWidth: 2.4,
              ),
            ),
          ],
        ),
      ),
    );
  }
}