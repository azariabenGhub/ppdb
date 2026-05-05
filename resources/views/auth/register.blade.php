<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPDB MI Ziyadatul Ihsan - Buat Akun</title>
    <link rel="stylesheet" href="{{ asset('storage/css/style.css') }}">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/fi-regular-rounded.css'>
</head>

<body>
    <main class="auth-container">
        <section class="auth-sidebar">
            <img src="{{ asset('storage/assets/logo-mizi.png') }}" alt="Logo MI Ziyadatul Ihsan" class="auth-logo">
            <div class="auth-text">
                <p class="welcome-text">Selamat Datang di</p>
                <h1 class="main-title">Portal PPDB MI Ziyadatul Ihsan</h1>
                <p class="description">Sepenuh hati berdedikasi untuk mewujudkan generasi yang cerdas secara akademik,
                    mulia dalam akhlak, dan teguh dalam iman.
                </p>
            </div>
            </p>
            </div>
        </section>
        <section class="auth-content">
            <div class="form-card">
                <div class="form-header">
                    <h2>Buat Akun</h2>
                    <p>Silahkan isi form berikut untuk mendaftarkan akun anda.</p>
                </div>

                <form id="registerForm" action="" class="auth-form">
                    <div class="input-group">
                        <label for="nama">Nama</label>
                        <input type="text" name="nama" id="name" placeholder="Nama Lengkap">
                    </div>
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" placeholder="Email Aktif">
                    </div>
                    <div class="input-group">
                        <label for="password">Kata Sandi</label>
                        <input type="password" name="password" id="password" placeholder="Masukkan Kata Sandi">
                    </div>
                    <div class="input-group">
                        <label for="confirm-password">Konfirmasi Kata Sandi</label>
                        <input type="password" name="confirm-password" id="password_confirmation"
                            placeholder="Masukkan Kata Sandi">
                    </div>

                    <div class="info-pw">
                        <p>Ketentuan Kata Sandi:</p>
                        <ul>
                            <li>Minimal 8 Karakter</li>
                            <li>Minimal 1 huruf kapital</li>
                            <li>Minimal 1 huruf kecil</li>
                            <li>Minimal 1 angka</li>
                        </ul>
                    </div>

                    <button type="submit" class="btn-primary">SIMPAN</button>

                </form>

                <div class="login-option">
                    <p>Sudah punya akun? <a href="/login">Login di sini</a></p>
                </div>

                <div class="captcha-terms">
                    Situs ini dilindungi oleh reCAPTCHA dan berlaku Kebijakan Privasi Google serta <a href="#">Kebijakan
                        Privasi</a> Kami.
                </div>
            </div>
        </section>
    </main>

    <script>
        document.getElementById('registerForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                password_confirmation: document.getElementById('password_confirmation').value
            };

            try {
                const response = await fetch('/api/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (response.ok) {
                    // Simpan token ke localStorage
                    localStorage.setItem('access_token', data.access_token);
                    localStorage.setItem('user', JSON.stringify(data.user));

                    // Redirect ke dashboard
                    window.location.href = '/dashboard';
                } else {
                    // Tampilkan pesan error (misal validasi)
                    document.getElementById('message').innerText = JSON.stringify(data);
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('message').innerText = 'Terjadi kesalahan jaringan.';
            }
        });
    </script>
</body>
<!-- <body>
    <h1>Register</h1>

    <form id="registerForm">
        <div>
            <label>Nama:</label>
            <input type="text" name="name" id="name" required>
        </div>
        <div>
            <label>Email:</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div>
            <label>Password:</label>
            <input type="password" name="password" id="password" required minlength="8">
        </div>
        <div>
            <label>Konfirmasi Password:</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required>
        </div>
        <button type="submit">Daftar</button>
    </form>

    <p>Sudah punya akun? <a href="/login">Login di sini</a></p>

    <div id="message"></div>
</body> -->

</html>