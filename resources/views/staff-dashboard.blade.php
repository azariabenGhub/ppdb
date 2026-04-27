<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Staff PPDB</title>
</head>
<body>
    <h1>Dashboard Staf - <span id="userName"></span></h1>
    <p>Anda login sebagai <strong id="userRole"></strong>.</p>

    <!-- Navigasi khusus staff (akan dikembangkan nanti) -->
    <div>
        <button data-section="beranda-staff">Beranda</button>
        <button data-section="verifikasi">Verifikasi Pendaftar</button>
        <button data-section="kelola-jadwal">Kelola Jadwal</button>
        <button data-section="laporan">Laporan</button>
    </div>
    <hr>

    <!-- Konten statis sementara -->
    <div id="beranda-staff" class="section">
        <p>Ini Beranda Staff</p>
    </div>
    <div id="verifikasi" class="section" style="display:none;">
        <p>Verifikasi Pendaftar</p>
    </div>
    <div id="kelola-jadwal" class="section" style="display:none;">
        <p>Kelola Jadwal</p>
    </div>
    <div id="laporan" class="section" style="display:none;">
        <p>Laporan</p>
    </div>

    <hr>
    <button id="logoutButton">Logout</button>
    <div id="message"></div>

    <script>
        // ======= AKSES KONTROL: hanya staff yang boleh =======
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        const token = localStorage.getItem('access_token');

        // Redirect jika tidak login atau bukan staff
        if (!token || (user.role !== 'panitia' && user.role !== 'kepala_sekolah')) {
            window.location.href = '/login';
        }

        // Tampilkan info user
        document.getElementById('userName').innerText = user.name || 'Pengguna';
        document.getElementById('userRole').innerText = user.role === 'kepala_sekolah' ? 'Kepala Sekolah' : 'Panitia PPDB';

        // Navigasi antar section
        document.querySelectorAll('button[data-section]').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-section');
                document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
                document.getElementById(targetId).style.display = 'block';
            });
        });

        // Logout
        document.getElementById('logoutButton').addEventListener('click', async function() {
            if (!token) { window.location.href = '/login'; return; }
            try {
                const response = await fetch('/api/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
                if (response.ok) {
                    localStorage.removeItem('access_token');
                    localStorage.removeItem('user');
                    window.location.href = '/login';
                }
            } catch (error) {
                document.getElementById('message').innerText = 'Gagal logout.';
            }
        });
    </script>
</body>
</html>