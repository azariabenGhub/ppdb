<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memproses Login Google - PPDB MI Ziyadatul Ihsan</title>
    <link rel="stylesheet" href="{{ asset('storage/css/style.css') }}">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/fi-regular-rounded.css'>
    <style>
        /* tambahan kecil untuk loading spinner dan layout */
        .loading-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f4f7f6;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #e0e0e0;
            border-top: 4px solid #1a4d2e;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .callback-card {
            text-align: center;
            padding: 40px;
            max-width: 450px;
            width: 90%;
            margin: 0 auto;
        }
        .callback-icon {
            font-size: 48px;
            color: #1a4d2e;
            margin-bottom: 20px;
        }
        .callback-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1a4d2e;
            margin-bottom: 12px;
        }
        .callback-message {
            color: #555;
            font-size: 0.9rem;
            margin-bottom: 25px;
        }
    </style>
</head>
<body>
    <main class="loading-container">
        <div class="form-card callback-card">
            <div class="callback-icon">
                <i class="fi fi-rr-user"></i>
            </div>
            <div class="callback-title">
                Memproses Login Google
            </div>
            <div class="callback-message">
                Silakan tunggu, kami sedang mengarahkan Anda ke dashboard.
            </div>
            <div class="spinner"></div>
            <div class="info-msg" style="margin-top: 20px;">
                <span>&#9432;</span> Jangan tutup halaman ini.
            </div>
        </div>
    </main>

    <script>
        // Ambil data dari query string yang dikirim dari controller
        const urlParams = new URLSearchParams(window.location.search);
        const token = urlParams.get('token');
        const userEncoded = urlParams.get('user');

        if (token && userEncoded) {
            try {
                const user = JSON.parse(atob(userEncoded));
                // Simpan ke localStorage
                localStorage.setItem('access_token', token);
                localStorage.setItem('user', JSON.stringify(user));

                // Redirect berdasarkan role
                const role = user.role;
                if (role === 'panitia' || role === 'kepala_sekolah' || role === 'bendahara') {
                    window.location.href = '/staff-dashboard';
                } else {
                    window.location.href = '/dashboard';
                }
            } catch (err) {
                console.error('Error parsing user data:', err);
                // Fallback: redirect ke login dengan pesan error
                alert('Terjadi kesalahan saat memproses data login. Silakan coba lagi.');
                window.location.href = '/login';
            }
        } else {
            // Jika tidak ada token atau user, redirect ke login
            alert('Data login tidak lengkap. Silakan login kembali.');
            window.location.href = '/login';
        }
    </script>
</body>
</html>