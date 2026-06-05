<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pendaftar - PPDB</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #1a4d2e; border-bottom: 2px solid #1a4d2e; padding-bottom: 8px; }
        .section { margin-bottom: 25px; }
        .section h3 { background: #e8f0e8; padding: 8px 12px; border-left: 4px solid #1a4d2e; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; vertical-align: top; }
        th { width: 180px; background: #f9f9f9; font-weight: 600; }
        .btn-back { display: inline-block; margin-top: 20px; padding: 8px 16px; background: #1a4d2e; color: white; text-decoration: none; border-radius: 4px; }
        .btn-back:hover { background: #0e3a22; }
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 0.85rem; }
        .status-menunggu { background: #f59e0b; color: white; }
        .status-diterima { background: #10b981; color: white; }
        .status-ditolak { background: #ef4444; color: white; }
        .status-belum { background: #9ca3af; color: white; }
        .file-link { margin-right: 12px; }
    </style>
</head>
<body>
    <div class="container" id="app">
        <h2>Detail Pendaftar</h2>
        <div id="loading">Memuat data...</div>
        <div id="content" style="display:none;"></div>
        <a href="javascript:history.back()" class="btn-back">← Kembali ke Dashboard</a>

        <div id="modalFullFormulir" style="display:none; position:fixed; top:5%; left:10%; width:80%; background:white; border:2px solid #1a4d2e; padding:20px; z-index:1000; max-height:85%; overflow:auto; border-radius:8px;">
            <h3>Formulir Pendaftaran Lengkap</h3>
            <div id="fullFormulirContent"></div>
            <div style="margin-top:20px; text-align:right;">
                <button onclick="closeModalFullFormulir()" class="btn-back">Tutup</button>
            </div>
        </div>
        <div id="overlayFullFormulir" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;"></div>
    </div>

    <script>
       // Ambil token dan data user dari localStorage
        const token = localStorage.getItem('access_token');
        const user = JSON.parse(localStorage.getItem('user') || '{}');

        // Validasi autentikasi dan otorisasi
        const allowedRoles = ['panitia', 'bendahara', 'kepala_sekolah'];
        if (!token || !allowedRoles.includes(user.role)) {
            window.location.href = '/login';
        }

        // ID dari server-side (Laravel Blade)
        const userId = {{ $id }};

        // Helper escape HTML
        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        // Fungsi untuk merender bagian Daftar Ulang
        function renderDaftarUlang(du) {
            if (!du) return '';
            const tokenParam = encodeURIComponent(token);
            let html = '';

            // --- Formulir Daftar Ulang ---
            html += `<div class="section">
                <h3>Formulir Daftar Ulang</h3>`;

            if (du.orang_tua || du.wali) {
                const siswaData = du.orang_tua || du.wali;
                html += `<h4>Data Siswa</h4>
                <table style="width:100%; border-collapse:collapse;">
                    <tr><th>Nama Lengkap</th><td>${escapeHtml(siswaData.nama_lengkap || '-')}</td></tr>
                    <tr><th>Tempat, Tanggal Lahir</th><td>${escapeHtml(siswaData.tempat_lahir || '-')}, ${siswaData.tanggal_lahir || '-'}</td></tr>
                    <tr><th>Jenis Kelamin</th><td>${escapeHtml(siswaData.jenis_kelamin || '-')}</td></tr>
                    <tr><th>Asal Sekolah (RA/TK/PAUD)</th><td>${escapeHtml(siswaData.asal_tk || '-')}</td></tr>
                    <tr><th>Alamat Domisili</th><td>${escapeHtml(siswaData.alamat_domisili || '-')}</td></tr>
                </table>`;

                if (du.orang_tua) {
                    html += `<h4>Data Orang Tua (Ayah & Ibu)</h4>
                    <table style="width:100%; border-collapse:collapse;">
                        <tr><th>Ayah - Nama Lengkap</th><td>${escapeHtml(du.orang_tua.nama_ayah || '-')}</td></tr>
                        <tr><th>Ayah - Pendidikan</th><td>${escapeHtml(du.orang_tua.pendidikan_ayah || '-')}</td></tr>
                        <tr><th>Ayah - Pekerjaan</th><td>${escapeHtml(du.orang_tua.pekerjaan_ayah || '-')}</td></tr>
                        <tr><th>Ayah - Alamat KTP</th><td>${escapeHtml(du.orang_tua.alamat_ktp || '-')}</td></tr>
                        <tr><th>Ayah - No HP</th><td>${escapeHtml(du.orang_tua.no_hp || '-')}</td></tr>
                        <tr><th>Ibu - Nama Lengkap</th><td>${escapeHtml(du.orang_tua.nama_ibu || '-')}</td></tr>
                        <tr><th>Ibu - Pendidikan</th><td>${escapeHtml(du.orang_tua.pendidikan_ibu || '-')}</td></tr>
                        <tr><th>Ibu - Pekerjaan</th><td>${escapeHtml(du.orang_tua.pekerjaan_ibu || '-')}</td></tr>
                        <tr><th>Narahubung</th><td>${escapeHtml(du.orang_tua.narahubung || '-')}</td></tr>
                    </table>`;
                } else if (du.wali) {
                    html += `<h4>Data Wali</h4>
                    <table style="width:100%; border-collapse:collapse;">
                        <tr><th>Nama Wali</th><td>${escapeHtml(du.wali.nama_wali || '-')}</td></tr>
                        <tr><th>Pendidikan Wali</th><td>${escapeHtml(du.wali.pendidikan_wali || '-')}</td></tr>
                        <tr><th>Pekerjaan Wali</th><td>${escapeHtml(du.wali.pekerjaan_wali || '-')}</td></tr>
                        <tr><th>Alamat KTP Wali</th><td>${escapeHtml(du.wali.alamat_ktp || '-')}</td></tr>
                        <tr><th>No HP Wali</th><td>${escapeHtml(du.wali.no_hp || '-')}</td></tr>
                        <tr><th>Narahubung</th><td>${escapeHtml(du.wali.narahubung || '-')}</td></tr>
                    </table>`;
                }
            } else {
                html += `<p>Data formulir daftar ulang tidak tersedia.</p>`;
            }
            html += `</div>`;

            // --- Dokumen Daftar Ulang ---
            html += `<div class="section">
                <h3>Dokumen Daftar Ulang</h3>`;
            const fileFields = [
                { label: 'Akte Kelahiran', field: 'akte_kelahiran', jenis: 'akte' },
                { label: 'Ijazah TK', field: 'ijazah_tk', jenis: 'ijazah' },
                { label: 'KTP Orang Tua/Wali', field: 'ktp_orang_tua', jenis: 'ktp' },
                { label: 'Kartu Keluarga', field: 'kartu_keluarga', jenis: 'kk' },
                { label: 'NISN (scan)', field: 'nisn_file', jenis: 'nisn' },
                { label: 'Surat Pernyataan', field: 'surat_pernyataan', jenis: 'pernyataan' },
                { label: 'Pakta Integritas', field: 'surat_pakta_integritas', jenis: 'pakta' }
            ];
            let filesHtml = '<ul style="list-style:none; padding-left:0;">';
            for (let f of fileFields) {
                if (du[f.field]) {
                    const url = `/api/file/daftar-ulang/${du.id}/${f.jenis}?token=${encodeURIComponent(token)}`;
                    filesHtml += `<li style="margin-bottom:8px;"><strong>${f.label}:</strong> <a href="${url}" target="_blank">Lihat File</a></li>`;
                } else {
                    filesHtml += `<li style="margin-bottom:8px;"><strong>${f.label}:</strong> Tidak ada</li>`;
                }
            }
            filesHtml += '</ul>';
            html += filesHtml;
            html += `<p><strong>Status Verifikasi:</strong> ${du.status || '-'}</p>`;
            if (du.catatan) html += `<p><strong>Catatan:</strong> ${escapeHtml(du.catatan)}</p>`;
            html += `</div>`;

            return html;
        }

        // Fungsi untuk menampilkan modal formulir lengkap
        window.showFullFormulir = function() {
            const userData = window.userData;
            if (!userData || !userData.formulir) {
                alert('Data formulir tidak tersedia.');
                return;
            }
            const f = userData.formulir;

            let html = `
                <h4>Data Calon Siswa</h4>
                <table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
                    <tr><th style="width:200px;">Nama Akun</th><td>${escapeHtml(userData.name)}</td></tr>
                    <tr><th>Nama Siswa (sesuai akta)</th><td>${escapeHtml(f.nama_lengkap || '-')}</td></tr>
                    <tr><th>Tempat Lahir</th><td>${escapeHtml(f.tempat_lahir || '-')}</td></tr>
                    <tr><th>Tanggal Lahir</th><td>${f.tanggal_lahir || '-'}</td></tr>
                    <tr><th>Jenis Kelamin</th><td>${f.jenis_kelamin || '-'}</td></tr>
                    <tr><th>NIK</th><td>${escapeHtml(f.nik || '-')}</td></tr>
                    <tr><th>Agama</th><td>${escapeHtml(f.agama || '-')}</td></tr>
                    <tr><th>Warga Negara</th><td>${f.warga_negara || '-'}</td></tr>
                    <tr><th>Anak ke-</th><td>${f.anak_ke || '-'}</td></tr>
                    <tr><th>Jumlah Saudara</th><td>${f.jumlah_saudara || '-'}</td></tr>
                    <tr><th>Alamat Lengkap</th><td>${escapeHtml(f.alamat_lengkap || '-')}</td></tr>
                    <tr><th>Punya NISN</th><td>${f.punya_nisn ? 'Ya' : 'Tidak'}</td></tr>
                    ${f.punya_nisn ? `<tr><th>NISN</th><td>${escapeHtml(f.nisn || '-')}</td></tr>` : ''}
                    <tr><th>Pernah TK/PAUD</th><td>${f.pernah_tk ? 'Ya' : 'Tidak'}</td></tr>
                    ${f.pernah_tk ? `<tr><th>Asal TK/PAUD</th><td>${escapeHtml(f.asal_tk || '-')}</td></tr>` : ''}
                </table>

                <h4>Data Ayah Kandung</h4>
                <table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
                    <tr><th style="width:200px;">Nama</th><td>${escapeHtml(f.nama_ayah || '-')}</td></tr>
                    <tr><th>NIK</th><td>${escapeHtml(f.no_ktp_ayah || '-')}</td></tr>
                    <tr><th>Pekerjaan</th><td>${escapeHtml(f.pekerjaan_ayah || '-')}</td></tr>
                    <tr><th>Agama</th><td>${escapeHtml(f.agama_ayah || '-')}</td></tr>
                    <tr><th>Pendidikan</th><td>${escapeHtml(f.pendidikan_ayah || '-')}</td></tr>
                    <tr><th>Penghasilan</th><td>${escapeHtml(f.penghasilan_ayah || '-')}</td></tr>
                    <tr><th>No. Telepon</th><td>${escapeHtml(f.no_telp_ayah || '-')}</td></tr>
                    <tr><th>Alamat</th><td>${escapeHtml(f.alamat_ayah || '-')}</td></tr>
                </table>

                <h4>Data Ibu Kandung</h4>
                <table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
                    <tr><th style="width:200px;">Nama</th><td>${escapeHtml(f.nama_ibu || '-')}</td></tr>
                    <tr><th>NIK</th><td>${escapeHtml(f.no_ktp_ibu || '-')}</td></tr>
                    <tr><th>Pekerjaan</th><td>${escapeHtml(f.pekerjaan_ibu || '-')}</td></tr>
                    <tr><th>Agama</th><td>${escapeHtml(f.agama_ibu || '-')}</td></tr>
                    <tr><th>Pendidikan</th><td>${escapeHtml(f.pendidikan_ibu || '-')}</td></tr>
                    <tr><th>Penghasilan</th><td>${escapeHtml(f.penghasilan_ibu || '-')}</td></tr>
                    <tr><th>No. Telepon</th><td>${escapeHtml(f.no_telp_ibu || '-')}</td></tr>
                    <tr><th>Alamat</th><td>${escapeHtml(f.alamat_ibu || '-')}</td></tr>
                </table>
            `;

            if (f.tipe_wali && f.nama_wali) {
                html += `
                    <h4>Data Wali (${f.tipe_wali === 'orang_tua' ? 'Orang Tua/Wali' : 'Wali'})</h4>
                    <table style="width:100%; border-collapse:collapse;">
                        <tr><th style="width:200px;">Nama</th><td>${escapeHtml(f.nama_wali || '-')}</td></tr>
                        <tr><th>NIK</th><td>${escapeHtml(f.no_ktp_wali || '-')}</td></tr>
                        <tr><th>Pekerjaan</th><td>${escapeHtml(f.pekerjaan_wali || '-')}</td></tr>
                        <tr><th>Agama</th><td>${escapeHtml(f.agama_wali || '-')}</td></tr>
                        <tr><th>Pendidikan</th><td>${escapeHtml(f.pendidikan_wali || '-')}</td></tr>
                        <tr><th>Penghasilan</th><td>${escapeHtml(f.penghasilan_wali || '-')}</td></tr>
                        <tr><th>No. Telepon</th><td>${escapeHtml(f.no_telp_wali || '-')}</td></tr>
                        <tr><th>Alamat</th><td>${escapeHtml(f.alamat_wali || '-')}</td></tr>
                    </table>
                `;
            }

            document.getElementById('fullFormulirContent').innerHTML = html;
            document.getElementById('modalFullFormulir').style.display = 'block';
            document.getElementById('overlayFullFormulir').style.display = 'block';
            document.body.classList.add('modal-open');
        };

        window.closeModalFullFormulir = function() {
            document.getElementById('modalFullFormulir').style.display = 'none';
            document.getElementById('overlayFullFormulir').style.display = 'none';
            document.body.classList.remove('modal-open');
        };

        // Fungsi utama load data detail
        async function loadDetail() {
            try {
                const res = await fetch(`/api/pendaftar/${userId}`, {
                    headers: { 'Authorization': 'Bearer ' + token }
                });

                if (!res.ok) {
                    if (res.status === 401) throw new Error('Sesi habis, silakan login ulang');
                    throw new Error('Gagal mengambil data');
                }

                const userData = await res.json();
                window.userData = userData; // untuk keperluan modal

                // Load data daftar ulang
                let daftarUlangData = null;
                try {
                    const duRes = await fetch(`/api/pendaftar/${userId}/daftar-ulang`, {
                        headers: { 'Authorization': 'Bearer ' + token }
                    });
                    if (duRes.ok) {
                        daftarUlangData = await duRes.json();
                    }
                } catch (e) {
                    console.error('Gagal memuat data daftar ulang:', e);
                }

                // Status badge helper
                const statusFormClass = userData.status_formulir === 'diterima' ? 'diterima' :
                                    (userData.status_formulir === 'ditolak' ? 'ditolak' :
                                    (userData.status_formulir === 'menunggu' ? 'menunggu' : 'belum'));

                const statusFormText = userData.status_formulir === 'belum_isi' ? 'Belum mengisi' : userData.status_formulir;

                const kelulusanText = userData.kelulusan === 'lulus' ? '✅ Lulus' :
                                    (userData.kelulusan === 'tidak_lulus' ? '❌ Tidak Lulus' :
                                    (userData.kelulusan === null ? '-' : userData.kelulusan));

                let html = `
                    <div class="section">
                        <h3>Informasi Akun</h3>
                        <table>
                            <tr><th>Nama Lengkap</th><td>${escapeHtml(userData.name)}</td></tr>
                            <tr><th>Email</th><td>${escapeHtml(userData.email)}</td></tr>
                            <tr><th>No. Induk Pendaftaran</th><td>${userData.no_pendaftaran || '-'}</td></tr>
                            <td><th>Status Formulir</th><td><span class="status-badge status-${statusFormClass}">${statusFormText}</span></td></tr>
                            <tr><th>Kelulusan Tes</th><td>${kelulusanText}</td></tr>
                            <tr><th>Status Daftar Ulang</th><td>${userData.status_daftar_ulang || '-'}</td></tr>
                        </table>
                    </div>
                `;

                // Data Formulir
                if (userData.formulir) {
                    const f = userData.formulir;
                    html += `
                        <div class="section">
                            <h3>Data Formulir Pendaftaran</h3>
                            <table>
                                <tr><th>Nama Siswa</th><td>${escapeHtml(f.nama_lengkap || '-')}</td></tr>
                                <tr><th>Tempat, Tanggal Lahir</th><td>${escapeHtml(f.tempat_lahir || '-')}, ${f.tanggal_lahir || '-'}</td></tr>
                                <tr><th>NIK</th><td>${escapeHtml(f.nik || '-')}</td></tr>
                                <tr><th>Agama</th><td>${escapeHtml(f.agama || '-')}</td></tr>
                                <tr><th>Alamat</th><td>${escapeHtml(f.alamat_lengkap || '-')}</td></tr>
                                <tr><th>Status Verifikasi</th><td>${f.verifikasi?.hasil_verifikasi || 'Belum diverifikasi'}</td></tr>
                                ${f.verifikasi?.catatan ? `<tr><th>Catatan Verifikasi</th><td>${escapeHtml(f.verifikasi.catatan)}</td></tr>` : ''}
                            </table>
                            <div style="margin-top: 15px;">
                                <button onclick="showFullFormulir()" class="btn-back" style="background:#4a6da8;">📄 Lihat Formulir Lengkap</button>
                            </div>
                        </div>
                    `;
                } else {
                    html += `<div class="section"><h3>Data Formulir</h3><p>Belum mengisi formulir.</p></div>`;
                }

                // Data Tes
                if (userData.seleksi_tes) {
                    const st = userData.seleksi_tes;
                    html += `
                        <div class="section">
                            <h3>Jadwal & Hasil Tes</h3>
                            <table>
                                <tr><th>Jadwal Tes</th><td>${st.jadwal_tes || '-'}</td></tr>
                                ${st.penilaian ? `
                                    <tr><th>Kemampuan Membaca</th><td>${st.penilaian.kemampuan_membaca || '-'}</td></tr>
                                    <tr><th>Kemampuan Menulis</th><td>${st.penilaian.kemampuan_menulis || '-'}</td></tr>
                                    <tr><th>Kemampuan Berhitung</th><td>${st.penilaian.kemampuan_berhitung || '-'}</td></tr>
                                    <tr><th>Baca Alquran</th><td>${st.penilaian.baca_alquran || '-'}</td></tr>
                                ` : ''}
                            </table>
                        </div>
                    `;
                }
                
                // daftar
                html += renderDaftarUlang(daftarUlangData);

                // Riwayat Pembayaran
                if (userData.bukti_pembayaran && userData.bukti_pembayaran.length) {
                    html += `<div class="section"><h3>Riwayat Pembayaran</h3><ul>`;
                    userData.bukti_pembayaran.forEach(b => {
                        const link = `<a href="javascript:void(0)" onclick="lihatBukti(${b.id_bukti_pembayaran})">Lihat Bukti</a>`;
                        html += `<li><strong>${b.jenis_pembayaran}</strong> - ${b.status} - ${link}</li>`;
                    });
                    html += `</ul></div>`;
                }

                // Tampilkan hasil
                document.getElementById('loading').style.display = 'none';
                document.getElementById('content').innerHTML = html;
                document.getElementById('content').style.display = 'block';

            } catch (err) {
                document.getElementById('loading').innerHTML = `Error: ${err.message}. <a href="javascript:location.reload()">Refresh</a>`;
            }
        }

        async function lihatBukti(id) {
            try {
                const res = await fetch(`/api/file/bukti/${id}`, {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                if (!res.ok) {
                    alert('Gagal mengambil bukti pembayaran');
                    return;
                }
                const blob = await res.blob();
                const url = URL.createObjectURL(blob);
                window.open(url, '_blank');
            } catch (e) {
                console.error(e);
                alert('Gagal membuka bukti pembayaran');
            }
        }

        window.lihatBukti = lihatBukti;

        // Jalankan
        loadDetail();
    </script>
</body>
</html>