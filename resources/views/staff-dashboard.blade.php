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

    <!-- Navigasi akan diisi dinamis oleh JavaScript -->
    <div id="nav-container"></div>
    <hr>

    <!-- Semua konten section (tetap seperti semula, tapi beberapa mungkin tidak akan pernah diakses) -->
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

        <!-- Tombol switch / tab -->
        <div>
            <button id="tabFormulir" class="tab-active" onclick="switchJenisPembayaran('formulir')">Pembayaran
                Formulir</button>
            <button id="tabMasuk" onclick="switchJenisPembayaran('masuk')">Pembayaran Masuk (Daftar Ulang)</button>
        </div>
        <br>

        <!-- Container untuk tabel formulir -->
        <div id="container-formulir">
            <h3>Menunggu Verifikasi</h3>
            <table border="1" width="100%" id="tabel-formulir-menunggu">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pendaftar</th>
                        <th>Jenis</th>
                        <th>Bukti</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <br>
            <h3>Sudah Diverifikasi</h3>
            <table border="1" width="100%" id="tabel-formulir-sudah">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pendaftar</th>
                        <th>Jenis</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th>Catatan / Kwitansi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- Container untuk tabel masuk (daftar ulang) -->
        <div id="container-masuk" style="display:none;">
            <h3>Menunggu Verifikasi</h3>
            <table border="1" width="100%" id="tabel-masuk-menunggu">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pendaftar</th>
                        <th>Jenis</th>
                        <th>Bukti</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <br>
            <h3>Sudah Diverifikasi</h3>
            <table border="1" width="100%" id="tabel-masuk-sudah">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pendaftar</th>
                        <th>Jenis</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th>Catatan / Kwitansi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- Modal untuk verifikasi -->
        <div id="modalVerifikasi"
            style="display:none; position:fixed; top:10%; left:10%; width:80%; background:white; border:2px solid #ccc; padding:20px; z-index:1000;">
            <h3>Verifikasi Bukti Pembayaran</h3>
            <div id="modalContent">
                <img id="modalGambar" src="" style="max-width:100%; max-height:300px;"><br>
                <button onclick="bukaGambarFull()">Lihat Gambar Full</button>
                <br><br>
                <label>Hasil Verifikasi:</label><br>
                <select id="modalHasil">
                    <option value="diterima">Terima</option>
                    <option value="ditolak">Tolak</option>
                </select>
                <br>
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
        <div id="overlay"
            style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;">
        </div>
    </div>

    <div id="kelola-jadwal" class="section" style="display:none;">
        <h2>Kelola Jadwal Tes</h2>
        <h3>Pendaftar Belum Terjadwal</h3>
        <table border="1" width="100%">
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
        <h3>Pendaftar Sudah Terjadwal</h3>
        <table border="1" width="100%">
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
        <div id="form-jadwal-container" style="display:none;"> ... </div>
    </div>

    <div id="penilaian" class="section" style="display:none;">
        <h2>Penilaian Tes</h2>

        <!-- Tabel 1: Pendaftar yang sudah dijadwalkan & belum dinilai -->
        <h3>Pendaftar Menunggu Penilaian</h3>
        <table border="1" width="100%" cellpadding="5">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pendaftar</th>
                    <th>Jadwal Tes</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-belum-dinilai"></tbody>
        </table>

        <!-- Tabel 2: Riwayat Penilaian (sudah dinilai) -->
        <h3>Riwayat Penilaian</h3>
        <table border="1" width="100%" cellpadding="5">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pendaftar</th>
                    <th>Membaca</th>
                    <th>Menulis</th>
                    <th>Berhitung</th>
                    <th>Baca Alquran</th>
                    <th>Kelulusan</th>
                    <th>Penilai</th>
                </tr>
            </thead>
            <tbody id="tabel-riwayat-penilaian"></tbody>
        </table>

        <!-- Modal Input Penilaian -->
        <div id="modalPenilaian"
            style="display:none; position:fixed; top:10%; left:10%; width:80%; background:white; border:2px solid #ccc; padding:20px; z-index:1000;">
            <h3>Input Penilaian</h3>
            <input type="hidden" id="modalIdPendaftar">
            <p><strong>Pendaftar: <span id="modalNamaPendaftar"></span></strong></p>
            <label>Kemampuan Membaca:</label><br>
            <input type="text" id="modalMembaca" placeholder="Kemampuan Membaca"><br><br>
            <label>Kemampuan Menulis:</label><br>
            <input type="text" id="modalMenulis" placeholder="Kemampuan Menulis"><br><br>
            <label>Kemampuan Berhitung:</label><br>
            <input type="text" id="modalBerhitung" placeholder="Kemampuan Berhitung"><br><br>
            <label>Baca Alquran:</label><br>
            <input type="text" id="modalBacaQuran" placeholder="Baca Alquran"><br><br>
            <label>Catatan:</label><br>
            <textarea id="modalCatatan" rows="3" cols="40"></textarea><br><br>
            <label>Kelulusan:</label><br>
            <select id="modalKelulusan">
                <option value="lulus">Lulus</option>
                <option value="tidak_lulus">Tidak Lulus</option>
            </select><br><br>
            <button onclick="simpanPenilaianModal()">Simpan Penilaian</button>
            <button onclick="tutupModalPenilaian()">Batal</button>
        </div>
        <div id="overlayPenilaian"
            style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;">
        </div>
    </div>

    <!-- Verifikasi Daftar Ulang -->
    <div id="verifikasi-daftar-ulang" class="section" style="display:none;">
        <h2>Verifikasi Daftar Ulang</h2>
        <table border="1" width="100%" cellpadding="8">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pendaftar</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-daftar-ulang"></tbody>
        </table>

        <!-- Modal Verifikasi & Lihat Berkas -->
        <div id="modalDaftarUlang"
            style="display:none; position:fixed; top:10%; left:10%; width:80%; background:white; border:2px solid #ccc; padding:20px; z-index:1000; overflow-y:auto; max-height:80%;">
            <h3>Verifikasi Daftar Ulang</h3>
            <input type="hidden" id="du-id">
            <p><strong>Pendaftar: <span id="du-nama"></span></strong></p>
            <hr>
            <h4>Berkas Persyaratan:</h4>
            <div id="du-files">
                <!-- file links akan diisi JS -->
            </div>
            <hr>
            <label>Hasil Verifikasi:</label><br>
            <select id="du-status">
                <option value="diterima">Terima</option>
                <option value="ditolak">Tolak</option>
            </select><br><br>
            <div id="du-catatan-group" style="display:none;">
                <label>Catatan Penolakan:</label><br>
                <textarea id="du-catatan" rows="3" cols="40"></textarea><br><br>
            </div>
            <button onclick="submitVerifikasiDaftarUlang()" style="padding:6px 12px;">Simpan</button>
            <button onclick="tutupModalDaftarUlang()" style="padding:6px 12px;">Tutup</button>
        </div>
        <div id="overlayDaftarUlang"
            style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;">
        </div>
    </div>

    <!-- Template Surat -->
    <div id="template-surat" class="section" style="display:none;">
        <h2>Manajemen Template Surat</h2>
        <button onclick="tampilkanFormTemplate()">Tambah Template</button>
        <br><br>
        <div id="form-template" style="display:none;">
            <h3 id="form-template-title">Tambah Template</h3>
            <input type="hidden" id="template-id">
            <label>Nama Template:</label><br>
            <select id="template-nama">
                <option value="Surat Pernyataan">Surat Pernyataan Orang Tua/Wali</option>
                <option value="Pakta Integritas">Pakta Integritas Orang Tua/Wali</option>
            </select><br><br>
            <label>File (PDF/DOC/DOCX):</label><br>
            <input type="file" id="template-file" accept=".pdf,.doc,.docx"><br><br>
            <button onclick="simpanTemplate()">Simpan</button>
            <button onclick="batalFormTemplate()">Batal</button>
        </div>
        <table border="1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Template</th>
                    <th>Download</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-template"></tbody>
        </table>
    </div>

    <div id="gelombang" class="section" style="display:none;">
        <h2>Manajemen Gelombang Pendaftaran</h2>
        <button onclick="tampilkanFormGelombang()">Tambah Gelombang</button>
        <br><br>

        <!-- Form Tambah/Edit -->
        <div id="form-gelombang" style="display:none; border:1px solid #ccc; padding:10px; margin-bottom:20px;">
            <h3 id="form-gelombang-title">Tambah Gelombang</h3>
            <input type="hidden" id="gelombang-id">
            <label>Nomor Gelombang:</label><br>
            <input type="number" id="nomor_gelombang" min="1"><br><br>
            <label>Tahun:</label><br>
            <input type="number" id="tahun" min="2000" max="2100" value="2026"><br><br>
            <label>Periode Mulai:</label><br>
            <input type="datetime-local" id="periode_mulai"><br><br>
            <label>Periode Selesai:</label><br>
            <input type="datetime-local" id="periode_selesai"><br><br>
            <label>Kuota (jumlah pendaftar):</label><br>
            <input type="number" id="kuota" min="1"><br><br>
            <label>Biaya Formulir (Rp):</label><br>
            <input type="number" id="biaya_formulir" step="1000" min="0"><br><br>
            <label>Biaya Daftar Ulang (Rp):</label><br>
            <input type="number" id="biaya_daftar_ulang" step="1000" min="0"><br><br>
            <label>Status:</label><br>
            <select id="status">
                <option value="nonaktif">Nonaktif</option>
                <option value="aktif">Aktif</option>
            </select><br><br>
            <button onclick="simpanGelombang()">Simpan</button>
            <button onclick="batalFormGelombang()">Batal</button>
        </div>

        <!-- Daftar Gelombang -->
        <table border="1" width="100%" cellpadding="5">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Gelombang</th>
                    <th>Tahun</th>
                    <th>Periode</th>
                    <th>Kuota</th>
                    <th>Sisa Kuota</th>
                    <th>Biaya Formulir</th>
                    <th>Biaya Daftar Ulang</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-gelombang"></tbody>
        </table>
    </div>

    <!-- Manajemen Pendaftar -->
    <div id="manajemen-pendaftar" class="section" style="display:none;">
        <h2>Manajemen Pendaftar</h2>

        <!-- Filter & Sorting -->
        <div style="display: flex; gap: 15px; margin-bottom: 25px; flex-wrap: wrap; align-items: flex-end;">
            <div>
                <label>Tahun: </label><br>
                <select id="filter-tahun" style="padding: 6px 12px;">
                    <option value="">Semua Tahun</option>
                </select>
            </div>
            <div>
                <label>Gelombang: </label><br>
                <select id="filter-gelombang" style="padding: 6px 12px;">
                    <option value="">Semua Gelombang</option>
                </select>
            </div>
            <div>
                <label>Status Formulir: </label><br>
                <select id="filter-status-formulir" style="padding: 6px 12px;">
                    <option value="">Semua</option>
                    <option value="sudah">Sudah Mengisi</option>
                    <option value="belum">Belum Mengisi</option>
                </select>
            </div>
            <div>
                <label>Status Kelulusan: </label><br>
                <select id="filter-kelulusan" style="padding: 6px 12px;">
                    <option value="">Semua</option>
                    <option value="lulus">Lulus</option>
                    <option value="tidak_lulus">Tidak Lulus</option>
                    <option value="belum">Belum Dites</option>
                </select>
            </div>
            <div>
                <label>Status Daftar Ulang: </label><br>
                <select id="filter-status-du" style="padding: 6px 12px;">
                    <option value="">Semua</option>
                    <option value="sudah">Sudah Daftar Ulang</option>
                    <option value="belum">Belum Daftar Ulang</option>
                    <option value="diterima">Diterima</option>
                    <option value="ditolak">Ditolak</option>
                    <option value="menunggu">Menunggu Verifikasi</option>
                </select>
            </div>
            <div>
                <label>Urutkan: </label><br>
                <select id="sort-by" style="padding: 6px 12px;">
                    <option value="created_at">Tanggal Daftar</option>
                    <option value="no_pendaftaran">No. Induk</option>
                    <option value="name">Nama</option>
                    <option value="email">Email</option>
                </select>
            </div>
            <div>
                <label>Urutan: </label><br>
                <select id="sort-order" style="padding: 6px 12px;">
                    <option value="desc">Terbaru ke Terlama</option>
                    <option value="asc">Terlama ke Terbaru</option>
                </select>
            </div>
            <div>
                <label>Cari (Nama/Email/No Induk): </label><br>
                <input type="text" id="search-input" placeholder="Ketik kata kunci..."
                    style="padding: 6px 12px; width: 220px;">
            </div>
            <div>
                <button onclick="loadPendaftar()" style="padding: 6px 18px;">Terapkan Filter</button>
            </div>
        </div>

        <!-- Tabel -->
        <table border="1" width="100%" cellpadding="8" style="border-collapse: collapse;">
            <thead style="background: #f0f0f0;">
                <tr>
                    <th>No. Induk</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Status Formulir</th>
                    <th>Kelulusan</th>
                    <th>Status Daftar Ulang</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-pendaftar">
                <tr>
                    <td colspan="7" style="text-align:center;">Memuat data...</td>
                </tr>
            </tbody>
        </table>

        <!-- Pagination -->
        <div id="pagination-pendaftar" style="margin-top: 25px; display: flex; justify-content: center; gap: 8px;">
        </div>

        <!-- Modal Detail -->
        <div id="modalDetailPendaftar"
            style="display:none; position:fixed; top:5%; left:10%; width:80%; background:white; border:2px solid #1a4d2e; padding:20px; z-index:1000; max-height:85%; overflow:auto; border-radius:8px;">
            <h3 style="margin-bottom:15px;">Detail Pendaftar</h3>
            <div id="detail-content"></div>
            <div
                style="margin-top:25px; display:flex; gap:15px; justify-content:flex-end; border-top:1px solid #eee; padding-top:20px;">
                <button id="btnLihatFormulir" onclick="lihatFormulirPendaftar()"
                    style="padding:8px 16px; background:#1a4d2e; color:white; border:none; border-radius:6px; cursor:pointer;">Lihat
                    Formulir</button>
                <button id="btnLihatDokumenDU" onclick="lihatDokumenDaftarUlang()"
                    style="padding:8px 16px; background:#1a4d2e; color:white; border:none; border-radius:6px; cursor:pointer;">Lihat
                    Dokumen Daftar Ulang</button>
                <button onclick="tutupModalDetail()"
                    style="padding:8px 16px; background:#ccc; border:none; border-radius:6px; cursor:pointer;">Tutup</button>
            </div>
        </div>
        <div id="overlayDetail"
            style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;">
        </div>

        <!-- Modal untuk menampilkan data formulir -->
        <div id="modalFormulir"
            style="display:none; position:fixed; top:10%; left:15%; width:70%; background:white; border:2px solid #1a4d2e; padding:20px; z-index:1001; max-height:70%; overflow:auto; border-radius:8px;">
            <h3>Data Formulir Pendaftaran</h3>
            <div id="formulir-content"></div>
            <div style="margin-top:20px; text-align:right;">
                <button onclick="tutupModalFormulir()">Tutup</button>
            </div>
        </div>

        <!-- Modal untuk menampilkan dokumen daftar ulang -->
        <div id="modalDokumenDU"
            style="display:none; position:fixed; top:10%; left:15%; width:70%; background:white; border:2px solid #1a4d2e; padding:20px; z-index:1001; max-height:70%; overflow:auto; border-radius:8px;">
            <h3>Dokumen Daftar Ulang</h3>
            <div id="dokumen-du-content"></div>
            <div style="margin-top:20px; text-align:right;">
                <button onclick="tutupModalDokumenDU()">Tutup</button>
            </div>
        </div>
    </div>

    <div id="manajemen-staff" class="section" style="display:none;">
        <h2>Manajemen Staff (Panitia & Bendahara)</h2>
        <button onclick="tampilkanFormStaff()">Tambah Staff</button>
        <br><br>

        <!-- Form Tambah/Edit -->
        <div id="form-staff" style="display:none; border:1px solid #ccc; padding:10px; margin-bottom:20px;">
            <h3 id="form-staff-title">Tambah Staff</h3>
            <input type="hidden" id="staff-id">
            <label>Nama Lengkap:</label><br>
            <input type="text" id="staff-name"><br><br>
            <label>Email:</label><br>
            <input type="email" id="staff-email"><br><br>
            <label>Role:</label><br>
            <select id="staff-role">
                <option value="panitia">Panitia PPDB</option>
                <option value="bendahara">Bendahara</option>
            </select><br><br>
            <label>Password:</label><br>
            <input type="password" id="staff-password"><br><br>
            <button onclick="simpanStaff()">Simpan</button>
            <button onclick="batalFormStaff()">Batal</button>
        </div>

        <!-- Daftar Staff -->
        <table border="1" width="100%" cellpadding="5">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-staff"></tbody>
        </table>
    </div>

    <div id="laporan" class="section" style="display:none;">
        <p>Laporan (akan diimplementasikan)</p>
    </div>

    <hr>
    <button id="logoutButton">Logout</button>
    <div id="message"></div>

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

        // Role yang diperbolehkan mengakses dashboard ini
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

        // ========== Definisikan menu berdasarkan role ==========
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

        // Event listener navigasi
        document.querySelectorAll('button[data-section]').forEach(button => {
            button.addEventListener('click', function () {
                const targetId = this.getAttribute('data-section');
                document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
                const target = document.getElementById(targetId);
                if (target) target.style.display = 'block';

                // Muat data sesuai section (hanya jika role memiliki akses)
                if (targetId === 'verifikasi' && (user.role === 'panitia' || user.role === 'kepala_sekolah')) {
                    loadVerifikasi();
                } else if (targetId === 'metode-pembayaran' && (user.role === 'bendahara' || user.role === 'kepala_sekolah')) {
                    loadMetodePembayaran();
                } else if (targetId === 'verifikasi-pembayaran' && (user.role === 'bendahara' || user.role === 'kepala_sekolah')) {
                    switchJenisPembayaran('formulir');
                } else if (targetId === 'kelola-jadwal' && (user.role === 'panitia' || user.role === 'kepala_sekolah')) {
                    loadBelumTerjadwal();
                    loadSudahTerjadwal();
                } else if (targetId === 'penilaian' && (user.role === 'panitia' || user.role === 'kepala_sekolah')) {
                    loadBelumDinilai();
                    loadRiwayatPenilaian();
                } else if (targetId === 'verifikasi-daftar-ulang') {
                    loadDaftarUlangStaff();
                } else if (targetId === 'template-surat') {
                    loadTemplateSurat();
                } else if (targetId === 'manajemen-pendaftar') {
                    loadFilterOptions();
                    loadPendaftar();
                } else if (targetId === 'gelombang') {
                    loadGelombang();
                } else if (targetId === 'manajemen-staff') {
                    loadStaff();
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
                    let imgHtml = '';
                    if (m.gambar_qris) {
                        imgHtml = `<img src="/api/file/metode/${m.id}?token=${token}" width="100"><br>`;
                    }
                    html += `<div>
                <strong>${m.nama_bank || 'QRIS'}</strong><br>
                No: ${m.nomor_rekening || '-'}<br>
                Atas Nama: ${m.atas_nama || '-'}<br>
                ${imgHtml}
                <button onclick="editMetode(${m.id}, '${m.nama_bank || ''}', '${m.nomor_rekening || ''}', '${m.atas_nama || ''}', '${m.keterangan || ''}')">Edit</button>
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
                preview.src = `/api/file/metode/${id}?token=${token}`;
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

        // Variabel global untuk menyimpan data bukti yang sedang diverifikasi
        let currentBukti = null;
        let currentJenis = 'formulir'; // formulir atau masuk

        // Switch tab
        function switchJenisPembayaran(jenis) {
            currentJenis = jenis;
            if (jenis === 'formulir') {
                document.getElementById('container-formulir').style.display = 'block';
                document.getElementById('container-masuk').style.display = 'none';
                document.getElementById('tabFormulir').classList.add('tab-active');
                document.getElementById('tabMasuk').classList.remove('tab-active');
                loadDataVerifikasiPembayaran('formulir');
            } else {
                document.getElementById('container-formulir').style.display = 'none';
                document.getElementById('container-masuk').style.display = 'block';
                document.getElementById('tabMasuk').classList.add('tab-active');
                document.getElementById('tabFormulir').classList.remove('tab-active');
                loadDataVerifikasiPembayaran('masuk');
            }
        }

        // Load data dari API dan render tabel
        async function loadDataVerifikasiPembayaran(jenis) {
            try {
                const res = await fetch('/api/bukti-pembayaran/semua', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const allData = await res.json();
                // Filter berdasarkan jenis_pembayaran
                const filtered = allData.filter(b => b.jenis_pembayaran === jenis);

                // Pisahkan menunggu dan sudah diverifikasi (diterima/ditolak)
                const menunggu = filtered.filter(b => b.status === 'menunggu');
                const sudah = filtered.filter(b => b.status !== 'menunggu');

                if (jenis === 'formulir') {
                    renderTabel(menunggu, 'tabel-formulir-menunggu', false);
                    renderTabelSudah(sudah, 'tabel-formulir-sudah');
                } else {
                    renderTabel(menunggu, 'tabel-masuk-menunggu', false);
                    renderTabelSudah(sudah, 'tabel-masuk-sudah');
                }
            } catch (err) {
                console.error(err);
            }
        }

        // Render tabel untuk status menunggu (tanpa kolom aksi, ganti dengan tombol Lihat Bukti)
        function renderTabel(data, tableId, withAction = false) {
            const tbody = document.querySelector(`#${tableId} tbody`);
            if (!tbody) return;
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5">Tidak ada data.</td></tr>';
                return;
            }
            let html = '';
            data.forEach((b, i) => {
                html += `<tr>
            <td>${i + 1}</td>
            <td>${b.pendaftar?.name ?? '-'}</td>
            <td>${b.jenis_pembayaran === 'formulir' ? 'Formulir' : 'Daftar Ulang'}</td>
            <td><button onclick="bukaModalVerifikasi(${b.id_bukti_pembayaran})">Lihat Bukti</button></td>
            <td>${b.status}</td>
        </tr>`;
            });
            tbody.innerHTML = html;
        }

        // Render tabel untuk status sudah diverifikasi (menampilkan catatan/kwitansi)
        function renderTabelSudah(data, tableId) {
            const tbody = document.querySelector(`#${tableId} tbody`);
            if (!tbody) return;
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6">Tidak ada data.</td></tr>';
                return;
            }
            let html = '';
            data.forEach((b, i) => {
                let info = '';
                if (b.status === 'diterima') {
                    const kwitansiId = b.verifikasi?.kwitansi?.id_kwitansi;
                    if (kwitansiId) {
                        const token = localStorage.getItem('access_token');
                        info = `<a href="/api/file/kwitansi/${kwitansiId}?token=${token}" target="_blank">Lihat Kwitansi</a>`;
                    } else {
                        info = '-';
                    }
                }
                html += `<tr>
            <td>${i + 1}</td>
            <td>${b.pendaftar?.name ?? '-'}</td>
            <td>${b.jenis_pembayaran === 'formulir' ? 'Formulir' : 'Daftar Ulang'}</td>
            <td><a href="/api/file/bukti/${b.id_bukti_pembayaran}?token=${token}" target="_blank">Lihat Bukti</a></td>
            <td>${b.status}</td>
            <td>${info}</td>
        </tr>`;
            });
            tbody.innerHTML = html;
        }

        // Buka modal verifikasi untuk bukti tertentu
        function bukaModalVerifikasi(idBukti, path) {
            currentBukti = { id: idBukti };
            const token = localStorage.getItem('access_token');
            const imageUrl = `/api/file/bukti/${idBukti}?token=${encodeURIComponent(token)}`;
            document.getElementById('modalGambar').src = imageUrl;
            document.getElementById('modalHasil').value = 'diterima';
            document.getElementById('modalCatatanGroup').style.display = 'none';
            document.getElementById('modalKwitansiGroup').style.display = 'block';
            document.getElementById('modalCatatan').value = '';
            document.getElementById('modalKwitansi').value = '';
            document.getElementById('modalVerifikasi').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        // Event listener untuk change select hasil
        document.getElementById('modalHasil')?.addEventListener('change', function () {
            const isTolak = this.value === 'ditolak';
            document.getElementById('modalCatatanGroup').style.display = isTolak ? 'block' : 'none';
            document.getElementById('modalKwitansiGroup').style.display = isTolak ? 'none' : 'block';
        });

        // Submit verifikasi
        async function submitVerifikasi() {
            if (!currentBukti) return;
            const hasil = document.getElementById('modalHasil').value;
            const catatan = document.getElementById('modalCatatan').value;
            const fileInput = document.getElementById('modalKwitansi');

            const formData = new FormData();
            formData.append('id_bukti_pembayaran', currentBukti.id);
            formData.append('hasil_verifikasi', hasil);
            if (catatan) formData.append('catatan', catatan);
            if (hasil === 'diterima' && fileInput.files.length > 0) {
                formData.append('kwitansi', fileInput.files[0]);
            } else if (hasil === 'diterima' && fileInput.files.length === 0) {
                alert('Kwitansi wajib diupload jika menerima pembayaran.');
                return;
            }

            try {
                const res = await fetch('/api/verifikasi-pembayaran', {
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + token },
                    body: formData
                });
                if (res.ok) {
                    alert('Verifikasi berhasil.');
                    tutupModal();
                    // Refresh data sesuai jenis aktif
                    loadDataVerifikasiPembayaran(currentJenis);
                } else {
                    const err = await res.json();
                    alert('Gagal: ' + (err.message || JSON.stringify(err)));
                }
            } catch (e) {
                alert('Error: ' + e.message);
            }
        }

        function bukaGambarFull() {
            if (currentBukti) {
                const token = localStorage.getItem('access_token');
                window.open(`/api/file/bukti/${currentBukti.id}?token=${token}`, '_blank');
            }
        }

        function tutupModal() {
            document.getElementById('modalVerifikasi').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
            currentBukti = null;
        }

        // verifikasi pendaftar
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
            <td><button onclick="aturJadwalBaru('${item.id}', '${item.name}')">Atur Jadwal</button></td>
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
            <td><button onclick="ubahJadwal('${item.id_seleksi_tes}', '${item.jadwal_tes}', '${item.pendaftar?.name}')">Ubah Jadwal</button></td>
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
        async function loadBelumDinilai() {
            try {
                const res = await fetch('/api/seleksi', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const data = await res.json();
                // Filter yang belum memiliki id_penilaian
                const belum = data.filter(item => !item.id_penilaian && item.pendaftar);
                let html = '';
                if (belum.length === 0) {
                    html = '<tr><td colspan="4">Tidak ada pendaftar yang perlu dinilai.</td></tr>';
                } else {
                    belum.forEach((item, i) => {
                        html += `<tr>
                    <td>${i + 1}</td>
                    <td>${item.pendaftar?.name ?? '-'}</td>
                    <td>${item.jadwal_tes ? new Date(item.jadwal_tes).toLocaleString() : '-'}</td>
                    <td><button onclick="bukaModalPenilaian('${item.id_pendaftar}', '${item.pendaftar?.name}')">Nilai</button></td>
                </tr>`;
                    });
                }
                document.getElementById('tabel-belum-dinilai').innerHTML = html;
            } catch (err) {
                console.error(err);
                document.getElementById('tabel-belum-dinilai').innerHTML = '<tr><td colspan="4">Gagal memuat data.</td></tr>';
            }
        }

        // Muat riwayat penilaian (dari /api/penilaian)
        async function loadRiwayatPenilaian() {
            try {
                const res = await fetch('/api/penilaian', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const data = await res.json();
                let html = '';
                if (data.length === 0) {
                    html = '<tr><td colspan="8">Belum ada penilaian.</td></tr>';
                } else {
                    data.forEach((n, i) => {
                        html += `<tr>
                    <td>${i + 1}</td>
                    <td>${n.pendaftar?.name ?? '-'}</td>
                    <td>${n.kemampuan_membaca ?? '-'}</td>
                    <td>${n.kemampuan_menulis ?? '-'}</td>
                    <td>${n.kemampuan_berhitung ?? '-'}</td>
                    <td>${n.baca_alquran ?? '-'}</td>
                    <td>${n.seleksi_tes?.kelulusan_tes ?? '-'}</td>
                    <td>${n.penilai?.name ?? '-'}</td>
                </tr>`;
                    });
                }
                document.getElementById('tabel-riwayat-penilaian').innerHTML = html;
            } catch (err) {
                console.error(err);
                document.getElementById('tabel-riwayat-penilaian').innerHTML = '<tr><td colspan="8">Gagal memuat data.</td></tr>';
            }
        }

        // Buka modal input penilaian
        function bukaModalPenilaian(idPendaftar, namaPendaftar) {
            document.getElementById('modalIdPendaftar').value = idPendaftar;
            document.getElementById('modalNamaPendaftar').innerText = namaPendaftar;
            // Reset form
            document.getElementById('modalMembaca').value = '';
            document.getElementById('modalMenulis').value = '';
            document.getElementById('modalBerhitung').value = '';
            document.getElementById('modalBacaQuran').value = '';
            document.getElementById('modalCatatan').value = '';
            document.getElementById('modalKelulusan').value = 'lulus';
            document.getElementById('modalPenilaian').style.display = 'block';
            document.getElementById('overlayPenilaian').style.display = 'block';
        }

        function tutupModalPenilaian() {
            document.getElementById('modalPenilaian').style.display = 'none';
            document.getElementById('overlayPenilaian').style.display = 'none';
        }

        // Simpan penilaian dari modal
        async function simpanPenilaianModal() {
            const id_pendaftar = document.getElementById('modalIdPendaftar').value;
            const payload = {
                id_pendaftar: id_pendaftar,
                kemampuan_membaca: document.getElementById('modalMembaca').value,
                kemampuan_menulis: document.getElementById('modalMenulis').value,
                kemampuan_berhitung: document.getElementById('modalBerhitung').value,
                baca_alquran: document.getElementById('modalBacaQuran').value,
                catatan: document.getElementById('modalCatatan').value,
                kelulusan_tes: document.getElementById('modalKelulusan').value,
            };
            try {
                const res = await fetch('/api/penilaian', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                    body: JSON.stringify(payload)
                });
                if (res.ok) {
                    alert('Penilaian berhasil disimpan');
                    tutupModalPenilaian();
                    // Refresh kedua tabel
                    loadBelumDinilai();
                    loadRiwayatPenilaian();
                } else {
                    const err = await res.json();
                    alert('Gagal: ' + (err.message || JSON.stringify(err)));
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }
        }

        // ========================
        // VERIFIKASI DAFTAR ULANG (STAFF)
        // ========================
        async function loadDaftarUlangStaff() {
            try {
                const res = await fetch('/api/staff/daftar-ulang', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const data = await res.json();
                let html = '';
                data.forEach((du, i) => {
                    let aksi = '';
                    if (du.status === 'menunggu') {
                        aksi = `<button onclick="bukaModalDaftarUlang(${du.id}, '${du.user?.name}')">Verifikasi & Lihat Berkas</button>`;
                    } else {
                        aksi = `<span style="color:gray;">Sudah diverifikasi (${du.status})</span>`;
                    }
                    html += `<tr>
                        <td>${i + 1}</td>
                        <td>${du.user?.name || '-'}</td>
                        <td>${du.status}</td>
                        <td>${aksi}</td>
                    </tr>`;
                });
                document.getElementById('tabel-daftar-ulang').innerHTML = html || '<tr><td colspan="4">Tidak ada data daftar ulang.</td></tr>';
            } catch (err) {
                console.error(err);
            }
        }

        async function bukaModalDaftarUlang(id, nama) {
            document.getElementById('du-id').value = id;
            document.getElementById('du-nama').innerText = nama;
            document.getElementById('du-status').value = 'diterima';
            document.getElementById('du-catatan').value = '';
            document.getElementById('du-catatan-group').style.display = 'none';

            // Tampilkan semua berkas
            const token = localStorage.getItem('access_token');
            const fileTypes = [
                { label: 'Akte Kelahiran', field: 'akte' },
                { label: 'Ijazah TK', field: 'ijazah' },
                { label: 'KTP Orang Tua/Wali', field: 'ktp' },
                { label: 'Kartu Keluarga', field: 'kk' },
                { label: 'NISN (scan)', field: 'nisn' },
                { label: 'Surat Pernyataan', field: 'pernyataan' },
                { label: 'Pakta Integritas', field: 'pakta' }
            ];
            let filesHtml = '<ul>';
            for (let ft of fileTypes) {
                const url = `/api/file/daftar-ulang/${id}/${ft.field}?token=${token}`;
                filesHtml += `<li><strong>${ft.label}:</strong> <a href="${url}" target="_blank">Lihat File</a></li>`;
            }
            filesHtml += '</ul>';
            document.getElementById('du-files').innerHTML = filesHtml;

            document.getElementById('modalDaftarUlang').style.display = 'block';
            document.getElementById('overlayDaftarUlang').style.display = 'block';
        }

        function tutupModalDaftarUlang() {
            document.getElementById('modalDaftarUlang').style.display = 'none';
            document.getElementById('overlayDaftarUlang').style.display = 'none';
        }

        // Event listener untuk menampilkan catatan jika ditolak
        document.getElementById('du-status')?.addEventListener('change', function () {
            const isTolak = this.value === 'ditolak';
            document.getElementById('du-catatan-group').style.display = isTolak ? 'block' : 'none';
        });

        async function submitVerifikasiDaftarUlang() {
            const id = document.getElementById('du-id').value;
            const status = document.getElementById('du-status').value;
            const catatan = document.getElementById('du-catatan').value;
            try {
                const res = await fetch(`/api/staff/daftar-ulang/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                    body: JSON.stringify({ status, catatan })
                });
                if (res.ok) {
                    alert('Verifikasi berhasil.');
                    tutupModalDaftarUlang();
                    loadDaftarUlangStaff();
                } else {
                    const err = await res.json();
                    alert('Gagal: ' + (err.message || JSON.stringify(err)));
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }
        }

        // Template Surat
        async function loadTemplateSurat() {
            const res = await fetch('/api/template-surat', { headers: { 'Authorization': 'Bearer ' + token } });
            const data = await res.json();
            let html = '';
            data.forEach((t, i) => {
                html += `<tr>
            <td>${i + 1}</td>
            <td>${t.nama}</td>
            <td><a href="/api/template-surat/download/${t.id}?token=${token}" target="_blank">Download</a></td>
            <td><button onclick="editTemplate(${t.id}, '${t.nama}')">Edit</button>
                   <button onclick="hapusTemplate(${t.id})">Hapus</button></td>
        </tr>`;
            });
            document.getElementById('tabel-template').innerHTML = html;
        }

        function tampilkanFormTemplate() {
            document.getElementById('form-template').style.display = 'block';
            document.getElementById('form-template-title').innerText = 'Tambah Template';
            document.getElementById('template-id').value = '';
            document.getElementById('template-nama').value = 'Surat Pernyataan';
            document.getElementById('template-file').value = '';
        }
        function batalFormTemplate() { document.getElementById('form-template').style.display = 'none'; }
        async function simpanTemplate() {
            const id = document.getElementById('template-id').value;
            const formData = new FormData();
            formData.append('nama', document.getElementById('template-nama').value);
            const fileInput = document.getElementById('template-file');
            if (fileInput.files.length > 0) formData.append('file', fileInput.files[0]);
            const url = id ? `/api/template-surat/${id}` : '/api/template-surat';
            const method = id ? 'POST' : 'POST'; // karena PUT tidak support multipart langsung, kita bisa gunakan POST dengan _method PUT
            if (id) formData.append('_method', 'PUT');
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token },
                body: formData
            });
            if (res.ok) {
                alert('Template tersimpan');
                batalFormTemplate();
                loadTemplateSurat();
            } else alert('Gagal');
        }
        async function editTemplate(id, nama) {
            document.getElementById('template-id').value = id;
            document.getElementById('template-nama').value = nama;
            document.getElementById('template-file').value = '';
            document.getElementById('form-template').style.display = 'block';
            document.getElementById('form-template-title').innerText = 'Edit Template';
        }
        async function hapusTemplate(id) {
            if (!confirm('Yakin hapus template?')) return;
            const res = await fetch(`/api/template-surat/${id}`, { method: 'DELETE', headers: { 'Authorization': 'Bearer ' + token } });
            if (res.ok) {
                alert('Dihapus');
                loadTemplateSurat();
            } else alert('Gagal');
        }

        // gelombang
        async function loadGelombang() {
            try {
                const res = await fetch('/api/gelombang', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const data = await res.json();
                let html = '';
                data.forEach((g, i) => {
                    const periode = `${new Date(g.periode_mulai).toLocaleString()} s/d ${new Date(g.periode_selesai).toLocaleString()}`;
                    html += `<tr>
                        <td>${i + 1}</td>
                        <td>Gelombang ${g.nomor_gelombang}</td>
                        <td>${g.tahun}</td>
                        <td>${periode}</td>
                        <td>${g.kuota}</td>
                        <td>${g.sisa_kuota}</td>
                        <td>Rp ${parseInt(g.biaya_formulir).toLocaleString()}</td>
                        <td>Rp ${parseInt(g.biaya_daftar_ulang).toLocaleString()}</td>
                        <td>${g.status === 'aktif' ? '✅ Aktif' : '❌ Nonaktif'}</td>
                        <td>
                            <button onclick="editGelombang(${g.id})">Edit</button>
                            <button onclick="hapusGelombang(${g.id})">Hapus</button>
                            <button onclick="toggleStatusGelombang(${g.id})">Toggle Aktif</button>
                        </td>
                    </tr>`;
                });
                document.getElementById('tabel-gelombang').innerHTML = html || '<tr><td colspan="9">Belum ada gelombang.</td></tr>';
            } catch (err) {
                console.error(err);
            }
        }

        function tampilkanFormGelombang() {
            document.getElementById('form-gelombang').style.display = 'block';
            document.getElementById('form-gelombang-title').innerText = 'Tambah Gelombang';
            document.getElementById('gelombang-id').value = '';
            document.getElementById('nomor_gelombang').value = '';
            document.getElementById('tahun').value = new Date().getFullYear();
            document.getElementById('periode_mulai').value = '';
            document.getElementById('periode_selesai').value = '';
            document.getElementById('kuota').value = '';
            document.getElementById('biaya_pendaftaran').value = '';
            document.getElementById('status').value = 'nonaktif';
        }

        function batalFormGelombang() {
            document.getElementById('form-gelombang').style.display = 'none';
        }

        async function simpanGelombang() {
            const id = document.getElementById('gelombang-id').value;
            const payload = {
                nomor_gelombang: document.getElementById('nomor_gelombang').value,
                tahun: document.getElementById('tahun').value,
                periode_mulai: document.getElementById('periode_mulai').value,
                periode_selesai: document.getElementById('periode_selesai').value,
                kuota: document.getElementById('kuota').value,
                biaya_formulir: document.getElementById('biaya_formulir').value,
                biaya_daftar_ulang: document.getElementById('biaya_daftar_ulang').value,
                status: document.getElementById('status').value,
            };
            const url = id ? `/api/gelombang/${id}` : '/api/gelombang';
            const method = id ? 'PUT' : 'POST';
            try {
                const res = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                    body: JSON.stringify(payload)
                });
                if (res.ok) {
                    alert('Gelombang berhasil disimpan.');
                    batalFormGelombang();
                    loadGelombang();
                } else {
                    const err = await res.json();
                    alert('Gagal: ' + (err.message || JSON.stringify(err)));
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }
        }

        async function editGelombang(id) {
            try {
                const res = await fetch(`/api/gelombang/${id}`, {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const g = await res.json();
                document.getElementById('gelombang-id').value = g.id;
                document.getElementById('nomor_gelombang').value = g.nomor_gelombang;
                document.getElementById('tahun').value = g.tahun;
                document.getElementById('periode_mulai').value = g.periode_mulai.slice(0, 16);
                document.getElementById('periode_selesai').value = g.periode_selesai.slice(0, 16);
                document.getElementById('kuota').value = g.kuota;
                document.getElementById('biaya_formulir').value = g.biaya_formulir;
                document.getElementById('biaya_daftar_ulang').value = g.biaya_daftar_ulang;
                document.getElementById('status').value = g.status;
                document.getElementById('form-gelombang').style.display = 'block';
                document.getElementById('form-gelombang-title').innerText = 'Edit Gelombang';
            } catch (err) {
                alert('Gagal mengambil data gelombang.');
            }
        }

        async function hapusGelombang(id) {
            if (!confirm('Yakin hapus gelombang ini?')) return;
            try {
                const res = await fetch(`/api/gelombang/${id}`, {
                    method: 'DELETE',
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                if (res.ok) {
                    alert('Gelombang dihapus.');
                    loadGelombang();
                } else {
                    const err = await res.json();
                    alert('Gagal: ' + err.message);
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }
        }

        async function toggleStatusGelombang(id) {
            try {
                const res = await fetch(`/api/gelombang/${id}/toggle-status`, {
                    method: 'PATCH',
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                if (res.ok) {
                    alert('Status gelombang berubah.');
                    loadGelombang();
                } else {
                    alert('Gagal mengubah status.');
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }
        }

        // ========================
        // MANAJEMEN PENDAFTAR
        // ========================

        async function loadFilterOptions() {
            try {
                // Load tahun
                const tahunRes = await fetch('/api/pendaftar/tahun-options', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                if (tahunRes.ok) {
                    const tahunList = await tahunRes.json();
                    let tahunHtml = '<option value="">Semua Tahun</option>';
                    tahunList.forEach(t => {
                        tahunHtml += `<option value="${t}">${t}</option>`;
                    });
                    document.getElementById('filter-tahun').innerHTML = tahunHtml;
                }

                // Load gelombang
                const gelRes = await fetch('/api/gelombang', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const gelData = await gelRes.json();
                let gelHtml = '<option value="">Semua Gelombang</option>';
                gelData.forEach(g => {
                    gelHtml += `<option value="${g.id}">Gelombang ${g.nomor_gelombang} - ${g.tahun}</option>`;
                });
                document.getElementById('filter-gelombang').innerHTML = gelHtml;
            } catch (err) {
                console.error('Gagal load filter options:', err);
            }
        }

        async function loadPendaftar(page = 1) {
            const tahun = document.getElementById('filter-tahun').value;
            const gelombang = document.getElementById('filter-gelombang').value;
            const sortBy = document.getElementById('sort-by').value;
            const sortOrder = document.getElementById('sort-order').value;
            const statusFormulir = document.getElementById('filter-status-formulir').value;
            const kelulusan = document.getElementById('filter-kelulusan').value;
            const statusDaftarUlang = document.getElementById('filter-status-du').value;
            const search = document.getElementById('search-input').value;

            let url = `/api/pendaftar?page=${page}&sort_by=${sortBy}&sort_order=${sortOrder}`;
            if (tahun) url += `&tahun=${tahun}`;
            if (gelombang) url += `&gelombang=${gelombang}`;
            if (statusFormulir) url += `&status_formulir=${statusFormulir}`;
            if (kelulusan) url += `&kelulusan=${kelulusan}`;
            if (statusDaftarUlang) url += `&status_daftar_ulang=${statusDaftarUlang}`;
            if (search) url += `&search=${encodeURIComponent(search)}`;

            try {
                const res = await fetch(url, { headers: { 'Authorization': 'Bearer ' + token } });
                const data = await res.json();
                renderTabelPendaftar(data.data);
                renderPagination(data);
            } catch (err) {
                console.error(err);
                document.getElementById('tabel-pendaftar').innerHTML = '<tr><td colspan="7">Gagal memuat data.</td></tr>';
            }
        }

        function renderTabelPendaftar(pendaftar) {
            if (!pendaftar || pendaftar.length === 0) {
                document.getElementById('tabel-pendaftar').innerHTML = '<tr><td colspan="7">Tidak ada pendaftar.</td></tr>';
                return;
            }
            let html = '';
            pendaftar.forEach(p => {
                const noInduk = p.no_pendaftaran || '-';
                let statusForm = p.status_formulir;
                if (statusForm === 'belum_isi') statusForm = 'Belum isi';
                else if (statusForm === 'menunggu') statusForm = 'Menunggu';
                else if (statusForm === 'diterima') statusForm = 'Diterima';
                else if (statusForm === 'ditolak') statusForm = 'Ditolak';
                else statusForm = '-';

                const kelulusan = p.kelulusan || '-';
                const statusDu = p.status_daftar_ulang || '-';

                html += `<tr>
                    <td>${escapeHtml(noInduk)}</td>
                    <td>${escapeHtml(p.name)}</td>
                    <td>${escapeHtml(p.email)}</td>
                    <td>${statusForm}</td>
                    <td>${kelulusan}</td>
                    <td>${statusDu}</td>
                    <td><button onclick="window.open('/staff/pendaftar/${p.id}', '_blank')">Detail</button></td>
                </tr>`;
            });
            document.getElementById('tabel-pendaftar').innerHTML = html;
        }

        function renderPagination(pagination) {
            const container = document.getElementById('pagination-pendaftar');
            if (!pagination || pagination.last_page <= 1) {
                container.innerHTML = '';
                return;
            }
            let html = '';
            for (let i = 1; i <= pagination.last_page; i++) {
                const active = i === pagination.current_page ? 'style="font-weight:bold; background:#1a4d2e; color:white;"' : '';
                html += `<button onclick="loadPendaftar(${i})" ${active} style="padding:5px 12px; cursor:pointer;">${i}</button>`;
            }
            container.innerHTML = html;
        }

        let currentPendaftarId = null;

        async function lihatDetailPendaftar(id) {
            currentPendaftarId = id;
            try {
                const res = await fetch(`/api/pendaftar/${id}`, {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const user = await res.json();

                // Format status formulir
                let statusForm = '-';
                if (user.status_formulir === 'belum_isi') statusForm = 'Belum mengisi formulir';
                else if (user.status_formulir === 'menunggu') statusForm = 'Menunggu verifikasi';
                else if (user.status_formulir === 'diterima') statusForm = 'Diterima';
                else if (user.status_formulir === 'ditolak') statusForm = 'Ditolak';
                else statusForm = user.status_formulir || '-';

                // Format kelulusan
                let kelulusan = '-';
                if (user.kelulusan === 'lulus') kelulusan = 'Lulus';
                else if (user.kelulusan === 'tidak_lulus') kelulusan = 'Tidak Lulus';
                else if (user.kelulusan === null) kelulusan = 'Belum ada jadwal/nilai';
                else kelulusan = user.kelulusan;

                // Format status daftar ulang
                let statusDU = '-';
                if (user.status_daftar_ulang === 'menunggu') statusDU = 'Menunggu verifikasi';
                else if (user.status_daftar_ulang === 'diterima') statusDU = 'Diterima';
                else if (user.status_daftar_ulang === 'ditolak') statusDU = 'Ditolak';
                else if (user.status_daftar_ulang === null) statusDU = 'Belum daftar ulang';
                else statusDU = user.status_daftar_ulang;

                let detailHtml = `
                    <table style="width:100%; border-collapse:collapse;">
                        <tr><td style="padding:8px; width:160px;"><strong>Nama:</strong></td><td>${escapeHtml(user.name)}</td></tr>
                        <tr><td style="padding:8px;"><strong>Email:</strong></td><td>${escapeHtml(user.email)}</td></tr>
                        <tr><td style="padding:8px;"><strong>No. Induk Pendaftaran:</strong></td><td>${escapeHtml(user.no_pendaftaran || '-')}</td></tr>
                        <tr><td style="padding:8px;"><strong>Status Formulir:</strong></td><td>${statusForm}</td></tr>
                        <tr><td style="padding:8px;"><strong>Kelulusan Tes:</strong></td><td>${kelulusan}</td></tr>
                        <tr><td style="padding:8px;"><strong>Status Daftar Ulang:</strong></td><td>${statusDU}</td></tr>
                    </table>
                `;

                document.getElementById('detail-content').innerHTML = detailHtml;
                document.getElementById('modalDetailPendaftar').style.display = 'block';
                document.getElementById('overlayDetail').style.display = 'block';
            } catch (err) {
                alert('Gagal mengambil detail pendaftar.');
            }
        }
        async function lihatFormulirPendaftar() {
            if (!currentPendaftarId) return;
            try {
                const res = await fetch(`/api/pendaftar/${currentPendaftarId}/formulir`, {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const data = await res.json();
                if (!data || !data.nama_lengkap) {
                    document.getElementById('formulir-content').innerHTML = '<p>Pendaftar belum mengisi formulir.</p>';
                } else {
                    let html = `
                <table style="width:100%; border-collapse:collapse;">
                    <tr><td style="padding:6px;"><strong>Nama Siswa:</strong></td><td>${escapeHtml(data.nama_lengkap)}</td></tr>
                    <tr><td style="padding:6px;"><strong>Tempat/Tgl Lahir:</strong></td><td>${escapeHtml(data.tempat_lahir)} / ${data.tanggal_lahir}</td></tr>
                    <tr><td style="padding:6px;"><strong>NIK:</strong></td><td>${escapeHtml(data.nik)}</td></tr>
                    <tr><td style="padding:6px;"><strong>Agama:</strong></td><td>${escapeHtml(data.agama)}</td></tr>
                    <tr><td style="padding:6px;"><strong>Alamat:</strong></td><td>${escapeHtml(data.alamat_lengkap)}</td></tr>
                    <tr><td style="padding:6px;"><strong>Status Verifikasi:</strong></td><td>${data.verifikasi ? data.verifikasi.hasil_verifikasi : 'Belum diverifikasi'}</td></tr>
                </table>
            `;
                    if (data.verifikasi && data.verifikasi.catatan) {
                        html += `<div style="margin-top:12px; background:#fde8e8; padding:8px;"><strong>Catatan Verifikasi:</strong> ${escapeHtml(data.verifikasi.catatan)}</div>`;
                    }
                    document.getElementById('formulir-content').innerHTML = html;
                }
                document.getElementById('modalFormulir').style.display = 'block';
            } catch (err) {
                alert('Gagal mengambil data formulir.');
            }
        }

        async function lihatDokumenDaftarUlang() {
            if (!currentPendaftarId) return;
            try {
                const res = await fetch(`/api/pendaftar/${currentPendaftarId}/daftar-ulang`, {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const du = await res.json();
                if (!du || !du.akte_kelahiran) {
                    document.getElementById('dokumen-du-content').innerHTML = '<p>Pendaftar belum melakukan daftar ulang.</p>';
                } else {
                    const tokenParam = encodeURIComponent(token);
                    const fields = [
                        { name: 'Akte Kelahiran', field: 'akte_kelahiran' },
                        { name: 'Ijazah TK', field: 'ijazah_tk' },
                        { name: 'KTP Orang Tua/Wali', field: 'ktp_orang_tua' },
                        { name: 'Kartu Keluarga', field: 'kartu_keluarga' },
                        { name: 'NISN', field: 'nisn_file' },
                        { name: 'Surat Pernyataan', field: 'surat_pernyataan' },
                        { name: 'Pakta Integritas', field: 'surat_pakta_integritas' }
                    ];
                    let html = '<ul style="list-style:none; padding-left:0;">';
                    for (let f of fields) {
                        if (du[f.field]) {
                            // Ambil ID dari path atau dari du.id? Kita punya id daftar ulang, tapi endpoint file memerlukan id du dan jenis file. Kita perlu buat endpoint file untuk daftar ulang dengan id record.
                            // Kita sudah punya /api/file/daftar-ulang/{id}/{jenis}
                            // Kita perlu id dari daftar ulang, bukan user id.
                            // Gunakan du.id (primary key daftar_ulang)
                            const fileUrl = `/api/file/daftar-ulang/${du.id}/${f.field.replace('_file', '')}?token=${tokenParam}`;
                            html += `<li style="margin-bottom:8px;"><strong>${f.name}:</strong> <a href="${fileUrl}" target="_blank">Lihat File</a></li>`;
                        } else {
                            html += `<li style="margin-bottom:8px;"><strong>${f.name}:</strong> Tidak ada</li>`;
                        }
                    }
                    html += '</ul>';
                    html += `<p><strong>Status:</strong> ${du.status}</p>`;
                    if (du.catatan) html += `<p><strong>Catatan:</strong> ${escapeHtml(du.catatan)}</p>`;
                    document.getElementById('dokumen-du-content').innerHTML = html;
                }
                document.getElementById('modalDokumenDU').style.display = 'block';
            } catch (err) {
                alert('Gagal mengambil dokumen daftar ulang.');
            }
        }

        function tutupModalFormulir() {
            document.getElementById('modalFormulir').style.display = 'none';
        }
        function tutupModalDokumenDU() {
            document.getElementById('modalDokumenDU').style.display = 'none';
        }
        function tutupModalDetail() {
            document.getElementById('modalDetailPendaftar').style.display = 'none';
            document.getElementById('overlayDetail').style.display = 'none';
            currentPendaftarId = null;
        }

        // ========================
        // MANAJEMEN STAFF (khusus kepala sekolah)
        // ========================
        async function loadStaff() {
            try {
                const res = await fetch('/api/admin/users', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const data = await res.json();
                let html = '';
                data.forEach((u, i) => {
                    let roleLabel = u.role === 'panitia' ? 'Panitia PPDB' : 'Bendahara';
                    html += `<tr>
                <td>${i + 1}</td>
                <td>${u.name}</td>
                <td>${u.email}</td>
                <td>${roleLabel}</td>
                <td>
                    <button onclick="editStaff(${u.id})">Edit</button>
                    <button onclick="hapusStaff(${u.id})">Hapus</button>
                 </td>
            </tr>`;
                });
                document.getElementById('tabel-staff').innerHTML = html || '<tr><td colspan="5">Tidak ada staff.</td></tr>';
            } catch (err) {
                console.error(err);
            }
        }

        function tampilkanFormStaff() {
            document.getElementById('form-staff').style.display = 'block';
            document.getElementById('form-staff-title').innerText = 'Tambah Staff';
            document.getElementById('staff-id').value = '';
            document.getElementById('staff-name').value = '';
            document.getElementById('staff-email').value = '';
            document.getElementById('staff-role').value = 'panitia';
            document.getElementById('staff-password').value = '';
        }

        function batalFormStaff() {
            document.getElementById('form-staff').style.display = 'none';
        }

        async function simpanStaff() {
            const id = document.getElementById('staff-id').value;
            const payload = {
                name: document.getElementById('staff-name').value,
                email: document.getElementById('staff-email').value,
                role: document.getElementById('staff-role').value,
                password: document.getElementById('staff-password').value
            };
            const url = id ? `/api/admin/users/${id}` : '/api/admin/users';
            const method = id ? 'PUT' : 'POST';
            try {
                const res = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                    body: JSON.stringify(payload)
                });
                if (res.ok) {
                    alert('Staff berhasil disimpan.');
                    batalFormStaff();
                    loadStaff();
                } else {
                    const err = await res.json();
                    alert('Gagal: ' + (err.message || JSON.stringify(err)));
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }
        }

        async function editStaff(id) {
            try {
                const res = await fetch(`/api/admin/users/${id}`, {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const u = await res.json();
                document.getElementById('staff-id').value = u.id;
                document.getElementById('staff-name').value = u.name;
                document.getElementById('staff-email').value = u.email;
                document.getElementById('staff-role').value = u.role;
                document.getElementById('staff-password').value = ''; // kosongkan
                document.getElementById('form-staff').style.display = 'block';
                document.getElementById('form-staff-title').innerText = 'Edit Staff';
            } catch (err) {
                alert('Gagal mengambil data staff.');
            }
        }

        async function hapusStaff(id) {
            if (!confirm('Yakin hapus staff ini?')) return;
            try {
                const res = await fetch(`/api/admin/users/${id}`, {
                    method: 'DELETE',
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                if (res.ok) {
                    alert('Staff dihapus.');
                    loadStaff();
                } else {
                    const err = await res.json();
                    alert('Gagal: ' + err.message);
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }
        }
        // ========== Semua fungsi yang sama seperti sebelumnya ==========
        // (loadMetodePembayaran, loadBuktiPembayaran, verifikasiBukti, dll tetap sama)
        // Pastikan pada fungsi-fungsi yang melakukan fetch, jika respons 403 akan muncul alert gagal, tidak masalah.

        // Agar kode tidak terlalu panjang, fungsi-fungsi lain (loadMetodePembayaran, 
        // loadBuktiPembayaran, loadVerifikasi, loadBelumTerjadwal, loadSudahTerjadwal,
        // loadPendaftarDinilai, loadDaftarPenilaian, submitJadwal, simpanPenilaian, dll)
        // tetap sama persis seperti kode asli. Tidak perlu diubah.

        // ... (salin semua fungsi dari kode asli di sini)

        // Logout (sama)
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
            }
        });
    </script>
</body>

</html>