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
        <h2>Daftar Pendaftar</h2>

        <!-- Tabel Ringkasan -->
        <table border="1" cellpadding="5" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pendaftar (User)</th>
                    <th>Email</th>
                    <th>Nama Lengkap Siswa</th>
                    <th>Tanggal Daftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-pendaftar">
                <!-- Data akan dimuat lewat JavaScript -->
                <tr>
                    <td colspan="6">Memuat data...</td>
                </tr>
            </tbody>
        </table>

        <br>

        <!-- Area Detail (akan muncul saat tombol Lihat diklik) -->
        <div id="detail-pendaftar" style="display:none;">
            <h3>Detail Formulir</h3>
            <button onclick="tutupDetail()">Tutup Detail</button>
            <hr>
            <div id="isi-detail">
                <!-- Konten akan diisi oleh JavaScript -->
            </div>
        </div>
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
            button.addEventListener('click', function () {
                const targetId = this.getAttribute('data-section');
                document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
                const target = document.getElementById(targetId);
                if (target) {
                    target.style.display = 'block';
                }

                // Tambahan: jika membuka Verifikasi Pendaftar, muat tabel
                if (targetId === 'verifikasi') {
                    loadDaftarPendaftar();
                }
            });
        });

        async function loadDaftarPendaftar() {
            const token = localStorage.getItem('access_token');
            if (!token) return;

            try {
                const response = await fetch('/api/pendaftaran', {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();

                const tbody = document.getElementById('tabel-pendaftar');
                if (!Array.isArray(data)) {
                    tbody.innerHTML = '<tr><td colspan="6">Gagal memuat data.</td></tr>';
                    return;
                }

                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6">Belum ada pendaftar.</td></tr>';
                    return;
                }

                let html = '';
                data.forEach((item, index) => {
                    html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.nama_pendaftar}</td>
                    <td>${item.email_pendaftar}</td>
                    <td>${item.nama_lengkap}</td>
                    <td>${item.tanggal_daftar}</td>
                    <td>
                        <button onclick="lihatDetail(${item.id})">Lihat</button>
                    </td>
                </tr>
            `;
                });
                tbody.innerHTML = html;
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('tabel-pendaftar').innerHTML = '<tr><td colspan="6">Terjadi kesalahan.</td></tr>';
            }
        }

        // Fungsi untuk menampilkan detail
        async function lihatDetail(id) {
            const token = localStorage.getItem('access_token');
            try {
                const response = await fetch(`/api/pendaftaran/${id}`, {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();
                const detail = result.data;

                // Sembunyikan tabel ringkasan (opsional) atau tampilkan di bawah
                document.getElementById('detail-pendaftar').style.display = 'block';

                // Bangun tampilan detail
                const container = document.getElementById('isi-detail');
                container.innerHTML = `
            <h4>Biodata Siswa</h4>
            <p>Nama Lengkap: ${detail.nama_lengkap}</p>
            <p>Tempat Lahir: ${detail.tempat_lahir}</p>
            <p>Tanggal Lahir: ${detail.tanggal_lahir}</p>
            <p>NIK: ${detail.nik}</p>
            <p>Agama: ${detail.agama}</p>
            <p>Warga Negara: ${detail.warga_negara}</p>
            <p>Anak ke-: ${detail.anak_ke || '-'}</p>
            <p>Jumlah Saudara: ${detail.jumlah_saudara || '-'}</p>
            <p>Alamat: ${detail.alamat_lengkap}</p>

            <h4>Data Orang Tua / Wali</h4>
            <p>Tipe: ${detail.tipe_wali}</p>
            ${detail.tipe_wali === 'orang_tua' ? `
                <p><strong>Ayah:</strong> ${detail.nama_ayah} (${detail.pekerjaan_ayah})</p>
                <p>Agama: ${detail.agama_ayah} | Pendidikan: ${detail.pendidikan_ayah}</p>
                <p>No KTP: ${detail.no_ktp_ayah} | Penghasilan: ${detail.penghasilan_ayah}</p>
                <p>Telp: ${detail.no_telp_ayah} | Alamat: ${detail.alamat_ayah}</p>
                <hr>
                <p><strong>Ibu:</strong> ${detail.nama_ibu} (${detail.pekerjaan_ibu})</p>
                <p>Agama: ${detail.agama_ibu} | Pendidikan: ${detail.pendidikan_ibu}</p>
                <p>No KTP: ${detail.no_ktp_ibu} | Penghasilan: ${detail.penghasilan_ibu}</p>
                <p>Telp: ${detail.no_telp_ibu} | Alamat: ${detail.alamat_ibu}</p>
            ` : `
                <p><strong>Wali:</strong> ${detail.nama_wali} (${detail.pekerjaan_wali})</p>
                <p>Agama: ${detail.agama_wali} | Pendidikan: ${detail.pendidikan_wali}</p>
                <p>No KTP: ${detail.no_ktp_wali} | Penghasilan: ${detail.penghasilan_wali}</p>
                <p>Telp: ${detail.no_telp_wali} | Alamat: ${detail.alamat_wali}</p>
            `}

            <h4>Data Akademik</h4>
            ${detail.is_bukan_pindahan === 1 ? `
                <p>Bukan murid pindahan</p>
            ` : `
                <p>Asal Sekolah: ${detail.asal_sekolah}</p>
                <p>No Ijazah: ${detail.no_ijazah} | Tahun: ${detail.tahun_ijazah}</p>
                <p>Diterima di Kelas: ${detail.diterima_kelas}</p>
                <p>Pindah dari: ${detail.pindah_dari} | No Pindah: ${detail.no_pindah}</p>
                <p>Tanggal Pindah: ${detail.tanggal_pindah}</p>
            `}
        `;
            } catch (error) {
                alert('Gagal memuat detail');
            }
        }

        function tutupDetail() {
            document.getElementById('detail-pendaftar').style.display = 'none';
        }

        // Logout
        document.getElementById('logoutButton').addEventListener('click', async function () {
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