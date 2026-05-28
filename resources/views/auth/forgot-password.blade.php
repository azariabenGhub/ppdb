<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPDB MI Ziyadatul Ihsan - Lupa Password</title>
    @vite('resources/css/style.css')
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
                    <h2>Lupa Password</h2>
                    <p>Masukkan alamat email Anda, kami akan mengirimkan tautan untuk mereset password.</p>
                </div>

                <form id="forgotForm" class="auth-form">
                    <div class="input-group">
                        <i class="fi fi-rr-envelope"></i>
                        <input type="email" name="email" id="email" placeholder="Masukkan Alamat Email" required>
                    </div>

                    <button type="submit" class="btn-primary">KIRIM TAUTAN RESET</button>
                </form>

                <div class="login-option">
                    <a href="{{ url('/login') }}">Kembali ke Login</a>
                    <a href="{{ url('/register-google') }}">Belum punya akun?</a>
                </div>

                <div id="message" style="margin-top: 20px; text-align: center;"></div>
            </div>
        </section>
    </main>

    <script>
        document.getElementById('forgotForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const messageDiv = document.getElementById('message');

            try {
                const response = await fetch('/api/forgot-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email })
                });

                const data = await response.json();

                if (response.ok) {
                    messageDiv.style.color = 'green';
                    messageDiv.innerText = data.message || 'Tautan reset password telah dikirim ke email Anda.';
                    document.getElementById('forgotForm').reset();
                } else {
                    messageDiv.style.color = 'red';
                    messageDiv.innerText = data.message || 'Email tidak ditemukan.';
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