<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPDB MI Ziyadatul Ihsan - Reset Password</title>
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
                <p class="description">Sepenuh hati berdedikasi untuk mewujudkan generasi yang cerdas secara akademik, mulia dalam akhlak, dan teguh dalam iman.</p>
            </div>
        </section>
        <section class="auth-content">
            <div class="form-card">
                <div class="form-header">
                    <h2>Reset Password</h2>
                    <p>Silakan masukkan password baru Anda.</p>
                </div>

                <form id="resetForm" class="auth-form">
                    <input type="hidden" name="token" id="token" value="{{ request()->query('token') ?? request()->token }}">
                    <input type="hidden" name="email" id="email" value="{{ request()->query('email') }}">

                    <div class="input-group">
                        <i class="fi fi-rr-lock"></i>
                        <input type="password" name="password" id="password" placeholder="Password Baru" required>
                    </div>
                    <div class="input-group">
                        <i class="fi fi-rr-lock"></i>
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Konfirmasi Password Baru" required>
                    </div>

                    <button type="submit" class="btn-primary">RESET PASSWORD</button>
                </form>

                <div class="login-option">
                    <a href="{{ url('/login') }}">Kembali ke Login</a>
                </div>

                <div id="message" style="margin-top: 20px; text-align: center;"></div>
            </div>
        </section>
    </main>

    <script>
        document.getElementById('resetForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const token = document.getElementById('token').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;
            const messageDiv = document.getElementById('message');

            if (!token || !email) {
                messageDiv.style.color = 'red';
                messageDiv.innerText = 'Tautan reset password tidak valid.';
                return;
            }

            try {
                const response = await fetch('/api/reset-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        token: token,
                        email: email,
                        password: password,
                        password_confirmation: passwordConfirmation
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    messageDiv.style.color = 'green';
                    messageDiv.innerText = 'Password berhasil direset! Silakan login.';
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 2000);
                } else {
                    messageDiv.style.color = 'red';
                    messageDiv.innerText = data.message || 'Gagal mereset password. Periksa kembali data Anda.';
                }
            } catch (error) {
                console.error('Error:', error);
                messageDiv.style.color = 'red';
                messageDiv.innerText = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
            }
        });
    </script>
</body>
</html>