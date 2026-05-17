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
    </div>

    <script>
        const token = localStorage.getItem('access_token');
        const user = JSON.parse(localStorage.getItem('user') || '{}');

        if (!token) {
            window.location.href = '/login';
        }

        const allowedRoles = ['panitia', 'bendahara', 'kepala_sekolah'];
        if (!token || !allowedRoles.includes(user.role)) {
            window.location.href = '/login';
        }
        
        const userId = {{ $id }};

        async function loadDetail() {
            try {
                const res = await fetch(`/api/pendaftar/${userId}`, {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                if (!res.ok) {
                    if (res.status === 401) throw new Error('Sesi habis, silakan login ulang');
                    throw new Error('Gagal mengambil data');
                }
                const user = await res.json();

                // Status badge helper
                const statusFormClass = user.status_formulir === 'diterima' ? 'diterima' : 
                                       (user.status_formulir === 'ditolak' ? 'ditolak' : 
                                       (user.status_formulir === 'menunggu' ? 'menunggu' : 'belum'));
                const statusFormText = user.status_formulir === 'belum_isi' ? 'Belum mengisi' : user.status_formulir;
                const kelulusanText = user.kelulusan === 'lulus' ? '✅ Lulus' : 
                                     (user.kelulusan === 'tidak_lulus' ? '❌ Tidak Lulus' : 
                                     (user.kelulusan === null ? '-' : user.kelulusan));

                let html = `
                    <div class="section">
                        <h3>Informasi Akun</h3>
                        <table>
                            <tr><th>Nama Lengkap</th><td>${escapeHtml(user.name)}</td></tr>
                            <tr><th>Email</th><td>${escapeHtml(user.email)}</td></tr>
                            <tr><th>No. Induk Pendaftaran</th><td>${user.no_pendaftaran || '-'}</td></tr>
                            <tr><th>Status Formulir</th><td><span class="status-badge status-${statusFormClass}">${statusFormText}</span></td></tr>
                            <tr><th>Kelulusan Tes</th><td>${kelulusanText}</td></tr>
                            <tr><th>Status Daftar Ulang</th><td>${user.status_daftar_ulang || '-'}</td></tr>
                        </table>
                    </div>
                `;

                // === DATA FORMULIR ===
                if (user.formulir) {
                    const f = user.formulir;
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
                        </div>
                    `;
                } else {
                    html += `<div class="section"><h3>Data Formulir</h3><p>Belum mengisi formulir.</p></div>`;
                }

                // === DATA TES ===
                if (user.seleksi_tes) {
                    const st = user.seleksi_tes;
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

                // === DOKUMEN DAFTAR ULANG ===
                if (user.daftar_ulang) {
                    const du = user.daftar_ulang;
                    const tokenParam = encodeURIComponent(token);
                    const fields = [
                        { label: 'Akte Kelahiran', field: 'akte_kelahiran', jenis: 'akte' },
                        { label: 'Ijazah TK', field: 'ijazah_tk', jenis: 'ijazah' },
                        { label: 'KTP Orang Tua/Wali', field: 'ktp_orang_tua', jenis: 'ktp' },
                        { label: 'Kartu Keluarga', field: 'kartu_keluarga', jenis: 'kk' },
                        { label: 'NISN', field: 'nisn_file', jenis: 'nisn' },
                        { label: 'Surat Pernyataan', field: 'surat_pernyataan', jenis: 'pernyataan' },
                        { label: 'Pakta Integritas', field: 'surat_pakta_integritas', jenis: 'pakta' }
                    ];
                    let fileLinks = '<ul>';
                    for (let f of fields) {
                        if (du[f.field]) {
                            let url = `/api/file/daftar-ulang/${du.id}/${f.jenis}?token=${tokenParam}`;
                            fileLinks += `<li><strong>${f.label}:</strong> <a href="${url}" target="_blank">Lihat File</a></li>`;
                        } else {
                            fileLinks += `<li><strong>${f.label}:</strong> Tidak ada</li>`;
                        }
                    }
                    fileLinks += '</ul>';
                    html += `
                        <div class="section">
                            <h3>Dokumen Daftar Ulang</h3>
                            ${fileLinks}
                            <p><strong>Status:</strong> ${du.status}</p>
                            ${du.catatan ? `<p><strong>Catatan:</strong> ${escapeHtml(du.catatan)}</p>` : ''}
                        </div>
                    `;
                } else {
                    html += `<div class="section"><h3>Daftar Ulang</h3><p>Belum melakukan daftar ulang.</p></div>`;
                }

                // === RIWAYAT PEMBAYARAN ===
                if (user.bukti_pembayaran && user.bukti_pembayaran.length) {
                    const tokenParam = encodeURIComponent(token);
                    html += `<div class="section"><h3>Riwayat Pembayaran</h3><ul>`;
                    user.bukti_pembayaran.forEach(b => {
                        const link = `<a href="/api/file/bukti/${b.id_bukti_pembayaran}?token=${tokenParam}" target="_blank">Lihat Bukti</a>`;
                        html += `<li><strong>${b.jenis_pembayaran}</strong> - ${b.status} - ${link}</li>`;
                    });
                    html += `</ul></div>`;
                }

                document.getElementById('loading').style.display = 'none';
                document.getElementById('content').innerHTML = html;
                document.getElementById('content').style.display = 'block';
            } catch (err) {
                document.getElementById('loading').innerHTML = `Error: ${err.message}. <a href="javascript:location.reload()">Refresh</a>`;
            }
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        loadDetail();
    </script>
</body>
</html>