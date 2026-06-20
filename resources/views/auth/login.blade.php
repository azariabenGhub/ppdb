<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPDB MI Ziyadatul Ihsan - Login</title>
    @vite('resources/css/style.css')
    @include('partials.alert-helper')
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
    <!-- <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/fi-regular-rounded.css'> -->
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
                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                <div class="form-header">
                    <h2>Login</h2>
                    <p>Silahkan isi form berikut untuk masuk ke akun anda.</p>
                </div>

                @if(session('errors'))
                    <div class="alert alert-danger">
                        {{ session('errors')->first('email') ?? session('errors')->first('google') }}
                    </div>
                @endif

                <form id="loginForm" class="auth-form">
                    <div class="input-group">
                        <i class="fi fi-rr-envelope"></i>
                        <input type="email" name="email" id="email" placeholder="Masukkan Alamat Email" required>
                    </div>
                    <div class="input-group">
                        <i class="fi fi-rr-lock"></i>
                        <input type="password" name="password" id="password" placeholder="Masukkan Kata Sandi" required>
                    </div>

                    <button type="submit" class="btn-primary">MASUK</button>
                </form>

                <div class="login-option">
                    <a href="{{ route('register.google') }}">Belum punya akun?</a>
                    <a href="/forgot-password">Lupa Kata Sandi?</a>
                </div>

                <div class="auth-divider">
                    <span class="line"></span>
                    <span class="text">atau</span>
                    <span class="line"></span>
                </div>

                <button type="button" class="btn-google" onclick="window.location.href='{{ route('google.login') }}'">
                    <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" alt="Google">
                    Masuk dengan Google
                </button>

                <div id="message"></div>
            </div>
        </section>
    </main>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            // Tampilkan loading atau disable button sementara
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerText = 'Memproses...';

            grecaptcha.ready(function () {
                grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', { action: 'login' }).then(async function (token) {
                    document.getElementById('g-recaptcha-response').value = token;

                    const formData = {
                        email: document.getElementById('email').value,
                        password: document.getElementById('password').value,
                        'g-recaptcha-response': token
                    };

                    try {
                        const response = await fetch('/api/login', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
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
                        } else {
                            let msg = data.message || 'Login gagal';
                            if (response.status === 403) {
                                msg += ' <a href="#" id="resend-verification-link" style="color: #856404; text-decoration: underline; font-weight: bold; margin-left: 5px;">Kirim ulang email verifikasi</a>';
                            }
                            document.getElementById('message').innerHTML = '<div style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 12px; border-radius: 6px; margin-top: 10px; font-size: 14px;">' + msg + '</div>';
                            
                            const resendLink = document.getElementById('resend-verification-link');
                            if (resendLink) {
                                resendLink.addEventListener('click', async (event) => {
                                    event.preventDefault();
                                    resendLink.innerText = 'Mengirim...';
                                    try {
                                        const resendRes = await fetch('/api/email/resend', {
                                            method: 'POST',
                                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                                            body: JSON.stringify({ email: document.getElementById('email').value })
                                        });
                                        const resendData = await resendRes.json();
                                        if (resendRes.ok) {
                                            document.getElementById('message').innerHTML = '<div style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 12px; border-radius: 6px; margin-top: 10px; font-size: 14px;">' + resendData.message + '</div>';
                                        } else {
                                            document.getElementById('message').innerHTML = '<div style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 12px; border-radius: 6px; margin-top: 10px; font-size: 14px;">' + resendData.message + '</div>';
                                        }
                                    } catch (err) {
                                        console.error(err);
                                        document.getElementById('message').innerHTML = '<div style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 12px; border-radius: 6px; margin-top: 10px; font-size: 14px;">Gagal mengirim ulang email verifikasi.</div>';
                                    }
                                });
                            }
                            
                            submitBtn.disabled = false;
                            submitBtn.innerText = 'MASUK';
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        document.getElementById('message').innerHTML = '<div style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 12px; border-radius: 6px; margin-top: 10px; font-size: 14px;">Terjadi kesalahan jaringan.</div>';
                        submitBtn.disabled = false;
                        submitBtn.innerText = 'MASUK';
                    }
                });
            });
        });

        // Tampilkan pesan jika diarahkan dari verifikasi email
        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const verified = urlParams.get('verified');
            const error = urlParams.get('error');
            const messageDiv = document.getElementById('message');

            if (verified === 'success') {
                messageDiv.innerHTML = '<div style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 12px; border-radius: 6px; margin-top: 10px; font-size: 14px;">Email Anda berhasil diverifikasi! Silakan masuk.</div>';
            } else if (verified === 'already') {
                messageDiv.innerHTML = '<div style="color: #0c5460; background-color: #d1ecf1; border-color: #bee5eb; padding: 12px; border-radius: 6px; margin-top: 10px; font-size: 14px;">Email Anda sudah terverifikasi sebelumnya. Silakan masuk.</div>';
            } else if (error === 'invalid') {
                messageDiv.innerHTML = '<div style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 12px; border-radius: 6px; margin-top: 10px; font-size: 14px;">Link verifikasi tidak valid atau kedaluwarsa.</div>';
            }
        });
    </script>
</body>

</html>