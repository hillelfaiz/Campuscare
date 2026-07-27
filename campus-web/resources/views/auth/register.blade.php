<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Campus Care</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        body, html { height: 100%; margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        .login-left {
            background-color: #121212;
            background-image: 
                radial-gradient(circle at 30% 40%, rgba(150, 0, 0, 0.4) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(100, 0, 0, 0.3) 0%, transparent 40%),
                radial-gradient(circle at 70% 80%, rgba(200, 200, 200, 0.15) 0%, transparent 40%);
            color: white; position: relative; overflow: hidden;
        }

        .brand-logo-text { font-size: 6rem; font-weight: 900; color: #cc0000; line-height: 1; margin-bottom: 10px; }
        .brand-logo-text span { color: #888; display: block; margin-top: -20px; }
        .title-welcome { font-size: 1.2rem; font-weight: normal; margin-top: 30px; }
        .title-sub { font-size: 1rem; color: #ddd; max-width: 80%; }
        
        .login-right { background-color: #ffffff; }
        .login-title { color: #cc0000; font-weight: bold; font-size: 1.1rem; margin-bottom: 20px; }
        
        .form-control-custom { border: none; border-bottom: 1px solid #ccc; border-radius: 0; padding: 10px 0; font-size: 0.9rem; }
        .form-control-custom:focus { box-shadow: none; border-bottom: 2px solid #cc0000; background-color: transparent; }
        .input-group-text-custom { background: transparent; border: none; border-bottom: 1px solid #ccc; border-radius: 0; color: #666; }
        
        .btn-register { background-color: #cc0000; color: white; border: none; border-radius: 6px; padding: 12px; font-weight: 600; transition: 0.2s;}
        .btn-register:hover { background-color: #a00000; color: white; }

        /* Tombol Google */
        .btn-google { border: 1px solid #ddd; background: white; color: #444; border-radius: 6px; padding: 10px; font-weight: 600; display: flex; align-items: center; justify-content: center; transition: 0.2s; text-decoration: none; font-size: 0.9rem; }
        .btn-google:hover { background: #f8f9fa; border-color: #ccc; color: #444; }
        .btn-google img { width: 20px; margin-right: 10px; }
        
        /* Garis Pemisah (Atau) */
        .divider-text { display: flex; align-items: center; text-align: center; margin: 20px 0; color: #aaa; font-size: 0.85rem; }
        .divider-text::before, .divider-text::after { content: ''; flex: 1; border-bottom: 1px solid #eee; }
        .divider-text:not(:empty)::before { margin-right: .5em; }
        .divider-text:not(:empty)::after { margin-left: .5em; }

        .btn-outline-custom { border: 1px solid #ddd; color: #666; border-radius: 6px; padding: 10px; font-weight: 600; text-decoration: none; display: block; text-align: center; transition: 0.2s; font-size: 0.9rem; }
        .btn-outline-custom:hover { background: #f8f9fa; color: #333; }
    </style>
</head>
<body>

    <div class="container-fluid h-100 p-0">
        <div class="row g-0 h-100">
            
            <div class="col-lg-7 col-md-6 d-none d-md-flex flex-column justify-content-center p-5 login-left">
                <div class="ms-lg-5">
                    <div class="brand-logo-text">Campus<span>Care</span></div>
                    <div class="title-welcome">Registrasi Akun Baru</div>
                    <div class="title-sub">Bergabunglah untuk mulai menggunakan layanan pelaporan barang hilang dan pelaporan kerusakan fasilitas kampus.</div>
                    <div class="mt-5 pt-5" style="font-size: 0.8rem; color: #888;">
                        Powered by: <strong class="text-white">Kelompok 1</strong>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-md-6 d-flex flex-column justify-content-center p-5 login-right">
                <div class="w-100 mx-auto" style="max-width: 380px;">
                    
                    @if($errors->any())
                        <div class="alert alert-danger p-2 small"><i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}</div>
                    @endif

                    <div class="login-title">Daftar Akun Baru</div>

                    <form action="/register" method="POST">
                        @csrf
                        <div class="mb-3 input-group">
                            <input type="text" name="name" class="form-control form-control-custom" placeholder="Nama Lengkap" required value="{{ old('name') }}">
                            <span class="input-group-text input-group-text-custom"><i class="fa-solid fa-user"></i></span>
                        </div>

                        <div class="mb-3 input-group">
                            <input type="text" name="nim" class="form-control form-control-custom" placeholder="NIM/NIK">
                            <span class="input-group-text input-group-text-custom"><i class="fa-solid fa-id-card"></i></span>
                        </div>

                        <div class="mb-3 input-group">
                            <input type="email" name="email" class="form-control form-control-custom" placeholder="Email" required value="{{ old('email') }}">
                            <span class="input-group-text input-group-text-custom"><i class="fa-solid fa-envelope"></i></span>
                        </div>

                        <div class="mb-3 input-group">
                            <input type="password" name="password" class="form-control form-control-custom" placeholder="Password" required>
                            <span class="input-group-text input-group-text-custom"><i class="fa-solid fa-lock"></i></span>
                        </div>

                        <div class="mb-4 input-group">
                            <input type="password" name="password_confirmation" class="form-control form-control-custom" placeholder="Konfirmasi Password" required>
                            <span class="input-group-text input-group-text-custom"><i class="fa-solid fa-shield-check"></i></span>
                        </div>

                        <div class="d-grid mb-2">
                            <button type="submit" class="btn btn-register shadow-sm">Daftar Sekarang</button>
                        </div>
                    </form>

                    <div class="divider-text">ATAU</div>

                    <div class="d-grid mb-4">
                        <a href="/auth/google" class="btn btn-google shadow-sm">
                            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google Logo">
                            Daftar dengan Google
                        </a>
                    </div>

                    <div class="mt-4 text-center">
                        <span class="text-muted small">Sudah memiliki akun?</span>
                        <a href="/login" class="btn btn-outline-custom mt-2">Kembali ke Halaman Login</a>
                    </div>

                </div>
            </div>

        </div>
    </div>

</body>
</html>