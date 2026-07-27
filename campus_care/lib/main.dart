import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'core/thema.dart';
import 'providers/auth_provider.dart';
import 'views/splash_screen.dart';
import 'providers/facility_provider.dart';
import 'views/dashboard/mahasiswa_dashboard.dart';
import 'views/dashboard/satpam_dashboard.dart';
import 'views/dashboard/admin_dashboard.dart';
import 'views/auth/login_screen.dart';
import 'views/auth/google_complete_profile_screen.dart';

void main() {
  runApp(
    MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider()),
        ChangeNotifierProvider(create: (_) => FacilityProvider()),
      ],
      child: const CampusCareApp(),
    ),
  );
}

class CampusCareApp extends StatelessWidget {
  const CampusCareApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Campus Care',
      theme: AppTheme.lightTheme,
      // FIX: Hapus named routes, gunakan home: AuthWrapper saja.
      // Semua navigasi pakai Navigator.pushAndRemoveUntil langsung ke widget.
      home: const SplashScreen(),
      debugShowCheckedModeBanner: false,
    );
  }
}

/// Router pintar: cek token → cek role → cek apakah Google user perlu lengkapi profil
class AuthWrapper extends StatefulWidget {
  const AuthWrapper({super.key});

  @override
  State<AuthWrapper> createState() => _AuthWrapperState();
}

class _AuthWrapperState extends State<AuthWrapper> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<AuthProvider>(context, listen: false).checkAuth();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<AuthProvider>(
      builder: (context, auth, _) {
        // ── Loading saat cek token ──
        if (auth.isLoading) {
          return const Scaffold(
            body: Center(
              child: CircularProgressIndicator(color: AppTheme.primaryRed),
            ),
          );
        }

        // ── Belum login ──
        if (auth.currentUser == null) {
          return const LoginScreen();
        }

        // ── FIX Google login: user baru via Google belum punya NIM → isi profil dulu ──
        // Tandai dengan flag needsProfileCompletion di AuthProvider
        if (auth.needsProfileCompletion) {
          return const GoogleCompleteProfileScreen();
        }

        // ── Routing berdasarkan role ──
        switch (auth.currentUser!.role) {
          case 'satpam':
            return const SatpamDashboard();
          case 'admin':
            return const AdminDashboard();
          default: // mahasiswa
            return const MahasiswaDashboard();
        }
      },
    );
  }
}