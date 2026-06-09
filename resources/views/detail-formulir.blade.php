<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Formulir #{{ $id }}</title>
    @include('partials.alert-helper')
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
        // Helper yang aman untuk escape HTML (mencegah XSS)
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

                // Escape semua data yang akan ditampilkan
                const namaLengkap = escapeHtml(d.nama_lengkap);
                const tempatLahir = escapeHtml(d.tempat_lahir);
                const tanggalLahir = escapeHtml(d.tanggal_lahir);
                const nik = escapeHtml(d.nik);
                const agama = escapeHtml(d.agama);
                const wargaNegara = escapeHtml(d.warga_negara);
                const anakKe = escapeHtml(String(d.anak_ke || '-'));
                const jumlahSaudara = escapeHtml(String(d.jumlah_saudara || '-'));
                const alamatLengkap = escapeHtml(d.alamat_lengkap);

                // Data orang tua / wali
                const namaAyah = escapeHtml(d.nama_ayah || '-');
                const pekerjaanAyah = escapeHtml(d.pekerjaan_ayah || '-');
                const agamaAyah = escapeHtml(d.agama_ayah || '-');
                const pendidikanAyah = escapeHtml(d.pendidikan_ayah || '-');
                const noKtpAyah = escapeHtml(d.no_ktp_ayah || '-');
                const penghasilanAyah = escapeHtml(d.penghasilan_ayah || '-');
                const noTelpAyah = escapeHtml(d.no_telp_ayah || '-');
                const alamatAyah = escapeHtml(d.alamat_ayah || '-');

                const namaIbu = escapeHtml(d.nama_ibu || '-');
                const pekerjaanIbu = escapeHtml(d.pekerjaan_ibu || '-');
                const agamaIbu = escapeHtml(d.agama_ibu || '-');
                const pendidikanIbu = escapeHtml(d.pendidikan_ibu || '-');
                const noKtpIbu = escapeHtml(d.no_ktp_ibu || '-');
                const penghasilanIbu = escapeHtml(d.penghasilan_ibu || '-');
                const noTelpIbu = escapeHtml(d.no_telp_ibu || '-');
                const alamatIbu = escapeHtml(d.alamat_ibu || '-');

                const namaWali = escapeHtml(d.nama_wali || '-');
                const pekerjaanWali = escapeHtml(d.pekerjaan_wali || '-');
                const agamaWali = escapeHtml(d.agama_wali || '-');
                const pendidikanWali = escapeHtml(d.pendidikan_wali || '-');
                const noKtpWali = escapeHtml(d.no_ktp_wali || '-');
                const penghasilanWali = escapeHtml(d.penghasilan_wali || '-');
                const noTelpWali = escapeHtml(d.no_telp_wali || '-');
                const alamatWali = escapeHtml(d.alamat_wali || '-');

                // Data akademik (jika pindahan)
                const asalSekolah = escapeHtml(d.asal_sekolah || '-');
                const noIjazah = escapeHtml(d.no_ijazah || '-');
                const tahunIjazah = escapeHtml(d.tahun_ijazah || '-');
                const diterimaKelas = escapeHtml(d.diterima_kelas || '-');
                const pindahDari = escapeHtml(d.pindah_dari || '-');
                const noPindah = escapeHtml(d.no_pindah || '-');
                const tanggalPindah = escapeHtml(d.tanggal_pindah || '-');

                // Catatan verifikasi
                const catatanVerifikasi = escapeHtml(d.verifikasi?.catatan ?? '-');

                let html = `
                    <!-- 1. BIODATA SISWA -->
                    <div class="section">
                        <h3>Biodata Siswa</h3>
                        <table>
                            <tr><th>Nama Lengkap</th><td>${namaLengkap}</td></tr>
                            <tr><th>Tempat, Tanggal Lahir</th><td>${tempatLahir}, ${tanggalLahir}</td></tr>
                            <tr><th>NIK</th><td>${nik}</td></tr>
                            <tr><th>Agama</th><td>${agama}</td></tr>
                            <tr><th>Kewarganegaraan</th><td>${wargaNegara}</td></tr>
                            <tr><th>Anak Ke / Jumlah Saudara</th><td>Anak ke-${anakKe} dari ${jumlahSaudara} bersaudara</td></tr>
                            <tr><th>Alamat Lengkap</th><td>${alamatLengkap}</td></tr>
                        </table>
                    </div>

                    <!-- 2. DATA ORANG TUA / WALI -->
                    <div class="section">
                        <h3>Data ${d.tipe_wali === 'orang_tua' ? 'Orang Tua' : 'Wali'}</h3>
                `;

                if (d.tipe_wali === 'orang_tua') {
                    html += `
                            <div class="sub-section-title">Data Ayah Kandung</div>
                            <table>
                                <tr><th>Nama Ayah</th><td>${namaAyah}</td></tr>
                                <tr><th>Pekerjaan Ayah</th><td>${pekerjaanAyah}</td></tr>
                                <tr><th>Agama Ayah</th><td>${agamaAyah}</td></tr>
                                <tr><th>Pendidikan Ayah</th><td>${pendidikanAyah}</td></tr>
                                <tr><th>No. KTP / NIK Ayah</th><td>${noKtpAyah}</td></tr>
                                <tr><th>Penghasilan Ayah</th><td>${penghasilanAyah}</td></tr>
                                <tr><th>No. Telepon / WA</th><td>${noTelpAyah}</td></tr>
                                <tr><th>Alamat Ayah</th><td>${alamatAyah}</td></tr>
                            </table>

                            <div class="sub-section-title" style="margin-top:20px;">Data Ibu Kandung</div>
                            <table>
                                <tr><th>Nama Ibu</th><td>${namaIbu}</td></tr>
                                <tr><th>Pekerjaan Ibu</th><td>${pekerjaanIbu}</td></tr>
                                <tr><th>Agama Ibu</th><td>${agamaIbu}</td></tr>
                                <tr><th>Pendidikan Ibu</th><td>${pendidikanIbu}</td></tr>
                                <tr><th>No. KTP / NIK Ibu</th><td>${noKtpIbu}</td></tr>
                                <tr><th>Penghasilan Ibu</th><td>${penghasilanIbu}</td></tr>
                                <tr><th>No. Telepon / WA</th><td>${noTelpIbu}</td></tr>
                                <tr><th>Alamat Ibu</th><td>${alamatIbu}</td></tr>
                            </table>
                    `;
                } else {
                    html += `
                            <div class="sub-section-title">Data Wali</div>
                            <table>
                                <tr><th>Nama Wali</th><td>${namaWali}</td></tr>
                                <tr><th>Pekerjaan Wali</th><td>${pekerjaanWali}</td></tr>
                                <tr><th>Agama Wali</th><td>${agamaWali}</td></tr>
                                <tr><th>Pendidikan Wali</th><td>${pendidikanWali}</td></tr>
                                <tr><th>No. KTP / NIK Wali</th><td>${noKtpWali}</td></tr>
                                <tr><th>Penghasilan Wali</th><td>${penghasilanWali}</td></tr>
                                <tr><th>No. Telepon / WA</th><td>${noTelpWali}</td></tr>
                                <tr><th>Alamat Wali</th><td>${alamatWali}</td></tr>
                            </table>
                    `;
                }

                html += `
                    </div>

                    <!-- 3. AKADEMIS & STATUS -->
                    <div class="section">
                        <h3>Akademik & Status</h3>
                        <table>
                `;

                if (d.is_bukan_pindahan) {
                    html += `<tr><th>Status Siswa</th><td>Siswa Baru (Bukan Pindahan)</td></tr>`;
                } else {
                    html += `
                            <tr><th>Status Siswa</th><td>Siswa Pindahan</td></tr>
                            <tr><th>Asal Sekolah</th><td>${asalSekolah}</td></tr>
                            <tr><th>No. Ijazah & Tahun Ijazah</th><td>${noIjazah} (${tahunIjazah})</td></tr>
                            <tr><th>Diterima di Kelas</th><td>${diterimaKelas}</td></tr>
                            <tr><th>Pindah dari / No & Tgl Surat</th><td>${pindahDari} (No: ${noPindah}, Tgl: ${tanggalPindah})</td></tr>
                    `;
                }

                html += `
                            <tr><th>Status Verifikasi</th>
                                <td><span class="status-badge ${statusClass}">${statusLabel}</span>
                            </td>
                        </table>
                `;

                if (d.status === 'ditolak') {
                    html += `
                        <div class="rejection-note">
                            <h4>Catatan Penolakan:</h4>
                            <p>${catatanVerifikasi}</p>
                        </div>
                    `;
                }

                html += `</div>`;

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
                        // Gunakan SweetAlert atau prompt biasa, pastikan catatan di-escape nanti
                        Swal.fire({
                            title: 'Tolak Formulir',
                            input: 'textarea',
                            inputLabel: 'Catatan Penolakan',
                            inputPlaceholder: 'Masukkan alasan penolakan...',
                            showCancelButton: true,
                            confirmButtonText: 'Tolak',
                            cancelButtonText: 'Batal',
                            customClass: {
                                popup: 'custom-swal-popup',
                                confirmButton: 'custom-swal-confirm',
                                cancelButton: 'custom-swal-cancel'
                            },
                            buttonsStyling: false
                        }).then((result) => {
                            if (result.isConfirmed && result.value) {
                                verifikasi('ditolak', result.value);
                            } else if (result.isConfirmed && !result.value) {
                                Swal.fire('Catatan wajib diisi', '', 'error');
                            }
                        });
                    });
                }
            } catch (error) {
                console.error(error);
                detailContainer.innerHTML = '<p>Terjadi kesalahan jaringan.</p>';
            }
        }

        async function verifikasi(hasil, catatan = '') {
            if (hasil === 'ditolak' && (!catatan || catatan.trim() === '')) {
                Swal.fire('Catatan wajib diisi', 'Berikan alasan penolakan.', 'error');
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
                    Swal.fire({
                        title: 'Berhasil',
                        text: r.message || 'Verifikasi berhasil.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'custom-swal-popup',
                            title: 'custom-swal-title',
                            htmlContainer: 'custom-swal-html',
                            confirmButton: 'custom-swal-confirm'
                        },
                        buttonsStyling: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal', r.message || 'Terjadi kesalahan.', 'error');
                }
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
            }
        }

        // Panggil load saat halaman siap
        load();
    </script>
</body>
</html>