<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Staff PPDB</title>
    @vite('resources/css/style.css')
    @include('partials.alert-helper')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-dashboard-layout">
    
    <div id="nav-container" class="admin-sidebar">
        <div class="sidebar-header">
            <h3>PPDB MIZI</h3>
            <p>Portal Admin</p>
        </div>
        </div>

    <div class="admin-content">
        <div class="top-header">
            <h1>Dashboard Staf - <span id="userName"></span></h1>
            <p class="role-badge">Login sebagai: <strong id="userRole"></strong></p>
            <button id="logoutButton" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
        </div>
        
        <div class="content-wrapper">
            {{-- Semua section halaman --}}
            @include('partials.staff-dashboard.beranda')
            @include('partials.staff-dashboard.verifikasi')
            @include('partials.staff-dashboard.metode-pembayaran')
            @include('partials.staff-dashboard.verifikasi-pembayaran')
            @include('partials.staff-dashboard.kelola-jadwal')
            @include('partials.staff-dashboard.penilaian')
            @include('partials.staff-dashboard.verifikasi-daftar-ulang')
            @include('partials.staff-dashboard.template-surat')
            @include('partials.staff-dashboard.gelombang')
            @include('partials.staff-dashboard.manajemen-pendaftar')
            @include('partials.staff-dashboard.manajemen-staff')
            @include('partials.staff-dashboard.laporan')
        </div>
    </div>

    {{-- ========================================================
         KUMPULAN MODAL GLOBAL (BAWAAN BEN YANG SUDAH CAKEP)
         ======================================================== --}}

    <style>
        #modalContent {
            width: 95% !important;
            max-width: 1000px !important;
            height: 95vh !important;
            max-height: 95vh !important;
            overflow-y: auto !important;
        }
        #modalGambar {
            width: 100% !important;
            height: 65vh !important;
            max-height: 800px !important;
            object-fit: contain !important;
            background: #f0f0f0;
        }
    </style>
    <div id="modalContent" style="display:none;">
        <h3>Verifikasi Bukti Pembayaran</h3>
        <img id="modalGambar" src="" alt="Bukti Pembayaran"><br>
        <button onclick="bukaGambarFull()">Lihat Gambar Full</button><br><br>
        <label>Hasil Verifikasi:</label><br>
        <select id="modalHasil">
            <option value="diterima">Terima</option>
            <option value="ditolak">Tolak</option>
        </select><br>
        <div id="catatanGroup" style="display:none;">
            <label>Catatan Penolakan:</label><br>
            <textarea id="modalCatatan" rows="2" cols="40"></textarea><br>
        </div>
        <div id="kwitansiGroup">
            <label>Upload Kwitansi (wajib jika diterima):</label><br>
            <input type="file" id="modalKwitansi" accept=".pdf,.jpg,.png"><br>
        </div>
        <br>
        <div class="modal-actions">
            <button onclick="submitVerifikasi()">Kirim Verifikasi</button>
            <button onclick="tutupModal()">Tutup</button>
        </div>
    </div>
    <div id="overlay" style="display:none;"></div>

    <div id="modalPenilaian" style="display:none;">
        <h3>Input Penilaian</h3>
        <input type="hidden" id="modalIdPendaftar">
        <div class="info-box"><strong>Pendaftar: <span id="modalNamaPendaftar"></span></strong></div>
        <label>Kemampuan Membaca:</label><br><input type="text" id="modalMembaca" placeholder="Kemampuan Membaca"><br><br>
        <label>Kemampuan Menulis:</label><br><input type="text" id="modalMenulis" placeholder="Kemampuan Menulis"><br><br>
        <label>Kemampuan Berhitung:</label><br><input type="text" id="modalBerhitung" placeholder="Kemampuan Berhitung"><br><br>
        <label>Baca Alquran:</label><br><input type="text" id="modalBacaQuran" placeholder="Baca Alquran"><br><br>
        <label>Catatan:</label><br><textarea id="modalCatatanPenilaian" rows="3" cols="40"></textarea><br><br>
        <label>Kelulusan:</label><br><select id="modalKelulusan"><option value="lulus">Lulus</option><option value="tidak_lulus">Tidak Lulus</option></select><br><br>
        <div class="modal-actions">
            <button onclick="simpanPenilaianModal()">Simpan Penilaian</button>
            <button onclick="tutupModalPenilaian()">Batal</button>
        </div>
    </div>
    <div id="overlayPenilaian" style="display:none;"></div>

    <div id="modalExportExcel" style="display:none;">
        <h3>Export Data Pendaftar Lulus</h3>
        <div class="grid-2">
            <div><label>Tahun:</label><br><select id="export-filter-tahun"><option value="">Semua Tahun</option></select></div>
            <div><label>Gelombang:</label><br><select id="export-filter-gelombang"><option value="">Semua Gelombang</option></select></div>
            <div><label>Status Formulir:</label><br><select id="export-filter-status-formulir"><option value="">Semua</option><option value="sudah">Sudah Mengisi</option><option value="belum">Belum Mengisi</option></select></div>
            <div><label>Kelulusan:</label><br><select id="export-filter-kelulusan"><option value="lulus">Lulus (default)</option><option value="tidak_lulus">Tidak Lulus</option><option value="belum">Belum Dites</option></select></div>
            <div><label>Status Daftar Ulang:</label><br><select id="export-filter-status-du"><option value="">Semua</option><option value="sudah">Sudah Daftar Ulang</option><option value="belum">Belum Daftar Ulang</option><option value="menunggu">Menunggu Verifikasi</option><option value="diterima">Diterima</option><option value="ditolak">Ditolak</option></select></div>
            <div><label>Status NISN:</label><br><select id="export-filter-nisn"><option value="">Semua</option><option value="ya">Sudah Punya NISN</option><option value="tidak">Belum Punya NISN</option></select></div>
        </div>
        <div style="margin-top:10px;"><label>Cari:</label><br><input type="text" id="export-search" placeholder="Ketik keyword..."></div>
        <hr style="margin:20px 0; border:0; border-top:1px solid #eee;">
        <h4>Pilih Kolom yang Akan Diekspor</h4>
        <div id="export-column-checkboxes" style="display:grid; grid-template-columns:repeat(3,1fr); gap:8px; max-height:200px; overflow-y:auto; border:1px solid #cbd5e1; padding:10px; border-radius:8px;"></div>
        <div style="margin-top:10px;"><button onclick="selectAllColumns()">Pilih Semua</button> <button onclick="deselectAllColumns()">Hapus Semua</button></div>
        <div class="modal-actions"><button onclick="doExportExcel()">Export Excel</button> <button onclick="closeExportModal()">Batal</button></div>
    </div>
    <div id="overlayExport" style="display:none;"></div>

    <div id="modalFilterDU" style="display:none;">
        <h3>Filter Arsip Daftar Ulang</h3>
        <label>Status Daftar Ulang:</label><br><select id="filter-status-du-modal"><option value="">Semua (diterima)</option><option value="diterima">Diterima</option><option value="ditolak">Ditolak</option><option value="menunggu">Menunggu</option></select><br><br>
        <label>Tahun:</label><br><select id="filter-tahun-du"><option value="">Semua Tahun</option></select><br><br>
        <label>Gelombang:</label><br><select id="filter-gelombang-du"><option value="">Semua Gelombang</option></select><br><br>
        <label>Cari (Nama/No Induk):</label><br><input type="text" id="filter-search-du" placeholder="Ketik keyword..."><br><br>
        <div class="modal-actions"><button onclick="downloadArsipDUWithFilter()">Download</button> <button onclick="tutupModalFilterDU()">Batal</button></div>
    </div>
    <div id="overlayFilterDU" style="display:none;"></div>

    <div id="modalFilterPembayaran" style="display:none;">
        <h3>Filter Arsip Pembayaran</h3>
        <label>Jenis Pembayaran:</label><br><select id="filter-jenis-pembayaran"><option value="">Semua</option><option value="formulir">Formulir</option><option value="masuk">Daftar Ulang</option></select><br><br>
        <label>Tahun:</label><br><select id="filter-tahun-pembayaran"><option value="">Semua Tahun</option></select><br><br>
        <label>Gelombang:</label><br><select id="filter-gelombang-pembayaran"><option value="">Semua Gelombang</option></select><br><br>
        <div class="modal-actions"><button onclick="downloadArsipPembayaranWithFilter()">Download</button> <button onclick="tutupModalFilterPembayaran()">Batal</button></div>
    </div>
    <div id="overlayFilterPembayaran" style="display:none;"></div>

    <div id="modalDetailPendaftar" style="display:none;">
        <h3>Detail Pendaftar</h3>
        <div id="detail-content"></div>
        <div class="modal-actions">
            <button id="btnLihatFormulir" onclick="lihatFormulirPendaftar()">Lihat Formulir</button>
            <button id="btnLihatDokumenDU" onclick="lihatDokumenDaftarUlang()">Lihat Dokumen Daftar Ulang</button>
            <button onclick="tutupModalDetail()">Tutup</button>
        </div>
    </div>
    <div id="overlayDetail" style="display:none;"></div>

    <div id="modalFormulir" style="display:none;">
        <h3>Data Formulir Pendaftaran</h3>
        <div id="formulir-content"></div>
        <div class="modal-actions"><button onclick="tutupModalFormulir()">Tutup</button></div>
    </div>

    <div id="modalDokumenDU" style="display:none;">
        <h3>Dokumen Daftar Ulang</h3>
        <div id="dokumen-du-content"></div>
        <div class="modal-actions"><button onclick="tutupModalDokumenDU()">Tutup</button></div>
    </div>

    <div id="modalFormulirDaftarUlang" style="display:none;">
        <h3>Formulir Daftar Ulang</h3>
        <div id="formulirDaftarUlangContent"></div>
        <div class="modal-actions"><button onclick="tutupModalFormulirDaftarUlang()">Tutup</button></div>
    </div>
    <div id="overlayFormulirDU" style="display:none;"></div>

    <div id="modalDaftarUlang" style="display:none;">
        <h3>Verifikasi Daftar Ulang</h3>
        <input type="hidden" id="du-id">
        <div class="info-box"><strong>Pendaftar: <span id="du-nama"></span></strong></div>
        
        <label>Berkas Dokumen:</label>
        <div id="du-files" style="margin-bottom:15px; background:#f8fafc; padding:15px; border-radius:8px; border:1px solid #cbd5e1;"></div>

        <div id="du-verifikasi-form">
            <label>Status Verifikasi:</label><br>
            <select id="du-status">
                <option value="diterima">Terima</option>
                <option value="ditolak">Tolak</option>
            </select><br>

            <div id="du-catatan-group" style="display:none;">
                <label>Catatan Penolakan:</label><br>
                <textarea id="du-catatan" rows="3" cols="40" placeholder="Masukkan alasan penolakan..."></textarea><br>
            </div>
        </div>

        <div class="modal-actions">
            <button id="du-simpan-btn" onclick="submitVerifikasiDaftarUlang()">Simpan Verifikasi</button>
            <button onclick="tutupModalDaftarUlang()">Tutup</button>
        </div>
    </div>
    <div id="overlayDaftarUlang" style="display:none;"></div>

    {{-- Script global javascript bawaan Ben (Navigasi, Logout, API fetcher) --}}
    <script>
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        const token = localStorage.getItem('access_token');

        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            str = String(str);
            return str.replace(/[&<>'"]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                if (m === "'") return '&#39;';
                if (m === '"') return '&quot;';
                return m;
            });
        }

        const allowedRoles = ['panitia', 'bendahara', 'kepala_sekolah'];
        if (!token || !allowedRoles.includes(user.role)) {
            window.location.href = '/login';
        }

        document.getElementById('userName').innerText = user.name || 'Pengguna';
        let roleText = '';
        if (user.role === 'kepala_sekolah') roleText = 'Kepala Sekolah';
        else if (user.role === 'panitia') roleText = 'Panitia PPDB';
        else if (user.role === 'bendahara') roleText = 'Bendahara';
        document.getElementById('userRole').innerText = roleText;

        // Menu berdasarkan role dengan icon FontAwesome
        const menuByRole = {
            panitia: [
                { id: 'beranda-staff', label: '<i class="fa-solid fa-house"></i> Beranda' },
                { id: 'verifikasi', label: '<i class="fa-solid fa-user-check"></i> Verifikasi Pendaftar' },
                { id: 'kelola-jadwal', label: '<i class="fa-solid fa-calendar-days"></i> Kelola Jadwal' },
                { id: 'penilaian', label: '<i class="fa-solid fa-star"></i> Penilaian' },
                { id: 'verifikasi-daftar-ulang', label: '<i class="fa-solid fa-file-signature"></i> Daftar Ulang' },
                { id: 'template-surat', label: '<i class="fa-solid fa-envelope"></i> Template Surat' },
                { id: 'gelombang', label: '<i class="fa-solid fa-layer-group"></i> Gelombang' },
                { id: 'manajemen-pendaftar', label: '<i class="fa-solid fa-users"></i> Manajemen Pendaftar' },
                { id: 'laporan', label: '<i class="fa-solid fa-chart-pie"></i> Laporan' }
            ],
            bendahara: [
                { id: 'beranda-staff', label: '<i class="fa-solid fa-house"></i> Beranda' },
                { id: 'metode-pembayaran', label: '<i class="fa-solid fa-building-columns"></i> Metode Pembayaran' },
                { id: 'verifikasi-pembayaran', label: '<i class="fa-solid fa-money-bill-transfer"></i> Verifikasi Bayar' },
                { id: 'gelombang', label: '<i class="fa-solid fa-layer-group"></i> Gelombang' },
                { id: 'laporan', label: '<i class="fa-solid fa-chart-pie"></i> Laporan' }
            ],
            kepala_sekolah: [
                { id: 'beranda-staff', label: '<i class="fa-solid fa-house"></i> Beranda' },
                { id: 'verifikasi', label: '<i class="fa-solid fa-user-check"></i> Verifikasi Pendaftar' },
                { id: 'metode-pembayaran', label: '<i class="fa-solid fa-building-columns"></i> Metode Pembayaran' },
                { id: 'verifikasi-pembayaran', label: '<i class="fa-solid fa-money-bill-transfer"></i> Verifikasi Bayar' },
                { id: 'kelola-jadwal', label: '<i class="fa-solid fa-calendar-days"></i> Kelola Jadwal' },
                { id: 'penilaian', label: '<i class="fa-solid fa-star"></i> Penilaian' },
                { id: 'verifikasi-daftar-ulang', label: '<i class="fa-solid fa-file-signature"></i> Daftar Ulang' },
                { id: 'template-surat', label: '<i class="fa-solid fa-envelope"></i> Template Surat' },
                { id: 'gelombang', label: '<i class="fa-solid fa-layer-group"></i> Gelombang' },
                { id: 'manajemen-pendaftar', label: '<i class="fa-solid fa-users"></i> Manajemen Pendaftar' },
                { id: 'manajemen-staff', label: '<i class="fa-solid fa-user-tie"></i> Manajemen Staff' },
                { id: 'laporan', label: '<i class="fa-solid fa-chart-pie"></i> Laporan' }
            ]
        };

        const currentMenu = menuByRole[user.role] || menuByRole.panitia;
        const navContainer = document.getElementById('nav-container');
        currentMenu.forEach(menu => {
            const btn = document.createElement('button');
            btn.innerHTML = menu.label;
            btn.setAttribute('data-section', menu.id);
            navContainer.appendChild(btn);
        });

        function showSection(sectionId) {
            document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
            const target = document.getElementById(sectionId);
            if (target) target.style.display = 'block';

            // Mengatur active state class tombol sidebar
            document.querySelectorAll('#nav-container button').forEach(btn => btn.classList.remove('active'));
            const activeBtn = document.querySelector(`button[data-section="${sectionId}"]`);
            if(activeBtn) activeBtn.classList.add('active');

            if (sectionId === 'beranda-staff' && typeof loadStaffStats === 'function') loadStaffStats();
            else if (sectionId === 'verifikasi' && (user.role === 'panitia' || user.role === 'kepala_sekolah') && typeof loadVerifikasi === 'function') loadVerifikasi();
            else if (sectionId === 'metode-pembayaran' && (user.role === 'bendahara' || user.role === 'kepala_sekolah') && typeof loadMetodePembayaran === 'function') loadMetodePembayaran();
            else if (sectionId === 'verifikasi-pembayaran' && (user.role === 'bendahara' || user.role === 'kepala_sekolah') && typeof switchJenisPembayaran === 'function') switchJenisPembayaran('formulir');
            else if (sectionId === 'kelola-jadwal' && (user.role === 'panitia' || user.role === 'kepala_sekolah')) {
                if (typeof loadBelumTerjadwal === 'function') loadBelumTerjadwal();
                if (typeof loadSudahTerjadwal === 'function') loadSudahTerjadwal();
            } else if (sectionId === 'penilaian' && (user.role === 'panitia' || user.role === 'kepala_sekolah')) {
                if (typeof loadBelumDinilai === 'function') loadBelumDinilai();
                if (typeof loadRiwayatPenilaian === 'function') loadRiwayatPenilaian();
            } else if (sectionId === 'verifikasi-daftar-ulang' && typeof loadDaftarUlangStaff === 'function') loadDaftarUlangStaff();
            else if (sectionId === 'template-surat' && typeof loadTemplateSurat === 'function') loadTemplateSurat();
            else if (sectionId === 'gelombang' && typeof loadGelombang === 'function') loadGelombang();
            else if (sectionId === 'manajemen-pendaftar') {
                if (typeof loadFilterOptions === 'function') loadFilterOptions();
                if (typeof loadPendaftar === 'function') loadPendaftar();
            } else if (sectionId === 'manajemen-staff' && typeof loadStaff === 'function') loadStaff();
        }

        document.querySelectorAll('button[data-section]').forEach(button => {
            button.addEventListener('click', function () {
                showSection(this.getAttribute('data-section'));
            });
        });

        if (currentMenu.length) showSection(currentMenu[0].id);

        document.getElementById('logoutButton').addEventListener('click', async function () {
            if (!token) { window.location.href = '/login'; return; }
            try {
                const response = await fetch('/api/logout', {
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });
                if (response.ok) {
                    localStorage.removeItem('access_token');
                    localStorage.removeItem('user');
                    window.location.href = '/login';
                }
            } catch (error) {
                alert('Gagal logout.');
            }
        });
    </script>

    {{-- Kumpulan script dari setiap section partials --}}
    @stack('staff-scripts')
</body>
</html>