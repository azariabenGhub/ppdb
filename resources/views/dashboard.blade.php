<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PPDB</title>
</head>

<body>
    <h1>Selamat Datang, <span id="userName"></span>!</h1>

    <!-- Navigasi Antar Halaman Dashboard -->
    <div>
        <button data-section="beranda">Beranda</button>
        <button data-section="formulir">Formulir Pendaftaran</button>
        <button data-section="jadwal">Jadwal Tes</button>
        <button data-section="pengumuman">Pengumuman</button>
        <button data-section="daftar-ulang">Daftar Ulang</button>
        <button data-section="pembayaran">Pembayaran</button>
        <button data-section="status">Status Pendaftaran</button>
    </div>

    <hr>

    <!-- HALAMAN BERANDA -->
    <div id="beranda" class="section">
        <p>Ini Beranda</p>
    </div>

    <!-- HALAMAN FORMULIR (MULTISTEP) -->
    <!-- FORMULIR MULTISTEP (UPDATE) -->
    <div id="formulir" class="section" style="display:none;">
        <h2>Formulir Pendaftaran</h2>

        <!-- STEP 1: BIODATA SISWA -->
        <div id="step-1" class="form-step">
            <fieldset>
                <legend>Langkah 1: Biodata Siswa</legend>
                <!-- Semua input tetap seperti sebelumnya -->
                <label>Nama Lengkap:</label><br>
                <input type="text" id="nama_lengkap"><br><br>
                <label>Tempat Lahir:</label><br>
                <input type="text" id="tempat_lahir"><br><br>
                <label>Tanggal Lahir:</label><br>
                <input type="date" id="tanggal_lahir"><br><br>
                <label>NIK:</label><br>
                <input type="text" id="nik"><br><br>
                <label>Agama:</label><br>
                <select id="agama">
                    <option value="">-- Pilih --</option>
                    <option>Islam</option>
                    <option>Kristen</option>
                    <option>Katolik</option>
                    <option>Hindu</option>
                    <option>Buddha</option>
                    <option>Konghucu</option>
                </select><br><br>
                <label>Warga Negara:</label><br>
                <input type="text" id="warga_negara"><br><br>
                <label>Anak ke-:</label><br>
                <input type="number" id="anak_ke" min="1"><br><br>
                <label>Jumlah Saudara:</label><br>
                <input type="number" id="jumlah_saudara" min="0"><br><br>
                <label>Alamat Lengkap:</label><br>
                <textarea id="alamat_lengkap" rows="3"></textarea>
            </fieldset>
            <br>
            <button type="button" onclick="nextStep(2)">Selanjutnya</button>
        </div>

        <!-- STEP 2: ORANG TUA / WALI -->
        <div id="step-2" class="form-step" style="display:none;">
            <fieldset>
                <legend>Langkah 2: Data Orang Tua / Wali</legend>
                <label>
                    <input type="radio" name="tipe_wali" value="orang_tua" checked onchange="toggleOrangTuaWali()">
                    Orang Tua
                </label>
                <label>
                    <input type="radio" name="tipe_wali" value="wali" onchange="toggleOrangTuaWali()"> Wali
                </label>
                <br><br>

                <!-- Form Orang Tua -->
                <div id="form-orang-tua">
                    <h3>Data Ayah</h3>
                    <input type="text" id="nama_ayah" placeholder="Nama Lengkap Ayah"><br><br>
                    <input type="text" id="pekerjaan_ayah" placeholder="Pekerjaan Ayah"><br><br>
                    <label>Agama:</label><br>
                    <select id="agama_ayah">
                        <option value="">-- Pilih --</option>
                        <option>Islam</option>
                        <option>Kristen</option>
                        <option>Katolik</option>
                        <option>Hindu</option>
                        <option>Buddha</option>
                        <option>Konghucu</option>
                    </select><br><br>
                    <input type="text" id="pendidikan_ayah" placeholder="Pendidikan Terakhir Ayah"><br><br>
                    <input type="text" id="no_ktp_ayah" placeholder="No KTP Ayah"><br><br>
                    <input type="text" id="penghasilan_ayah" placeholder="Penghasilan per Bulan Ayah"><br><br>
                    <input type="text" id="no_telp_ayah" placeholder="No Telp Ayah"><br><br>
                    <textarea id="alamat_ayah" rows="2" placeholder="Alamat Ayah"></textarea>
                    <hr>
                    <h3>Data Ibu</h3>
                    <input type="text" id="nama_ibu" placeholder="Nama Lengkap Ibu"><br><br>
                    <input type="text" id="pekerjaan_ibu" placeholder="Pekerjaan Ibu"><br><br>
                    <label>Agama:</label><br>
                    <select id="agama_ibu">
                        <option value="">-- Pilih --</option>
                        <option>Islam</option>
                        <option>Kristen</option>
                        <option>Katolik</option>
                        <option>Hindu</option>
                        <option>Buddha</option>
                        <option>Konghucu</option>
                    </select><br><br>
                    <input type="text" id="pendidikan_ibu" placeholder="Pendidikan Terakhir Ibu"><br><br>
                    <input type="text" id="no_ktp_ibu" placeholder="No KTP Ibu"><br><br>
                    <input type="text" id="penghasilan_ibu" placeholder="Penghasilan per Bulan Ibu"><br><br>
                    <input type="text" id="no_telp_ibu" placeholder="No Telp Ibu"><br><br>
                    <textarea id="alamat_ibu" rows="2" placeholder="Alamat Ibu"></textarea>
                </div>

                <!-- Form Wali -->
                <div id="form-wali" style="display: none;">
                    <h3>Data Wali</h3>
                    <input type="text" id="nama_wali" placeholder="Nama Lengkap Wali"><br><br>
                    <input type="text" id="pekerjaan_wali" placeholder="Pekerjaan Wali"><br><br>
                    <input type="text" id="agama_wali" placeholder="Agama Wali"><br><br>
                    <input type="text" id="pendidikan_wali" placeholder="Pendidikan Terakhir Wali"><br><br>
                    <input type="text" id="no_ktp_wali" placeholder="No KTP Wali"><br><br>
                    <input type="text" id="penghasilan_wali" placeholder="Penghasilan per Bulan Wali"><br><br>
                    <input type="text" id="no_telp_wali" placeholder="No Telp Wali"><br><br>
                    <textarea id="alamat_wali" rows="2" placeholder="Alamat Wali"></textarea>
                </div>
            </fieldset>
            <br>
            <button type="button" onclick="prevStep(1)">Sebelumnya</button>
            <button type="button" onclick="nextStep(3)">Selanjutnya</button>
        </div>

        <!-- STEP 3: DATA AKADEMIK (MURID PINDAHAN - DENGAN CHECKBOX DI SINI) -->
        <div id="step-3" class="form-step" style="display:none;">
            <fieldset>
                <legend>Langkah 3: Data Akademik</legend>
                <label>
                    <input type="checkbox" id="bukan-pindahan" onchange="toggleInputAkademik()"> Bukan murid pindahan
                </label>
                <br><br>
                <div id="form-akademik">
                    <input type="text" id="asal_sekolah" placeholder="Asal Sekolah/Madrasah"><br><br>
                    <input type="text" id="no_ijazah" placeholder="No Ijazah/STTB"><br><br>
                    <input type="text" id="tahun_ijazah" placeholder="Tahun"><br><br>
                    <input type="text" id="diterima_kelas" placeholder="Diterima di Kelas"><br><br>
                    <input type="text" id="pindah_dari" placeholder="Pindah dari"><br><br>
                    <input type="text" id="no_pindah" placeholder="No Pindah"><br><br>
                    <input type="date" id="tanggal_pindah">
                </div>
            </fieldset>
            <br>
            <button type="button" onclick="prevStep(2)">Sebelumnya</button>
            <button type="button" onclick="submitForm()">Kirim Formulir</button>
        </div>
    </div>

    <!-- HALAMAN LAINNYA -->
    <div id="jadwal" class="section" style="display:none;">
        <p>Ini Jadwal Tes</p>
    </div>
    <div id="pengumuman" class="section" style="display:none;">
        <p>Ini Pengumuman</p>
    </div>
    <div id="daftar-ulang" class="section" style="display:none;">
        <p>Ini Daftar Ulang</p>
    </div>
    <div id="pembayaran" class="section" style="display:none;">
        <p>Ini Pembayaran</p>
    </div>
    <div id="status" class="section" style="display:none;">
        <p>Ini Status Pendaftaran</p>
    </div>

    <hr>

    <!-- Tombol Logout -->
    <button id="logoutButton">Logout</button>
    <div id="message"></div>

    <script>
        // ========== 1. Nama User ==========
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        document.getElementById('userName').innerText = user.name || 'Pengguna';

        // ========== 2. Navigasi Halaman Utama ==========
        const navButtons = document.querySelectorAll('button[data-section]');
        const sections = document.querySelectorAll('.section');

        navButtons.forEach(button => {
            button.addEventListener('click', function () {
                const targetId = this.getAttribute('data-section');
                sections.forEach(section => {
                    section.style.display = 'none';
                });
                const target = document.getElementById(targetId);
                if (target) {
                    target.style.display = 'block';
                }
            });
        });

        // ========== 3. Multistep dalam Formulir ==========
        function showStep(stepNumber) {
            // Sembunyikan semua step, kecuali step khusus "selesai"
            document.getElementById('step-1').style.display = 'none';
            document.getElementById('step-2').style.display = 'none';
            document.getElementById('step-3').style.display = 'none';
            // document.getElementById('step-selesai').style.display = 'none';

            // Tampilkan step yang diminta
            const stepMap = {
                1: 'step-1',
                2: 'step-2',
                3: 'step-3',
                'selesai': 'step-selesai'
            };
            const el = document.getElementById(stepMap[stepNumber]);
            if (el) el.style.display = 'block';
        }

        function nextStep(step) {
            showStep(step);
        }

        function prevStep(step) {
            showStep(step);
        }

        // Toggle Orang Tua / Wali
        function toggleOrangTuaWali() {
            const radio = document.querySelector('input[name="tipe_wali"]:checked');
            const formOrtu = document.getElementById('form-orang-tua');
            const formWali = document.getElementById('form-wali');
            if (radio && radio.value === 'orang_tua') {
                formOrtu.style.display = 'block';
                formWali.style.display = 'none';
            } else {
                formOrtu.style.display = 'none';
                formWali.style.display = 'block';
            }
        }

        function toggleInputAkademik() {
            const checkbox = document.getElementById('bukan-pindahan');
            const formAkademik = document.getElementById('form-akademik');
            formAkademik.style.display = checkbox.checked ? 'none' : 'block';
        }

        // Fungsi untuk mengambil semua data formulir
        function getFormData() {
            const tipe_wali = document.querySelector('input[name="tipe_wali"]:checked').value;
            const isBukanPindahan = document.getElementById('bukan-pindahan').checked;

            const data = {
                // Biodata siswa
                nama_lengkap: document.getElementById('nama_lengkap').value,
                tempat_lahir: document.getElementById('tempat_lahir').value,
                tanggal_lahir: document.getElementById('tanggal_lahir').value,
                nik: document.getElementById('nik').value,
                agama: document.getElementById('agama').value,
                warga_negara: document.getElementById('warga_negara').value,
                anak_ke: document.getElementById('anak_ke').value,
                jumlah_saudara: document.getElementById('jumlah_saudara').value,
                alamat_lengkap: document.getElementById('alamat_lengkap').value,
                tipe_wali: tipe_wali,

                // Data orang tua / wali (hanya isi sesuai tipe)
                ...(tipe_wali === 'orang_tua' ? {
                    nama_ayah: document.getElementById('nama_ayah').value,
                    pekerjaan_ayah: document.getElementById('pekerjaan_ayah').value,
                    agama_ayah: document.getElementById('agama_ayah').value,
                    pendidikan_ayah: document.getElementById('pendidikan_ayah').value,
                    no_ktp_ayah: document.getElementById('no_ktp_ayah').value,
                    penghasilan_ayah: document.getElementById('penghasilan_ayah').value,
                    no_telp_ayah: document.getElementById('no_telp_ayah').value,
                    alamat_ayah: document.getElementById('alamat_ayah').value,
                    nama_ibu: document.getElementById('nama_ibu').value,
                    pekerjaan_ibu: document.getElementById('pekerjaan_ibu').value,
                    agama_ibu: document.getElementById('agama_ibu').value,
                    pendidikan_ibu: document.getElementById('pendidikan_ibu').value,
                    no_ktp_ibu: document.getElementById('no_ktp_ibu').value,
                    penghasilan_ibu: document.getElementById('penghasilan_ibu').value,
                    no_telp_ibu: document.getElementById('no_telp_ibu').value,
                    alamat_ibu: document.getElementById('alamat_ibu').value,
                } : {
                    nama_wali: document.getElementById('nama_wali').value,
                    pekerjaan_wali: document.getElementById('pekerjaan_wali').value,
                    agama_wali: document.getElementById('agama_wali').value,
                    pendidikan_wali: document.getElementById('pendidikan_wali').value,
                    no_ktp_wali: document.getElementById('no_ktp_wali').value,
                    penghasilan_wali: document.getElementById('penghasilan_wali').value,
                    no_telp_wali: document.getElementById('no_telp_wali').value,
                    alamat_wali: document.getElementById('alamat_wali').value,
                }),

                // Data akademik (hanya jika bukan pindahan tidak dikirim, bisa null)
                is_bukan_pindahan: isBukanPindahan,
                ...(isBukanPindahan ? {} : {
                    asal_sekolah: document.getElementById('asal_sekolah').value,
                    no_ijazah: document.getElementById('no_ijazah').value,
                    tahun_ijazah: document.getElementById('tahun_ijazah').value,
                    diterima_kelas: document.getElementById('diterima_kelas').value,
                    pindah_dari: document.getElementById('pindah_dari').value,
                    no_pindah: document.getElementById('no_pindah').value,
                    tanggal_pindah: document.getElementById('tanggal_pindah').value,
                })
            };

            return data;
        }

        // Fungsi submit ke server
        async function submitForm() {
            const token = localStorage.getItem('access_token');
            if (!token) {
                alert('Anda belum login.');
                return;
            }

            const payload = getFormData();
            try {
                const response = await fetch('/api/formulir', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();
                if (response.ok) {
                    alert('Pendaftaran berhasil!');
                    // Reset atau arahkan ke halaman lain
                } else {
                    alert('Gagal: ' + (result.message || JSON.stringify(result.errors)));
                }
            }
            catch (error) {
                console.error(error);
                alert('Terjadi kesalahan jaringan.');
            }
        }

        // ========== 4. Logout ==========
        document.getElementById('logoutButton').addEventListener('click', async function () {
            const token = localStorage.getItem('access_token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

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
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('message').innerText = 'Terjadi kesalahan jaringan.';
            }
        });
    </script>
</body>

</html>