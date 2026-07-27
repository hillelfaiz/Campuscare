import 'package:flutter/material.dart';

class AppTheme {
  // Warna Merah khas Telkom University
  static const Color primaryRed = Color(0xFFED1C24);
  
  // Kita ubah warnanya menjadi terang agar cocok untuk background Card/Input
  // (Nama variabel tetap dipertahankan agar tidak error di file lain)
  static const Color surfaceDark = Color(0xFFF8F9FA); 

  static final ThemeData lightTheme = ThemeData(
    brightness: Brightness.light,
    primaryColor: primaryRed,
    scaffoldBackgroundColor: Colors.white, // Latar utama menjadi putih
    appBarTheme: const AppBarTheme(
      backgroundColor: primaryRed,
      foregroundColor: Colors.white,
      elevation: 0,
    ),
    colorScheme: const ColorScheme.light(
      primary: primaryRed,
      secondary: primaryRed,
      surface: Colors.white,
    ),
    elevatedButtonTheme: ElevatedButtonThemeData(
      style: ElevatedButton.styleFrom(
        backgroundColor: primaryRed,
        foregroundColor: Colors.white,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(8),
        ),
      ),
    ),
    textButtonTheme: TextButtonThemeData(
      style: TextButton.styleFrom(foregroundColor: primaryRed),
    ),
  );
}