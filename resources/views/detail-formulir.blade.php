<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Formulir #{{ $id }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #1a4d2e; border-bottom: 2px solid #1a4d2e; padding-bottom: 8px; margin-top: 0; }
        .section { margin-bottom: 25px; }
        .section h3 { background: #e8f0e8; padding: 8px 12px; border-left: 4px solid #1a4d2e; margin-bottom: 12px; margin-top: 0; color: #1a4d2e; }
        .sub-section-title { font-weight: bold; color: #1a4d2e; margin: 15px 0 8px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 15px; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; vertical-align: top; }
        th { width: 200px; background: #f9f9f9; font-weight: 600; }
        .btn-back { display: inline-block; margin-top: 20px; padding: 8px 16px; background: #1a4d2e; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
        .btn-back:hover { background: #0e3a22; }
        
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: bold; }
        .status-menunggu { background: #f59e0b; color: white; }
        .status-diterima { background: #10b981; color: white; }
        .status-ditolak { background: #ef4444; color: white; }

        .btn-action { padding: 8px 16px; border-radius: 4px; border: none; cursor: pointer; font-weight: bold; margin-right: 10px; }
        .btn-success { background: #10b981; color: white; }
        .btn-success:hover { background: #059669; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-danger:hover { background: #dc2626; }

        .rejection-note { background-color: #fee2e2; border-left: 4px solid #ef4444; padding: 12px; margin-top: 10px; border-radius: 4px; }
        .rejection-note h4 { margin: 0 0 5px 0; color: #b91c1c; }
        .rejection-note p { margin: 0; color: #7f1d1d; }

        .action-banner { background: #f9f9f9; padding: 15px; border-radius: 6px; border: 1px solid #e5e7eb; margin-top: 20px; }
        .action-banner h4 { margin: 0 0 8px 0; color: #1a4d2e; }
        .action-banner p { margin: 0 0 12px 0; color: #555; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Detail Formulir #{{ $id }}</h2>
        
        <div id="detail">Memuat data...</div>

        <a href="/staff-dashboard" class="btn-back">← Kembali ke Dashboard</a>
    </div>

    <script>
        // Ambil token & user
        const token = localStorage.getItem('access_token');
        const user = JSON.parse(localStorage.getItem('user') || 'null');

        // Proteksi: hanya panitia & kepsek yang boleh akses
        if (!token || !user || (user.role !== 'panitia' && user.role !== 'kepala_sekolah')) {
            alert('Anda tidak memiliki akses.');
            window.location.href = '/login';
        }

        const id = {{ $id }}; // Diisi dari route Laravel

        async function load() {
            const detailContainer = document.getElementById('detail');
            try {
                const res = await fetch(`/api/pendaftaran/${id}`, {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });

                if (!res.ok) {
                    detailContainer.innerHTML = '<p>Gagal memuat data.</p>';
                    return;
                }

                const result = await res.json();
                const d = result.data;

                let statusClass = 'status-menunggu';
                let statusLabel = 'Menunggu';
                if (d.status === 'diterima') {
                    statusClass = 'status-diterima';
                    statusLabel = 'Diterima';
                } else if (d.status === 'ditolak') {
                    statusClass = 'status-ditolak';
                    statusLabel = 'Ditolak';
                }

                let html = `
                    <!-- 1. BIODATA SISWA -->
                    <div class="section">
                        <h3>Biodata Siswa</h3>
                        <table>
                            <tr>
                                <th>Nama Lengkap</th>
                                <td>${d.nama_lengkap}</td>
                            </tr>
                            <tr>
                                <th>Tempat, Tanggal Lahir</th>
                                <td>${d.tempat_lahir}, ${d.tanggal_lahir}</td>
                            </tr>
                            <tr>
                                <th>NIK</th>
                                <td>${d.nik}</td>
                            </tr>
                            <tr>
                                <th>Agama</th>
                                <td>${d.agama}</td>
                            </tr>
                            <tr>
                                <th>Kewarganegaraan</th>
                                <td>${d.warga_negara}</td>
                            </tr>
                            <tr>
                                <th>Anak Ke / Jumlah Saudara</th>
                                <td>Anak ke-${d.anak_ke || '-'} dari ${d.jumlah_saudara || '-'} bersaudara</td>
                            </tr>
                            <tr>
                                <th>Alamat Lengkap</th>
                                <td>${d.alamat_lengkap}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- 2. DATA ORANG TUA / WALI -->
                    <div class="section">
                        <h3>Data ${d.tipe_wali === 'orang_tua' ? 'Orang Tua' : 'Wali'}</h3>
                        ${d.tipe_wali === 'orang_tua' ? `
                            <div class="sub-section-title">Data Ayah Kandung</div>
                            <table>
                                <tr>
                                    <th>Nama Ayah</th>
                                    <td>${d.nama_ayah || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Pekerjaan Ayah</th>
                                    <td>${d.pekerjaan_ayah || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Agama Ayah</th>
                                    <td>${d.agama_ayah || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Pendidikan Ayah</th>
                                    <td>${d.pendidikan_ayah || '-'}</td>
                                </tr>
                                <tr>
                                    <th>No. KTP / NIK Ayah</th>
                                    <td>${d.no_ktp_ayah || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Penghasilan Ayah</th>
                                    <td>${d.penghasilan_ayah || '-'}</td>
                                </tr>
                                <tr>
                                    <th>No. Telepon / WA</th>
                                    <td>${d.no_telp_ayah || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Alamat Ayah</th>
                                    <td>${d.alamat_ayah || '-'}</td>
                                </tr>
                            </table>

                            <div class="sub-section-title" style="margin-top:20px;">Data Ibu Kandung</div>
                            <table>
                                <tr>
                                    <th>Nama Ibu</th>
                                    <td>${d.nama_ibu || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Pekerjaan Ibu</th>
                                    <td>${d.pekerjaan_ibu || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Agama Ibu</th>
                                    <td>${d.agama_ibu || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Pendidikan Ibu</th>
                                    <td>${d.pendidikan_ibu || '-'}</td>
                                </tr>
                                <tr>
                                    <th>No. KTP / NIK Ibu</th>
                                    <td>${d.no_ktp_ibu || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Penghasilan Ibu</th>
                                    <td>${d.penghasilan_ibu || '-'}</td>
                                </tr>
                                <tr>
                                    <th>No. Telepon / WA</th>
                                    <td>${d.no_telp_ibu || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Alamat Ibu</th>
                                    <td>${d.alamat_ibu || '-'}</td>
                                </tr>
                            </table>
                        ` : `
                            <div class="sub-section-title">Data Wali</div>
                            <table>
                                <tr>
                                    <th>Nama Wali</th>
                                    <td>${d.nama_wali || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Pekerjaan Wali</th>
                                    <td>${d.pekerjaan_wali || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Agama Wali</th>
                                    <td>${d.agama_wali || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Pendidikan Wali</th>
                                    <td>${d.pendidikan_wali || '-'}</td>
                                </tr>
                                <tr>
                                    <th>No. KTP / NIK Wali</th>
                                    <td>${d.no_ktp_wali || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Penghasilan Wali</th>
                                    <td>${d.penghasilan_wali || '-'}</td>
                                </tr>
                                <tr>
                                    <th>No. Telepon / WA</th>
                                    <td>${d.no_telp_wali || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Alamat Wali</th>
                                    <td>${d.alamat_wali || '-'}</td>
                                </tr>
                            </table>
                        `}
                    </div>

                    <!-- 3. AKADEMIS & STATUS -->
                    <div class="section">
                        <h3>Akademik & Status</h3>
                        <table>
                            ${d.is_bukan_pindahan ? `
                                <tr>
                                    <th>Status Siswa</th>
                                    <td>Siswa Baru (Bukan Pindahan)</td>
                                </tr>
                            ` : `
                                <tr>
                                    <th>Status Siswa</th>
                                    <td>Siswa Pindahan</td>
                                </tr>
                                <tr>
                                    <th>Asal Sekolah</th>
                                    <td>${d.asal_sekolah || '-'}</td>
                                </tr>
                                <tr>
                                    <th>No. Ijazah & Tahun Ijazah</th>
                                    <td>${d.no_ijazah || '-'} (${d.tahun_ijazah || '-'})</td>
                                </tr>
                                <tr>
                                    <th>Diterima di Kelas</th>
                                    <td>${d.diterima_kelas || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Pindah dari / No & Tgl Surat</th>
                                    <td>${d.pindah_dari || '-'} (No: ${d.no_pindah || '-'}, Tgl: ${d.tanggal_pindah || '-'})</td>
                                </tr>
                            `}
                            <tr>
                                <th>Status Verifikasi</th>
                                <td><span class="status-badge ${statusClass}">${statusLabel}</span></td>
                            </tr>
                        </table>

                        ${d.status === 'ditolak' ? `
                            <div class="rejection-note">
                                <h4>Catatan Penolakan:</h4>
                                <p>${d.verifikasi?.catatan ?? '-'}</p>
                            </div>
                        ` : ''}
                    </div>
                `;

                // Tombol verifikasi hanya jika status menunggu
                if (d.status === 'menunggu') {
                    html += `
                        <div class="action-banner">
                            <h4>Verifikasi Pendaftaran</h4>
                            <p>Tentukan apakah formulir pendaftaran ini diterima atau ditolak.</p>
                            <button id="btn-terima" class="btn-action btn-success">Terima</button>
                            <button id="btn-tolak" class="btn-action btn-danger">Tolak</button>
                        </div>
                    `;
                }

                detailContainer.innerHTML = html;

                // Pasang event listener jika tombol ada
                if (d.status === 'menunggu') {
                    document.getElementById('btn-terima').addEventListener('click', () => verifikasi('diterima'));
                    document.getElementById('btn-tolak').addEventListener('click', () => {
                        const catatan = prompt('Masukkan catatan penolakan:');
                        if (catatan !== null) verifikasi('ditolak', catatan);
                    });
                }
            } catch (error) {
                console.error(error);
                detailContainer.innerHTML = '<p>Terjadi kesalahan jaringan.</p>';
            }
        }

        async function verifikasi(hasil, catatan = '') {
            if (!catatan && hasil === 'ditolak') {
                alert('Catatan wajib diisi untuk penolakan.');
                return;
            }
            try {
                const res = await fetch('/api/verifikasi', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({
                        id_formulir: id,
                        hasil_verifikasi: hasil,
                        catatan: catatan
                    })
                });
                const r = await res.json();
                if (res.ok) {
                    alert(r.message || 'Verifikasi berhasil.');
                    location.reload();
                } else {
                    alert('Gagal: ' + (r.message || 'Terjadi kesalahan.'));
                }
            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan jaringan.');
            }
        }

        // Panggil load saat halaman siap
        load();
    </script>
</body>
</html>