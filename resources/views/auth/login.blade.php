<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPDB MI Ziyadatul Ihsan - Login</title>
    <link rel="stylesheet" href="{{ asset('storage/css/style.css') }}">
    <!-- <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/fi-regular-rounded.css'> -->
</head>
<body>
    <main class="auth-container">
        <section class="auth-sidebar">
            <img src="{{ asset('storage/assets/logo-mizi.png') }}" alt="Logo MI Ziyadatul Ihsan" class="auth-logo">
            <div class="auth-text">
                <p class="welcome-text">Selamat Datang di</p>
                <h1 class="main-title">Portal PPDB MI Ziyadatul Ihsan</h1>
                <p class="description">Sepenuh hati berdedikasi untuk mewujudkan generasi yang cerdas secara akademik, mulia dalam akhlak, dan teguh dalam iman.</p>
            </div>
        </section>
        <section class="auth-content">
            <div class="form-card">
                <div class="form-header">
                    <h2>Login</h2>
                    <p>Silahkan isi form berikut untuk masuk ke akun anda.</p>
                </div>

                <form id="loginForm" class="auth-form">
                    <div class="input-wrapper">
                        <i class="fi fi-rr-envelope"></i>
                        <input type="email" name="email" id="email" placeholder="Masukkan Alamat Email" required>
                    </div>
                    <div class="input-wrapper">
                        <i class="fi fi-rr-lock"></i>
                        <input type="password" name="password" id="password" placeholder="Masukkan Kata Sandi" required>
                    </div>

                    <button type="submit" class="btn-primary">MASUK</button>
                </form>

                <div class="login-option">
                    <a href="{{ url('/register') }}">Belum punya akun?</a>
                    <a href="#">Lupa Kata Sandi?</a>
                </div>

                <div id="message"></div>
            </div>
        </section>
    </main>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value
            };

            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (response.ok) {
                    localStorage.setItem('access_token', data.access_token);
                    localStorage.setItem('user', JSON.stringify(data.user));

                    const role = data.user.role;
                    if (role === 'panitia' || role === 'kepala_sekolah' || role === 'bendahara') {
                        window.location.href = '/staff-dashboard';
                    } else {
                        window.location.href = '/dashboard';
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('message').innerText = 'Terjadi kesalahan jaringan.';
            }
        });
    </script>
</body>
</html>