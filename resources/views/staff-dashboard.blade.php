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
        <button data-section="metode-pembayaran">Metode Pembayaran</button>
        <button data-section="verifikasi-pembayaran">Verifikasi Pembayaran</button>
        <button data-section="kelola-jadwal">Kelola Jadwal</button>
        <button data-section="penilaian">Penilaian</button>
        <button data-section="laporan">Laporan</button>
    </div>
    <hr>

    <!-- Konten statis sementara -->
    <div id="beranda-staff" class="section">
        <p>Ini Beranda Staff</p>
    </div>
    <div id="verifikasi" class="section" style="display:none;">
        <h2>Verifikasi Pendaftar</h2>
        <h3>Menunggu Verifikasi</h3>
        <table border="1" cellpadding="5" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Nama Siswa</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-menunggu"></tbody>
        </table>
        <br>
        <h3>Sudah Diverifikasi</h3>
        <table border="1" cellpadding="5" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Nama Siswa</th>
                    <th>Hasil</th>
                    <th>Detail</th>
                </tr>
            </thead>
            <tbody id="tabel-sudah"></tbody>
        </table>
    </div>

    <div id="metode-pembayaran" class="section" style="display:none;">
        <h2>Metode Pembayaran</h2>
        <button onclick="tampilkanFormMetode()">Tambah Metode</button>

        <!-- Form Tambah -->
        <div id="form-metode" style="display:none;">
            <form id="formMetode" enctype="multipart/form-data">
                <input type="text" name="nama_bank" placeholder="Nama Bank"><br>
                <input type="text" name="nomor_rekening" placeholder="Nomor Rekening"><br>
                <input type="text" name="atas_nama" placeholder="Atas Nama"><br>
                <input type="file" name="gambar_qris" accept="image/*"><br>
                <textarea name="keterangan" placeholder="Keterangan"></textarea><br>
                <button type="submit">Simpan</button>
            </form>
        </div>

        <!-- Form Edit -->
        <div id="form-edit-metode" style="display:none;">
            <h3>Edit Metode</h3>
            <form id="formEditMetode" enctype="multipart/form-data">
                <input type="hidden" id="edit_id">
                <input type="text" name="nama_bank" id="edit_nama_bank" placeholder="Nama Bank"><br>
                <input type="text" name="nomor_rekening" id="edit_nomor_rekening" placeholder="Nomor Rekening"><br>
                <input type="text" name="atas_nama" id="edit_atas_nama" placeholder="Atas Nama"><br>
                <input type="file" name="gambar_qris" id="edit_gambar_qris" accept="image/*"><br>
                <label>Gambar Saat Ini:</label><br>
                <img id="edit_preview_gambar" width="100" style="display:none;"><br>
                <textarea name="keterangan" id="edit_keterangan" placeholder="Keterangan"></textarea><br>
                <button type="submit">Update</button>
                <button type="button" onclick="batalEdit()">Batal</button>
            </form>
        </div>

        <hr>
        <div id="daftar-metode">Memuat...</div>
    </div>

    <div id="verifikasi-pembayaran" class="section" style="display:none;">
        <h2>Verifikasi Bukti Pembayaran</h2>
        <table border="1" id="tabel-bukti" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Pendaftar</th>
                    <th>Jenis</th>
                    <th>Bukti</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div id="aksi-verifikasi" style="display:none;">
            <h3>Verifikasi</h3>
            <form id="formVerifikasi" enctype="multipart/form-data">
                <input type="hidden" id="bukti_id">
                <img id="preview-bukti" width="100"><br>
                <label>Hasil:</label>
                <select id="hasil_verifikasi" name="hasil_verifikasi">
                    <option value="diterima">Terima</option>
                    <option value="ditolak">Tolak</option>
                </select><br>
                <div id="div-catatan" style="display:none;">
                    <label>Catatan Penolakan:</label>
                    <textarea id="catatan" name="catatan"></textarea><br>
                </div>
                <div id="div-kwitansi">
                    <label>Upload Kwitansi (hanya jika diterima):</label>
                    <input type="file" id="kwitansi" name="kwitansi" accept=".pdf,.jpg,.png"><br>
                </div>
                <button type="submit">Kirim Verifikasi</button>
            </form>
        </div>
    </div>

    <div id="kelola-jadwal" class="section" style="display:none;">
        <h2>Kelola Jadwal Tes</h2>

        <!-- Tabel 1: Belum Terjadwal -->
        <h3>Pendaftar Belum Terjadwal</h3>
        <table border="1" width="100%" cellpadding="5">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-belum-terjadwal"></tbody>
        </table>

        <!-- Tabel 2: Sudah Terjadwal -->
        <h3>Pendaftar Sudah Terjadwal</h3>
        <table border="1" width="100%" cellpadding="5">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Jadwal Tes</th>
                    <th>Penjadwal</th>
                    <th>Status Kelulusan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-sudah-terjadwal"></tbody>
        </table>

        <div id="form-jadwal-container"
            style="display:none; border:1px solid #ccc; padding:10px; margin-top:10px; background:#f9f9f9;">
            <h3 id="form-jadwal-title">Atur Jadwal Tes</h3>
            <input type="hidden" id="jadwal-action" value="new"> <!-- new / update -->
            <input type="hidden" id="jadwal-id" value=""> <!-- id_pendaftar atau id_seleksi_tes -->
            <input type="hidden" id="jadwal-nama" value="">
            <label>Pendaftar: <span id="jadwal-nama-tampil"></span></label><br><br>
            <label>Jadwal Tes:</label>
            <input type="datetime-local" id="jadwal-datetime" style="width:200px;"><br><br>
            <button onclick="submitJadwal()">Simpan</button>
            <button onclick="tutupFormJadwal()">Batal</button>
        </div>
    </div>
    <div id="penilaian" class="section" style="display:none;">
        <h2>Input Penilaian</h2>
        <select id="pilih-pendaftar-nilai"></select>
        <br><br>
        <input type="text" id="membaca" placeholder="Kemampuan Membaca"><br>
        <input type="text" id="menulis" placeholder="Kemampuan Menulis"><br>
        <input type="text" id="berhitung" placeholder="Kemampuan Berhitung"><br>
        <input type="text" id="baca_quran" placeholder="Baca Alquran"><br>
        <textarea id="catatan_nilai" placeholder="Catatan"></textarea><br>
        <label>Kelulusan:</label>
        <select id="kelulusan_tes">
            <option value="lulus">Lulus</option>
            <option value="tidak_lulus">Tidak Lulus</option>
        </select><br><br>
        <button onclick="simpanPenilaian()">Simpan Penilaian</button>

        <h3>Daftar Penilaian</h3>
        <table border="1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Membaca</th>
                    <th>Menulis</th>
                    <th>Berhitung</th>
                    <th>Alquran</th>
                    <th>Kelulusan</th>
                </tr>
            </thead>
            <tbody id="daftar-penilaian"></tbody>
        </table>
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
        // Navigasi antar section
        document.querySelectorAll('button[data-section]').forEach(button => {
            button.addEventListener('click', function () {
                const targetId = this.getAttribute('data-section');
                document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
                const target = document.getElementById(targetId);
                if (target) {
                    target.style.display = 'block';
                }

                // Muat data sesuai section
                if (targetId === 'verifikasi') {
                    loadVerifikasi();
                } else if (targetId === 'metode-pembayaran') {
                    loadMetodePembayaran();
                } else if (targetId === 'verifikasi-pembayaran') {
                    loadBuktiPembayaran();
                } else if (targetId === 'kelola-jadwal') {
                    loadBelumTerjadwal();
                    loadSudahTerjadwal();
                } else if (targetId === 'penilaian') {
                    loadPendaftarDinilai();
                    loadDaftarPenilaian();
                }
            });
        });

        // Load daftar metode
        async function loadMetodePembayaran() {
            try {
                const res = await fetch('/api/metode-pembayaran', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const data = await res.json();
                let html = '';
                data.forEach(m => {
                    html += `<div>
                        <strong>${m.nama_bank || 'QRIS'}</strong><br>
                        No: ${m.nomor_rekening || '-'}<br>
                        Atas Nama: ${m.atas_nama || '-'}<br>
                        ${m.gambar_qris ? `<img src="/storage/${m.gambar_qris}" width="100"><br>` : ''}
                        <button onclick="editMetode(${m.id}, '${m.nama_bank || ''}', '${m.nomor_rekening || ''}', '${m.atas_nama || ''}', '${m.keterangan || ''}', '${m.gambar_qris || ''}')">Edit</button>
                        <button onclick="hapusMetode(${m.id})">Hapus</button>
                        <hr></div>`;
                });
                document.getElementById('daftar-metode').innerHTML = html || 'Belum ada metode.';
            } catch (err) {
                console.error(err);
            }
        }

        function tampilkanFormMetode() {
            document.getElementById('form-metode').style.display = 'block';
            document.getElementById('form-edit-metode').style.display = 'none';
        }

        // Submit form metode
        document.getElementById('formMetode').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const res = await fetch('/api/metode-pembayaran', {
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + token },
                    body: formData
                });
                if (res.ok) {
                    alert('Metode disimpan!');
                    this.reset();
                    document.getElementById('form-metode').style.display = 'none';
                    loadMetodePembayaran();
                } else {
                    const err = await res.json();
                    alert('Gagal: ' + JSON.stringify(err));
                }
            } catch (error) {
                console.error(error);
            }
        });

        function editMetode(id, nama_bank, nomor_rekening, atas_nama, keterangan, gambar_qris) {
            document.getElementById('form-metode').style.display = 'none';
            document.getElementById('form-edit-metode').style.display = 'block';
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nama_bank').value = nama_bank;
            document.getElementById('edit_nomor_rekening').value = nomor_rekening;
            document.getElementById('edit_atas_nama').value = atas_nama;
            document.getElementById('edit_keterangan').value = keterangan;
            const preview = document.getElementById('edit_preview_gambar');
            if (gambar_qris) {
                preview.src = '/storage/' + gambar_qris;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        }

        function batalEdit() {
            document.getElementById('form-edit-metode').style.display = 'none';
        }

        document.getElementById('formEditMetode').addEventListener('submit', async function (e) {
            e.preventDefault();
            const id = document.getElementById('edit_id').value;
            const formData = new FormData(this);
            try {
                const res = await fetch(`/api/metode-pembayaran/${id}`, {
                    method: 'PUT',  // Penting! Gunakan PUT untuk update
                    headers: { 'Authorization': 'Bearer ' + token },
                    body: formData
                });
                if (res.ok) {
                    alert('Metode diperbarui!');
                    batalEdit();
                    loadMetodePembayaran();
                } else {
                    const err = await res.json();
                    alert('Gagal update: ' + JSON.stringify(err));
                }
            } catch (error) {
                console.error(error);
            }
        });

        async function hapusMetode(id) {
            if (!confirm('Yakin ingin menghapus?')) return;
            try {
                const res = await fetch(`/api/metode-pembayaran/${id}`, {
                    method: 'DELETE',
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                if (res.ok) {
                    alert('Metode dihapus.');
                    loadMetodePembayaran();
                } else {
                    alert('Gagal menghapus.');
                }
            } catch (error) {
                console.error(error);
            }
        }

        // Load semua bukti pembayaran untuk staf
        async function loadBuktiPembayaran() {
            const res = await fetch('/api/bukti-pembayaran/semua', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const data = await res.json();
            let html = '';
            data.forEach((b, i) => {
                html += `<tr>
            <td>${i + 1}</td>
            <td>${b.pendaftar?.name ?? '-'}</td>
            <td>${b.jenis_pembayaran}</td>
            <td><a href="/storage/${b.bukti_pembayaran}" target="_blank">Lihat</a></td>
            <td>${b.status}</td>
            <td>
                ${b.status === 'menunggu' ? `<button onclick="verifikasiBukti(${b.id_bukti_pembayaran}, '${b.bukti_pembayaran}')">Verifikasi</button>` : ''}
            </td>
        </tr>`;
            });
            document.querySelector('#tabel-bukti tbody').innerHTML = html || '<tr><td colspan="6">Tidak ada data.</td></tr>';
        }

        function verifikasiBukti(id, path) {
            document.getElementById('aksi-verifikasi').style.display = 'block';
            document.getElementById('bukti_id').value = id;
            document.getElementById('preview-bukti').src = `/storage/${path}`;
            document.getElementById('hasil_verifikasi').value = 'diterima';
            document.getElementById('div-catatan').style.display = 'none';
            document.getElementById('div-kwitansi').style.display = 'block';
        }

        document.getElementById('hasil_verifikasi').addEventListener('change', function () {
            document.getElementById('div-catatan').style.display = this.value === 'ditolak' ? 'block' : 'none';
            document.getElementById('div-kwitansi').style.display = this.value === 'diterima' ? 'block' : 'none';
        });

        document.getElementById('formVerifikasi').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData();
            formData.append('id_bukti_pembayaran', document.getElementById('bukti_id').value);
            formData.append('hasil_verifikasi', document.getElementById('hasil_verifikasi').value);
            formData.append('catatan', document.getElementById('catatan').value);
            if (document.getElementById('hasil_verifikasi').value === 'diterima') {
                const fileInput = document.getElementById('kwitansi');
                if (fileInput.files.length > 0) {
                    formData.append('kwitansi', fileInput.files[0]);
                }
            }
            const res = await fetch('/api/verifikasi-pembayaran', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token },
                body: formData
            });
            if (res.ok) {
                alert('Verifikasi berhasil.');
                loadBuktiPembayaran();
                document.getElementById('aksi-verifikasi').style.display = 'none';
            } else {
                alert('Gagal melakukan verifikasi.');
            }
        });

        async function loadVerifikasi() {
            const res = await fetch('/api/pendaftaran', { headers: { 'Authorization': 'Bearer ' + token } });
            const data = await res.json();

            let menungguHtml = '';
            let sudahHtml = '';
            data.forEach((item, i) => {
                const baris = `<tr>
            <td>${i + 1}</td>
            <td>${item.nama_pendaftar}</td>
            <td>${item.nama_lengkap}</td>
            <td>
                <a href="/formulir/${item.id}" target="_blank">Lihat Detail</a>
            </td>
        </tr>`;
                if (item.status === 'menunggu') {
                    menungguHtml += baris;
                } else {
                    sudahHtml += `<tr>
                <td>${i + 1}</td>
                <td>${item.nama_pendaftar}</td>
                <td>${item.nama_lengkap}</td>
                <td>${item.status}</td>
                <td><a href="/formulir/${item.id}" target="_blank">Detail</a></td>
            </tr>`;
                }
            });
            document.getElementById('tabel-menunggu').innerHTML = menungguHtml || '<tr><td colspan="4">Tidak ada</td></tr>';
            document.getElementById('tabel-sudah').innerHTML = sudahHtml || '<tr><td colspan="5">Tidak ada</td></tr>';
        }

        // kelola jadwal
        async function loadBelumTerjadwal() {
            const res = await fetch('/api/seleksi/eligible', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const data = await res.json();
            let html = '';
            data.forEach((item, i) => {
                html += `<tr>
            <td>${i + 1}</td>
            <td>${item.name}</td>
            <td>${item.email}</td>
            <td><button onclick="aturJadwalBaru('${item.id}', '${item.name}')">📅 Atur Jadwal</button></td>
        </tr>`;
            });
            document.getElementById('tabel-belum-terjadwal').innerHTML = html || '<tr><td colspan="4">Tidak ada pendaftar yang eligible.</td></tr>';
        }

        async function loadSudahTerjadwal() {
            const res = await fetch('/api/seleksi', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const data = await res.json();
            let html = '';
            data.forEach((item, i) => {
                html += `<tr>
            <td>${i + 1}</td>
            <td>${item.pendaftar?.name ?? '-'}</td>
            <td>${item.jadwal_tes}</td>
            <td>${item.penjadwal?.name ?? '-'}</td>
            <td>${item.kelulusan_tes ?? '-'}</td>
            <td><button onclick="ubahJadwal('${item.id_seleksi_tes}', '${item.jadwal_tes}', '${item.pendaftar?.name}')">✏️ Ubah Jadwal</button></td>
        </tr>`;
            });
            document.getElementById('tabel-sudah-terjadwal').innerHTML = html || '<tr><td colspan="6">Belum ada jadwal.</td></tr>';
        }

        function aturJadwalBaru(id_pendaftar, nama) {
            document.getElementById('form-jadwal-title').innerText = 'Atur Jadwal Baru';
            document.getElementById('jadwal-action').value = 'new';
            document.getElementById('jadwal-id').value = id_pendaftar;
            document.getElementById('jadwal-nama').value = nama;
            document.getElementById('jadwal-nama-tampil').innerText = nama;
            document.getElementById('jadwal-datetime').value = ''; // kosongkan
            document.getElementById('form-jadwal-container').style.display = 'block';
        }

        function ubahJadwal(id_seleksi_tes, jadwalLama, nama) {
            document.getElementById('form-jadwal-title').innerText = 'Ubah Jadwal';
            document.getElementById('jadwal-action').value = 'update';
            document.getElementById('jadwal-id').value = id_seleksi_tes;
            document.getElementById('jadwal-nama').value = nama;
            document.getElementById('jadwal-nama-tampil').innerText = nama;
            // Konversi format "YYYY-MM-DD HH:MM:SS" ke "YYYY-MM-DDTHH:MM" untuk datetime-local
            let datetimeValue = '';
            if (jadwalLama && jadwalLama.includes(' ')) {
                let [date, time] = jadwalLama.split(' ');
                datetimeValue = date + 'T' + time.substring(0, 5); // ambil HH:MM
            }
            document.getElementById('jadwal-datetime').value = datetimeValue;
            document.getElementById('form-jadwal-container').style.display = 'block';
        }

        function tutupFormJadwal() {
            document.getElementById('form-jadwal-container').style.display = 'none';
        }

        async function submitJadwal() {
            const action = document.getElementById('jadwal-action').value;
            const datetimeLocal = document.getElementById('jadwal-datetime').value;
            if (!datetimeLocal) {
                alert('Harap pilih tanggal dan jam tes!');
                return;
            }
            // Konversi datetime-local (YYYY-MM-DDTHH:MM) ke format database (YYYY-MM-DD HH:MM:00)
            const jadwal_tes = datetimeLocal.replace('T', ' ') + ':00';

            if (action === 'new') {
                const id_pendaftar = document.getElementById('jadwal-id').value;
                const res = await fetch('/api/seleksi', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                    body: JSON.stringify({ id_pendaftar, jadwal_tes })
                });
                if (res.ok) {
                    alert('Jadwal berhasil disimpan');
                    tutupFormJadwal();
                    loadBelumTerjadwal();
                    loadSudahTerjadwal();
                } else {
                    const err = await res.json();
                    alert('Gagal: ' + (err.message || JSON.stringify(err)));
                }
            } else if (action === 'update') {
                const id_seleksi_tes = document.getElementById('jadwal-id').value;
                const res = await fetch(`/api/seleksi/${id_seleksi_tes}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                    body: JSON.stringify({ jadwal_tes })
                });
                if (res.ok) {
                    alert('Jadwal berhasil diubah');
                    tutupFormJadwal();
                    loadSudahTerjadwal();
                    loadBelumTerjadwal();
                } else {
                    const err = await res.json();
                    alert('Gagal mengubah: ' + (err.message || JSON.stringify(err)));
                }
            }
        }

        // penilaian
        async function loadPendaftarDinilai() {
            // Ambil pendaftar yang sudah dijadwalkan tapi belum dinilai
            const res = await fetch('/api/seleksi', { headers: { 'Authorization': 'Bearer ' + token } });
            const data = await res.json();
            const belumDinilai = data.filter(d => d.id_penilaian == null);
            let html = '<option value="">-- Pilih --</option>';
            belumDinilai.forEach(d => {
                html += `<option value="${d.id_pendaftar}">${d.pendaftar?.name}</option>`;
            });
            document.getElementById('pilih-pendaftar-nilai').innerHTML = html;
        }

        async function simpanPenilaian() {
            const id_pendaftar = document.getElementById('pilih-pendaftar-nilai').value;
            const payload = {
                id_pendaftar,
                kemampuan_membaca: document.getElementById('membaca').value,
                kemampuan_menulis: document.getElementById('menulis').value,
                kemampuan_berhitung: document.getElementById('berhitung').value,
                baca_alquran: document.getElementById('baca_quran').value,
                catatan: document.getElementById('catatan_nilai').value,
                kelulusan_tes: document.getElementById('kelulusan_tes').value,
            };
            const res = await fetch('/api/penilaian', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify(payload)
            });
            if (res.ok) { alert('Berhasil'); loadPendaftarDinilai(); loadDaftarPenilaian(); }
        }

        async function loadDaftarPenilaian() {
            const res = await fetch('/api/penilaian', { headers: { 'Authorization': 'Bearer ' + token } });
            const data = await res.json();
            let html = '';
            data.forEach((n, i) => {
                html += `<tr>
            <td>${i + 1}</td>
            <td>${n.pendaftar?.name}</td>
            <td>${n.kemampuan_membaca}</td>
            <td>${n.kemampuan_menulis}</td>
            <td>${n.kemampuan_berhitung}</td>
            <td>${n.baca_alquran ?? '-'}</td>
            <td>${n.seleksi_tes?.kelulusan_tes}</td>
        </tr>`;
            });
            document.querySelector('#daftar-penilaian').innerHTML = html;
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