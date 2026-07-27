<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lengkapi Profil - Campus Care</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #000; overflow: hidden; font-family: 'Inter', sans-serif; }
        .bg-gradient-custom {
            background: radial-gradient(circle at center, #4a0000 0%, #1a1a1a 100%);
        }
    </style>
</head>
<body class="h-screen flex">
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-custom items-center justify-center relative overflow-hidden">
        <div class="relative z-10 p-12">
            <h1 class="text-7xl font-bold text-red-600 leading-tight">Campus<br><span class="text-gray-400">Care</span></h1>
            <div class="mt-8">
                <h2 class="text-white text-xl font-semibold">Satu Langkah Lagi</h2>
                <p class="text-gray-400 mt-2 text-sm max-w-sm italic">"Pastikan data diri Anda sudah sesuai untuk mempermudah koordinasi layanan fasilitas."</p>
            </div>
            <p class="text-gray-500 text-xs mt-20">Powered by <span class="font-bold text-gray-400">Kelompok 1</span></p>
        </div>
        <div class="absolute -bottom-20 -left-20 w-96 h-96 bg-red-900 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
    </div>

    <div class="w-full lg:w-1/2 bg-white flex items-center justify-center p-8">
        <div class="w-full max-w-md">
            <div class="lg:hidden mb-8 text-center">
                <h1 class="text-4xl font-bold text-red-600 leading-none">Campus <span class="text-gray-400">Care</span></h1>
            </div>

            <div class="mb-10">
                <h2 class="text-2xl font-bold text-gray-800">Lengkapi Profil</h2>
                <p class="text-gray-500 mt-1">Konfirmasi identitas mahasiswa Anda.</p>
            </div>

            <form action="{{ route('auth.store-profile') }}" method="POST" class="space-y-6">
                @csrf
                
                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                        <p class="text-red-700 text-sm font-medium">{{ $errors->first() }}</p>
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Nama Lengkap</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 group-focus-within:text-red-600 transition-colors">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" name="name" value="{{ session('google_name') }}" 
                               class="w-full pl-10 pr-4 py-3 border-b-2 border-gray-100 focus:border-red-600 outline-none transition-all text-gray-800 font-medium" 
                               placeholder="Nama Lengkap Anda" required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">NIM / NIK Mahasiswa</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 group-focus-within:text-red-600 transition-colors">
                            <i class="fa-solid fa-id-card"></i>
                        </span>
                        <input type="text" name="nim" 
                               class="w-full pl-10 pr-4 py-3 border-b-2 border-gray-100 focus:border-red-600 outline-none transition-all text-gray-800 font-medium" 
                               placeholder="Masukkan NIM Anda" required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Buat Password Baru</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 group-focus-within:text-red-600 transition-colors">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" 
                               class="w-full pl-10 pr-4 py-3 border-b-2 border-gray-100 focus:border-red-600 outline-none transition-all text-gray-800" 
                               placeholder="Minimal 6 karakter" required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Konfirmasi Password</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 group-focus-within:text-red-600 transition-colors">
                            <i class="fa-solid fa-shield-check"></i>
                        </span>
                        <input type="password" name="password_confirmation" 
                               class="w-full pl-10 pr-4 py-3 border-b-2 border-gray-100 focus:border-red-600 outline-none transition-all text-gray-800" 
                               placeholder="Ulangi password" required>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" 
                            class="w-full bg-red-600 text-white py-4 rounded-xl font-bold shadow-lg shadow-red-100 hover:bg-red-700 hover:shadow-red-200 hover:-translate-y-0.5 transition-all active:scale-[0.98]">
                        SELESAIKAN PENDAFTARAN
                    </button>
                </div>
            </form>

            <div class="mt-12 text-center">
                <a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-red-600 transition-colors font-medium">
                    <i class="fa-solid fa-arrow-left-long mr-2"></i> Kembali ke Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>