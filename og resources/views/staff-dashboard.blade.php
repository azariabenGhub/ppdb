<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Staff PPDB</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Gaya minimal (bisa ditambah sesuai kebutuhan) 
        body { font-family: sans-serif; margin: 0; padding: 20px; background: #f5f7fa; }
        #nav-container button { margin: 5px; padding: 8px 16px; cursor: pointer; background: #e2e8f0; border: none; border-radius: 6px; }
        #nav-container button:hover { background: #cbd5e1; }
        .section { background: white; padding: 20px; border-radius: 12px; margin-top: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        button { cursor: pointer; }
        .tab-active { background: #1a4d2e; color: white; }
         Gaya lainnya dari file asli bisa ditambahkan di sini */
    </style>
</head>
<body>
    <h1>Dashboard Staf - <span id="userName"></span></h1>
    <p>Anda login sebagai <strong id="userRole"></strong>.</p>

    <!-- Navigasi akan diisi dinamis oleh JavaScript -->
    <div id="nav-container"></div>
    <hr>

    {{-- Semua section --}}
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

    {{-- Semua modal global (diambil dari file asli) --}}
    <!-- Modal Verifikasi Pembayaran -->
    <div id="modalVerifikasi" style="display:none; position:fixed; top:10%; left:10%; width:80%; background:white; border:2px solid #ccc; padding:20px; z-index:1000;">
        <h3>Verifikasi Bukti Pembayaran</h3>
        <div id="modalContent">
            <img id="modalGambar" src="" style="max-width:100%; max-height:300px;"><br>
            <button onclick="bukaGambarFull()">Lihat Gambar Full</button><br><br>
            <label>Hasil Verifikasi:</label><br>
            <select id="modalHasil">
                <option value="diterima">Terima</option>
                <option value="ditolak">Tolak</option>
            </select><br>
            <div id="modalCatatanGroup" style="display:none;">
                <label>Catatan Penolakan:</label><br>
                <textarea id="modalCatatan" rows="2" cols="40"></textarea><br>
            </div>
            <div id="modalKwitansiGroup">
                <label>Upload Kwitansi (wajib jika diterima):</label><br>
                <input type="file" id="modalKwitansi" accept=".pdf,.jpg,.png"><br>
            </div>
            <br>
            <button onclick="submitVerifikasi()">Kirim Verifikasi</button>
            <button onclick="tutupModal()">Tutup</button>
        </div>
    </div>
    <div id="overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;"></div>

    <!-- Modal Penilaian -->
    <div id="modalPenilaian" style="display:none; position:fixed; top:10%; left:10%; width:80%; background:white; border:2px solid #ccc; padding:20px; z-index:1000;">
        <h3>Input Penilaian</h3>
        <input type="hidden" id="modalIdPendaftar">
        <p><strong>Pendaftar: <span id="modalNamaPendaftar"></span></strong></p>
        <label>Kemampuan Membaca:</label><br><input type="text" id="modalMembaca" placeholder="Kemampuan Membaca"><br><br>
        <label>Kemampuan Menulis:</label><br><input type="text" id="modalMenulis" placeholder="Kemampuan Menulis"><br><br>
        <label>Kemampuan Berhitung:</label><br><input type="text" id="modalBerhitung" placeholder="Kemampuan Berhitung"><br><br>
        <label>Baca Alquran:</label><br><input type="text" id="modalBacaQuran" placeholder="Baca Alquran"><br><br>
        <label>Catatan:</label><br><textarea id="modalCatatanPenilaian" rows="3" cols="40"></textarea><br><br>
        <label>Kelulusan:</label><br><select id="modalKelulusan"><option value="lulus">Lulus</option><option value="tidak_lulus">Tidak Lulus</option></select><br><br>
        <button onclick="simpanPenilaianModal()">Simpan Penilaian</button>
        <button onclick="tutupModalPenilaian()">Batal</button>
    </div>
    <div id="overlayPenilaian" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;"></div>

    <!-- Modal Export Excel -->
    <div id="modalExportExcel" style="display:none; position:fixed; top:5%; left:10%; width:80%; background:white; border:2px solid #1a4d2e; padding:20px; z-index:1002; max-height:85%; overflow:auto; border-radius:8px;">
        <h3>Export Data Pendaftar Lulus</h3>
        <div style="display:flex; gap:20px; flex-wrap:wrap;">
            <div><label>Tahun:</label><br><select id="export-filter-tahun" style="padding:6px;"><option value="">Semua Tahun</option></select></div>
            <div><label>Gelombang:</label><br><select id="export-filter-gelombang" style="padding:6px;"><option value="">Semua Gelombang</option></select></div>
            <div><label>Status Formulir:</label><br><select id="export-filter-status-formulir" style="padding:6px;"><option value="">Semua</option><option value="sudah">Sudah Mengisi</option><option value="belum">Belum Mengisi</option></select></div>
            <div><label>Kelulusan:</label><br><select id="export-filter-kelulusan" style="padding:6px;"><option value="lulus">Lulus (default)</option><option value="tidak_lulus">Tidak Lulus</option><option value="belum">Belum Dites</option></select></div>
            <div><label>Status Daftar Ulang:</label><br><select id="export-filter-status-du" style="padding:6px;"><option value="">Semua</option><option value="sudah">Sudah Daftar Ulang</option><option value="belum">Belum Daftar Ulang</option><option value="menunggu">Menunggu Verifikasi</option><option value="diterima">Diterima</option><option value="ditolak">Ditolak</option></select></div>
            <div><label>Status NISN:</label><br><select id="export-filter-nisn" style="padding:6px;"><option value="">Semua</option><option value="ya">Sudah Punya NISN</option><option value="tidak">Belum Punya NISN</option></select></div>
            <div><label>Cari:</label><br><input type="text" id="export-search" placeholder="Ketik keyword..." style="padding:6px; width:200px;"></div>
        </div>
        <hr>
        <h4>Pilih Kolom yang Akan Diekspor</h4>
        <div id="export-column-checkboxes" style="display:grid; grid-template-columns:repeat(3,1fr); gap:8px; max-height:300px; overflow-y:auto; border:1px solid #ccc; padding:10px;"></div>
        <div style="margin-top:10px;"><button onclick="selectAllColumns()">Pilih Semua</button> <button onclick="deselectAllColumns()">Hapus Semua</button></div>
        <div style="margin-top:20px; text-align:right;"><button onclick="doExportExcel()" style="background:#1a4d2e; color:white; padding:8px 16px;">Export Excel</button> <button onclick="closeExportModal()">Batal</button></div>
    </div>
    <div id="overlayExport" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1001;"></div>

    <!-- Modal Filter Arsip Daftar Ulang -->
    <div id="modalFilterDU" style="display:none; position:fixed; top:15%; left:25%; width:50%; background:white; border:2px solid #1a4d2e; padding:20px; z-index:1003; border-radius:8px;">
        <h3>Filter Arsip Daftar Ulang</h3>
        <div style="display:flex; flex-direction:column; gap:15px;">
            <div><label>Status Daftar Ulang:</label><br><select id="filter-status-du-modal" style="padding:6px; width:100%;"><option value="">Semua (diterima)</option><option value="diterima">Diterima</option><option value="ditolak">Ditolak</option><option value="menunggu">Menunggu</option></select></div>
            <div><label>Tahun:</label><br><select id="filter-tahun-du" style="padding:6px; width:100%;"><option value="">Semua Tahun</option></select></div>
            <div><label>Gelombang:</label><br><select id="filter-gelombang-du" style="padding:6px; width:100%;"><option value="">Semua Gelombang</option></select></div>
            <div><label>Cari (Nama/No Induk):</label><br><input type="text" id="filter-search-du" placeholder="Ketik keyword..." style="padding:6px; width:100%;"></div>
        </div>
        <div style="margin-top:20px; text-align:right;"><button onclick="downloadArsipDUWithFilter()" style="background:#1a4d2e; color:white; padding:8px 16px;">Download</button> <button onclick="tutupModalFilterDU()">Batal</button></div>
    </div>
    <div id="overlayFilterDU" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1002;"></div>

    <!-- Modal Filter Arsip Pembayaran -->
    <div id="modalFilterPembayaran" style="display:none; position:fixed; top:15%; left:25%; width:50%; background:white; border:2px solid #1a4d2e; padding:20px; z-index:1003; border-radius:8px;">
        <h3>Filter Arsip Pembayaran</h3>
        <div style="display:flex; flex-direction:column; gap:15px;">
            <div><label>Jenis Pembayaran:</label><br><select id="filter-jenis-pembayaran" style="padding:6px; width:100%;"><option value="">Semua</option><option value="formulir">Formulir</option><option value="masuk">Daftar Ulang</option></select></div>
            <div><label>Tahun:</label><br><select id="filter-tahun-pembayaran" style="padding:6px; width:100%;"><option value="">Semua Tahun</option></select></div>
            <div><label>Gelombang:</label><br><select id="filter-gelombang-pembayaran" style="padding:6px; width:100%;"><option value="">Semua Gelombang</option></select></div>
        </div>
        <div style="margin-top:20px; text-align:right;"><button onclick="downloadArsipPembayaranWithFilter()" style="background:#1a4d2e; color:white; padding:8px 16px;">Download</button> <button onclick="tutupModalFilterPembayaran()">Batal</button></div>
    </div>
    <div id="overlayFilterPembayaran" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1002;"></div>

    <!-- Modal Detail Pendaftar -->
    <div id="modalDetailPendaftar" style="display:none; position:fixed; top:5%; left:10%; width:80%; background:white; border:2px solid #1a4d2e; padding:20px; z-index:1000; max-height:85%; overflow:auto; border-radius:8px;">
        <h3>Detail Pendaftar</h3>
        <div id="detail-content"></div>
        <div style="margin-top:25px; display:flex; gap:15px; justify-content:flex-end; border-top:1px solid #eee; padding-top:20px;">
            <button id="btnLihatFormulir" onclick="lihatFormulirPendaftar()" style="padding:8px 16px; background:#1a4d2e; color:white; border:none; border-radius:6px;">Lihat Formulir</button>
            <button id="btnLihatDokumenDU" onclick="lihatDokumenDaftarUlang()" style="padding:8px 16px; background:#1a4d2e; color:white; border:none; border-radius:6px;">Lihat Dokumen Daftar Ulang</button>
            <button onclick="tutupModalDetail()" style="padding:8px 16px; background:#ccc; border:none; border-radius:6px;">Tutup</button>
        </div>
    </div>
    <div id="overlayDetail" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;"></div>

    <!-- Modal Formulir Pendaftar -->
    <div id="modalFormulir" style="display:none; position:fixed; top:10%; left:15%; width:70%; background:white; border:2px solid #1a4d2e; padding:20px; z-index:1001; max-height:70%; overflow:auto; border-radius:8px;">
        <h3>Data Formulir Pendaftaran</h3>
        <div id="formulir-content"></div>
        <div style="margin-top:20px; text-align:right;"><button onclick="tutupModalFormulir()">Tutup</button></div>
    </div>

    <!-- Modal Dokumen Daftar Ulang -->
    <div id="modalDokumenDU" style="display:none; position:fixed; top:10%; left:15%; width:70%; background:white; border:2px solid #1a4d2e; padding:20px; z-index:1001; max-height:70%; overflow:auto; border-radius:8px;">
        <h3>Dokumen Daftar Ulang</h3>
        <div id="dokumen-du-content"></div>
        <div style="margin-top:20px; text-align:right;"><button onclick="tutupModalDokumenDU()">Tutup</button></div>
    </div>

    <!-- Modal Formulir Daftar Ulang (untuk staff) -->
    <div id="modalFormulirDaftarUlang" style="display:none; position:fixed; top:10%; left:15%; width:70%; background:white; border:2px solid #1a4d2e; padding:20px; z-index:1001; max-height:70%; overflow:auto; border-radius:8px;">
        <h3>Formulir Daftar Ulang</h3>
        <div id="formulirDaftarUlangContent"></div>
        <div style="margin-top:20px; text-align:right;"><button onclick="tutupModalFormulirDaftarUlang()">Tutup</button></div>
    </div>
    <div id="overlayFormulirDU" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;"></div>

    <hr>
    <button id="logoutButton">Logout</button>
    <div id="message"></div>

    {{-- Kumpulan script dari setiap section --}}
    @stack('staff-scripts')

    {{-- Script global (navigasi, logout, dll) --}}
    <script>
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        const token = localStorage.getItem('access_token');

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function (m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
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

        // Menu berdasarkan role
        const menuByRole = {
            panitia: [
                { id: 'beranda-staff', label: 'Beranda' },
                { id: 'verifikasi', label: 'Verifikasi Pendaftar' },
                { id: 'kelola-jadwal', label: 'Kelola Jadwal' },
                { id: 'penilaian', label: 'Penilaian' },
                { id: 'verifikasi-daftar-ulang', label: 'Verifikasi Daftar Ulang' },
                { id: 'template-surat', label: 'Template Surat' },
                { id: 'gelombang', label: 'Gelombang' },
                { id: 'manajemen-pendaftar', label: 'Manajemen Pendaftar' },
                { id: 'laporan', label: 'Laporan' }
            ],
            bendahara: [
                { id: 'beranda-staff', label: 'Beranda' },
                { id: 'metode-pembayaran', label: 'Metode Pembayaran' },
                { id: 'verifikasi-pembayaran', label: 'Verifikasi Pembayaran' },
                { id: 'gelombang', label: 'Gelombang' },
                { id: 'laporan', label: 'Laporan' }
            ],
            kepala_sekolah: [
                { id: 'beranda-staff', label: 'Beranda' },
                { id: 'verifikasi', label: 'Verifikasi Pendaftar' },
                { id: 'metode-pembayaran', label: 'Metode Pembayaran' },
                { id: 'verifikasi-pembayaran', label: 'Verifikasi Pembayaran' },
                { id: 'kelola-jadwal', label: 'Kelola Jadwal' },
                { id: 'penilaian', label: 'Penilaian' },
                { id: 'verifikasi-daftar-ulang', label: 'Verifikasi Daftar Ulang' },
                { id: 'template-surat', label: 'Template Surat' },
                { id: 'gelombang', label: 'Gelombang' },
                { id: 'manajemen-pendaftar', label: 'Manajemen Pendaftar' },
                { id: 'manajemen-staff', label: 'Manajemen Staff' },
                { id: 'laporan', label: 'Laporan' }
            ]
        };

        const currentMenu = menuByRole[user.role] || menuByRole.panitia;
        const navContainer = document.getElementById('nav-container');
        currentMenu.forEach(menu => {
            const btn = document.createElement('button');
            btn.textContent = menu.label;
            btn.setAttribute('data-section', menu.id);
            navContainer.appendChild(btn);
        });

        function showSection(sectionId) {
            document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
            const target = document.getElementById(sectionId);
            if (target) target.style.display = 'block';

            // Panggil fungsi load sesuai section (didefinisikan di file masing-masing)
            if (sectionId === 'verifikasi' && (user.role === 'panitia' || user.role === 'kepala_sekolah') && typeof loadVerifikasi === 'function') loadVerifikasi();
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
                document.getElementById('message').innerText = 'Gagal logout.';
                document.getElementById('message').style.display = 'block';
                setTimeout(() => document.getElementById('message').style.display = 'none', 3000);
            }
        });
    </script>
</body>
</html>