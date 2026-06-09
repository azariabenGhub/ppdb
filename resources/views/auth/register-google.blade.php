<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPDB MI Ziyadatul Ihsan - Buat Akun</title>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/fi-regular-rounded.css'>
    @vite('resources/css/style.css')
    @include('partials.alert-helper')
</head>

<body>
    <main class="auth-container">
        <section class="auth-sidebar">
            <img src="{{ asset('storage/assets/logo-mizi.png') }}" alt="Logo MI Ziyadatul Ihsan" class="auth-logo">
            <div class="auth-text">
                <p class="welcome-text">Selamat Datang di</p>
                <h1 class="main-title">Portal PPDB MI Ziyadatul Ihsan</h1>
                <p class="description">Sepenuh hati berdedikasi untuk mewujudkan generasi yang cerdas secara akademik,
                    mulia dalam akhlak, dan teguh dalam iman.</p>
            </div>
        </section>
        <section class="auth-content">
            <div class="form-card">
                <div class="form-header">
                    <h2>Buat Akun</h2>
                    <p>Silahkan isi nama dan email anda terlebih dahulu.</p>
                </div>

                <form action="{{ route('register.google.store') }}" method="POST" class="auth-form">
                    @csrf
                    <div class="input-group">
                        <label for="name">Nama</label>
                        <input type="text" name="name" id="name" placeholder="Nama Lengkap" value="{{ old('name') }}"
                            required>
                    </div>
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" placeholder="Email Aktif" value="{{ old('email') }}"
                            required>
                    </div>

                    <div class="info-msg">
                        <span>&#9432;</span> Pastikan email yang anda masukkan benar
                    </div>

                    <button type="submit" class="btn-primary">DAFTAR</button>
                    <div class="login-option">
                        <a href="/login">Sudah punya akun?</a>
                    </div>

                    <div class="auth-divider">
                        <span class="line"></span>
                        <span class="text">atau</span>
                        <span class="line"></span>
                    </div>

                    <button type="button" class="btn-google"
                        onclick="window.location.href='{{ route('google.login') }}'">
                        <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" alt="Google">
                        Daftar dengan Google
                    </button>
                </form>

                <div class="captcha-terms">
                    Situs ini dilindungi oleh reCAPTCHA dan berlaku Kebijakan Privasi Google serta <a href="#">Kebijakan
                        Privasi</a> Kami.
                </div>
            </div>
        </section>
    </main>
</body>

</html>