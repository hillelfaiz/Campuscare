import 'package:flutter/material.dart';

enum TopNotificationType {
  success,
  error,
  warning,
  info,
}

class TopNotification {
  static String _cleanMessage(String message) {
    return message
        .replaceAll('Exception:', '')
        .replaceAll('Gagal melakukan login:', '')
        .replaceAll('Gagal memilih foto:', '')
        .replaceAll('Gagal menyimpan:', '')
        .replaceAll('Error:', '')
        .trim();
  }

  static void show(
    BuildContext context, {
    required String message,
    TopNotificationType type = TopNotificationType.info,
    Duration duration = const Duration(seconds: 3),
  }) {
    final overlay = Overlay.of(context);
    late OverlayEntry overlayEntry;

    Color bgColor;
    Color borderColor;
    Color iconBg;
    Color iconColor;
    IconData icon;
    String title;

    switch (type) {
      case TopNotificationType.success:
        bgColor = const Color(0xFFECFDF5);
        borderColor = const Color(0xFFBBF7D0);
        iconBg = const Color(0xFFD1FAE5);
        iconColor = const Color(0xFF10B981);
        icon = Icons.check_circle_rounded;
        title = 'Berhasil';
        break;

      case TopNotificationType.error:
        bgColor = const Color(0xFFFFF1F2);
        borderColor = const Color(0xFFFFCDD5);
        iconBg = const Color(0xFFFFE4E6);
        iconColor = const Color(0xFFE4002B);
        icon = Icons.error_rounded;
        title = 'Gagal';
        break;

      case TopNotificationType.warning:
        bgColor = const Color(0xFFFFFBEB);
        borderColor = const Color(0xFFFDE68A);
        iconBg = const Color(0xFFFEF3C7);
        iconColor = const Color(0xFFF59E0B);
        icon = Icons.warning_rounded;
        title = 'Peringatan';
        break;

      case TopNotificationType.info:
        bgColor = const Color(0xFFEFF6FF);
        borderColor = const Color(0xFFBFDBFE);
        iconBg = const Color(0xFFDBEAFE);
        iconColor = const Color(0xFF3B82F6);
        icon = Icons.info_rounded;
        title = 'Info';
        break;
    }

    final cleanMessage = _cleanMessage(message);

    overlayEntry = OverlayEntry(
      builder: (context) {
        return Positioned(
          top: MediaQuery.of(context).padding.top + 12,
          left: 18,
          right: 18,
          child: Material(
            color: Colors.transparent,
            child: TweenAnimationBuilder<double>(
              tween: Tween(begin: -28, end: 0),
              duration: const Duration(milliseconds: 260),
              curve: Curves.easeOutCubic,
              builder: (context, value, child) {
                return Transform.translate(
                  offset: Offset(0, value),
                  child: child,
                );
              },
              child: Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: bgColor,
                  borderRadius: BorderRadius.circular(18),
                  border: Border.all(
                    color: borderColor,
                    width: 1.2,
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withValues(alpha: 0.10),
                      blurRadius: 18,
                      offset: const Offset(0, 8),
                    ),
                  ],
                ),
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      width: 38,
                      height: 38,
                      decoration: BoxDecoration(
                        color: iconBg,
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Icon(
                        icon,
                        color: iconColor,
                        size: 20,
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Padding(
                        padding: const EdgeInsets.only(top: 1),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              title,
                              style: const TextStyle(
                                color: Color(0xFF111827),
                                fontSize: 13.5,
                                fontWeight: FontWeight.w800,
                              ),
                            ),
                            const SizedBox(height: 3),
                            Text(
                              cleanMessage.isNotEmpty
                                  ? cleanMessage
                                  : 'Terjadi kesalahan. Coba lagi.',
                              style: const TextStyle(
                                color: Color(0xFF6B7280),
                                fontSize: 12.5,
                                fontWeight: FontWeight.w500,
                                height: 1.35,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(width: 8),
                    GestureDetector(
                      onTap: () {
                        if (overlayEntry.mounted) {
                          overlayEntry.remove();
                        }
                      },
                      child: const Icon(
                        Icons.close_rounded,
                        color: Color(0xFF9CA3AF),
                        size: 18,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        );
      },
    );

    overlay.insert(overlayEntry);

    Future.delayed(duration, () {
      if (overlayEntry.mounted) {
        overlayEntry.remove();
      }
    });
  }
}