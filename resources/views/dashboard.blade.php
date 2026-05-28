<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PPDB MI Ziyadatul Ihsan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap">
    @vite('resources/css/style.css')
</head>

<body>

    <!-- ===== HEADER ===== -->
    <header class="main-header">
        <div class="header-brand">
            <img src="{{ asset('storage/assets/logo-mizi.png') }}" alt="Logo" class="logo-mizi">
            <div>
                <strong style="font-size:1rem; color:#1a4d2e; display:block;">MI Ziyadatul Ihsan</strong>
                <span style="font-size:0.75rem; color:#888;">PPDB 2025/2026</span>
            </div>
        </div>
        <div class="header-profile">
            <span id="headerUserName">Nama Calon Siswa</span>
            <div
                style="width:36px;height:36px;border-radius:50%;background:#1a4d2e;display:flex;align-items:center;justify-content:center;">
                <i class="fa-solid fa-user" style="color:white;font-size:1rem;"></i>
            </div>
            <button class="btn-logout" id="logoutButton">
                <i class="fa-solid fa-right-from-bracket"></i> Keluar
            </button>
        </div>
    </header>

    <!-- ===== MAIN WRAPPER ===== -->
    <div class="dashboard-wrapper">

        <!-- ===== SIDEBAR ===== -->
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li class="active" data-section="beranda" href="#" onclick="navigate(event,'beranda')">
                    <a>
                        <i class="fa-solid fa-house"></i> Beranda
                    </a>
                </li>
                <li data-section="formulir" href="#" onclick="navigate(event,'formulir')">
                    <a>
                        <i class="fa-solid fa-file-signature"></i> Formulir Pendaftaran
                    </a>
                </li>
                <li data-section="seleksi" href="#" onclick="navigate(event,'seleksi')">
                    <a>
                        <i class="fa-solid fa-calendar-days"></i> Jadwal Tes
                    </a>
                </li>
                <li data-section="pengumuman" href="#" onclick="navigate(event,'pengumuman')">
                    <a>
                        <i class="fa-solid fa-bullhorn"></i> Pengumuman
                    </a>
                </li>
                <li data-section="daftar-ulang" href="#" onclick="navigate(event,'daftar-ulang')">
                    <a>
                        <i class="fa-solid fa-rotate-right"></i> Daftar Ulang
                    </a>
                </li>
                <li data-section="pembayaran" href="#" onclick="navigate(event,'pembayaran')">
                    <a>
                        <i class="fa-solid fa-credit-card"></i> Pembayaran
                    </a>
                </li>
                <li data-section="status" href="#" onclick="navigate(event,'status')">
                    <a>
                        <i class="fa-solid fa-circle-info"></i> Status Pendaftaran
                    </a>
                </li>
            </ul>
        </aside>

        <!-- ===== CONTENT AREA ===== -->
        <main class="content-area">

            {{-- SECTION: BERANDA --}}
            @include('partials.dashboard.beranda')

            {{-- SECTION: FORMULIR --}}
            @include('partials.dashboard.formulir')

            {{-- SECTION: SELEKSI --}}
            @include('partials.dashboard.seleksi')

            {{-- SECTION: PENGUMUMAN --}}
            @include('partials.dashboard.pengumuman')

            {{-- SECTION: DAFTAR ULANG --}}
            @include('partials.dashboard.daftar-ulang')

            {{-- SECTION: PEMBAYARAN --}}
            @include('partials.dashboard.pembayaran')

            {{-- SECTION: STATUS --}}
            @include('partials.dashboard.status')

        </main><!-- /content-area -->
    </div><!-- /dashboard-wrapper -->

    <div id="message"
        style="position:fixed;bottom:20px;right:20px;background:#1a4d2e;color:white;padding:10px 18px;border-radius:8px;font-size:0.85rem;display:none;z-index:999;">
    </div>

    {{-- Semua script dari partial section dikumpulkan di sini (dieksekusi sebelum script utama) --}}
    @stack('section-scripts')

    {{-- ===== SCRIPT UTAMA ===== --}}
    <script>
        // ========== GLOBAL VARIABLES & HELPERS ==========
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        const token = localStorage.getItem('access_token');

        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            str = String(str); // pastikan string
            return str.replace(/[&<>]/g, function (m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        // Redirect jika belum login atau bukan pendaftar
        if (!token || user.role !== 'pendaftar') {
            window.location.href = '/login';
        }

        // Set nama user di header
        document.getElementById('headerUserName').innerText = user.name || 'Pengguna';

        // ========== NAVIGASI HALAMAN UTAMA ==========
        function navigate(event, sectionName) {
            event.preventDefault();

            // Sembunyikan semua section
            const sections = document.querySelectorAll('.section');
            sections.forEach(section => section.style.display = 'none');

            // Tampilkan section yang dituju
            const target = document.getElementById(sectionName);
            if (target) {
                target.style.display = 'block';
            }

            // Perbarui active state sidebar
            const sidebarItems = document.querySelectorAll('.sidebar-menu li');
            sidebarItems.forEach(item => item.classList.remove('active'));
            const activeItem = document.querySelector(`.sidebar-menu li[data-section="${sectionName}"]`);
            if (activeItem) {
                activeItem.classList.add('active');
            }

            // Panggil fungsi load sesuai section
            if (sectionName === 'formulir') {
                loadFormulirSection();
            } else if (sectionName === 'pembayaran') {
                loadMetodeUntukPendaftar();
                loadRiwayatBukti();
                cekStatusPendaftaran();
            } else if (sectionName === 'seleksi') {
                loadSeleksiSaya();
            } else if (sectionName === 'pengumuman') {
                loadPengumuman();
            } else if (sectionName === 'daftar-ulang') {
                loadDaftarUlangSection();
            } else if (sectionName === 'status') {
                loadStatusPendaftaran();
            }
        }

        // ========== LOGOUT ==========
        document.getElementById('logoutButton').addEventListener('click', async function () {
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
                } else {
                    document.getElementById('message').innerText = 'Gagal logout.';
                    document.getElementById('message').style.display = 'block';
                    setTimeout(() => {
                        document.getElementById('message').style.display = 'none';
                    }, 3000);
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('message').innerText = 'Terjadi kesalahan jaringan.';
                document.getElementById('message').style.display = 'block';
                setTimeout(() => {
                    document.getElementById('message').style.display = 'none';
                }, 3000);
            }
        });
    </script>
</body>

</html>