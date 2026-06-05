{{-- ===== PEMBAYARAN ===== --}}
<div id="pembayaran" class="section" style="display:none;">
    <div class="payment-container">
        <!-- Header Sekolah (sama seperti desain) -->
        <div class="payment-header">
            <div class="school-header">
                <img src="{{ asset('storage/assets/logo-mizi.png') }}" alt="Logo MI Ziyadatul Ihsan" class="school-logo">
                <div class="school-title">
                    <p>YAYASAN PENDIDIKAN ISLAM ZIYADATUL IHSAN</p>
                    <h2>MADRASAH IBTIDAIYAH</h2>
                </div>
            </div>
        </div>

        <!-- Judul Halaman -->
        <div class="page-title">
            <h2>Pembayaran</h2>
            <p>Bayar dan verifikasi biaya pendaftaran</p>
        </div>

        <!-- Dua Kolom: Instruksi Pembayaran (kiri) dan Konfirmasi (kanan) -->
        <div class="payment-two-columns">
            <!-- Kolom Kiri: Instruksi Pembayaran -->
            <div class="payment-instructions-card">
                <div class="card-header">
                    <div class="icon-badge green-bg">
                        <i class="fa-solid fa-credit-card"></i>
                    </div>
                    <div class="header-text">
                        <h3>Instruksi Pembayaran</h3>
                        <p>Transfer ke rekening di bawah ini:</p>
                    </div>
                </div>
                <div class="bank-info" id="bank-info-container">
                    <!-- Data metode pembayaran akan diisi via JS -->
                    <div class="bank-detail">
                        <strong id="bank-name">BANK DKI</strong>
                        <div class="account-number">
                            <span id="account-number">1234567891111</span>
                            <button class="btn-copy" onclick="copyAccountNumber()"><i class="fa-regular fa-copy"></i></button>
                        </div>
                        <small id="account-owner">Atas Nama: MI ZIYADATUL IHSAN</small>
                    </div>
                </div>
                <div class="fee-breakdown" id="fee-breakdown">
                    <!-- Rincian biaya dari gelombang aktif akan diisi JS -->
                    <div class="divider"></div>
                    <p class="breakdown-title">Rincian Tahapan Biaya:</p>
                    <div class="fee-item">
                        <span class="fee-name" id="fee-formulir-name">Tahap 1: Biaya Formulir</span>
                        <span class="fee-amount" id="fee-formulir-amount">Rp 50.000</span>
                    </div>
                    <div class="fee-item">
                        <span class="fee-name" id="fee-daftarulang-name">Tahap 2: Biaya Daftar Ulang</span>
                        <span class="fee-amount" id="fee-daftarulang-amount">Rp 1.900.000</span>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Konfirmasi Pembayaran -->
            <div class="payment-confirmation-card">
                <div class="card-header">
                    <div class="icon-badge green-bg">
                        <i class="fa-regular fa-receipt"></i>
                    </div>
                    <div class="header-text">
                        <h3>Konfirmasi Pembayaran</h3>
                        <p>Kirim Bukti Pembayaran</p>
                    </div>
                </div>
                <form id="form-bukti" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="jenis_pembayaran">Jenis Pembayaran</label>
                        <select name="jenis_pembayaran" id="jenis_pembayaran" class="form-control">
                            <option value="formulir">Pembayaran Formulir</option>
                            <option value="daftar_ulang" disabled>Pembayaran Masuk (menunggu formulir diterima)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="bukti_pembayaran">Bukti Transfer (JPG/PNG/PDF)</label>
                        <div class="upload-area" id="upload-area">
                            <i class="fa-regular fa-cloud-arrow-up"></i>
                            <p>Klik atau seret file ke sini untuk mengunggah</p>
                            <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" accept=".jpg,.jpeg,.png,.pdf" hidden>
                            <span class="file-name-display"></span>
                        </div>
                    </div>
                    <button type="submit" class="btn-submit-kirim">Kirim Konfirmasi</button>
                </form>
            </div>
        </div>

        <!-- Riwayat Pembayaran -->
        <div class="payment-history">
            <h3>Riwayat Pembayaran</h3>
            <div class="history-table-wrapper">
                <table class="history-table" id="riwayat-bukti">
                    <thead>
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Keterangan</th>
                            <th>Jumlah</th>
                            <th>Bukti</th>
                            <th>Status</th>
                            <th>Kwitansi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="6" style="text-align:center;">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('section-scripts')
<script>
    // ========== GELOMBANG AKTIF (ambil biaya) ==========
    let activeGelombang = null;

    async function loadActiveGelombang() {
        try {
            const res = await fetch('/api/gelombang/aktif', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const gel = await res.json();
            if (gel && gel.id) {
                activeGelombang = gel;
                // Update tampilan rincian biaya
                document.getElementById('fee-formulir-amount').innerText = formatRupiah(gel.biaya_formulir);
                document.getElementById('fee-daftarulang-amount').innerText = formatRupiah(gel.biaya_daftar_ulang);
            }
        } catch (e) {
            console.error('Gagal ambil gelombang aktif', e);
        }
    }

    function formatRupiah(angka) {
        return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
    }

    // ========== METODE PEMBAYARAN ==========
    async function loadMetodeUntukPendaftar() {
        const bankInfoContainer = document.getElementById('bank-info-container');
        try {
            const res = await fetch('/api/metode-pembayaran', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const data = await res.json();
            if (data.length > 0) {
                // Ambil metode pertama (atau bisa ditampilkan semua, sesuai desain cukup satu)
                const metode = data[0];
                document.getElementById('bank-name').innerText = metode.nama_bank || 'Bank Transfer';
                document.getElementById('account-number').innerText = metode.nomor_rekening || '-';
                document.getElementById('account-owner').innerHTML = `Atas Nama: ${metode.atas_nama || '-'}`;
                // Jika ada QRIS bisa ditampilkan di bawah
                if (metode.gambar_qris) {
                    const qrisHtml = `<div class="qris-image"><img src="/api/file/metode/${metode.id}?token=${token}" alt="QRIS" style="max-width:150px; margin-top:10px;"></div>`;
                    bankInfoContainer.insertAdjacentHTML('beforeend', qrisHtml);
                }
            } else {
                bankInfoContainer.innerHTML = '<p>Belum ada metode pembayaran.</p>';
            }
        } catch (e) {
            console.error(e);
        }
    }

    // ========== UPLOAD BUKTI DENGAN DRAG & DROP ==========
    function initUploadArea() {
        const uploadArea = document.getElementById('upload-area');
        const fileInput = document.getElementById('bukti_pembayaran');
        const fileNameDisplay = uploadArea.querySelector('.file-name-display');

        uploadArea.addEventListener('click', () => fileInput.click());
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                updateFileName(fileInput.files[0].name, fileNameDisplay);
            }
        });
        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) {
                updateFileName(fileInput.files[0].name, fileNameDisplay);
            } else {
                fileNameDisplay.innerText = '';
            }
        });
    }

    function updateFileName(name, displayEl) {
        displayEl.innerText = name.length > 30 ? name.substring(0, 27) + '...' : name;
        displayEl.style.display = 'block';
    }

    // Submit form bukti (override)
    document.getElementById('form-bukti').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);

        const res = await fetch('/api/bukti-pembayaran', {
            method: 'POST',
            headers: { 'Authorization': 'Bearer ' + token },
            body: formData
        });
        if (res.ok) {
            alert('Bukti pembayaran berhasil dikirim');
            loadRiwayatBukti();
            document.getElementById('form-bukti').reset();
            document.querySelector('.file-name-display').innerText = '';
            // Reset status pilihan daftar ulang jika perlu
            cekStatusPendaftaran();
        } else {
            const err = await res.json();
            alert('Gagal: ' + (err.message || 'Terjadi kesalahan'));
        }
    });

    // ========== RIWAYAT PEMBAYARAN ==========
    async function loadRiwayatBukti() {
        const tbody = document.querySelector('#riwayat-bukti tbody');
        try {
            const res = await fetch('/api/bukti-pembayaran', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const data = await res.json();
            if (!data.length) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Belum ada riwayat pembayaran</td></tr>';
                return;
            }
            let html = '';
            data.forEach(b => {
                const statusClass = b.status === 'diterima' ? 'status-success' : (b.status === 'menunggu' ? 'status-pending' : 'status-rejected');
                const statusText = b.status === 'diterima' ? 'Terverifikasi' : (b.status === 'menunggu' ? 'Menunggu Verifikasi' : 'Ditolak');
                const kwitansiLink = b.verifikasi?.kwitansi ? `<a href="javascript:void(0)" onclick="lihatKwitansi(${b.verifikasi.kwitansi.id_kwitansi})" class="btn-download-kwitansi">Unduh</a>` : '-';
                
                let jumlahBiaya = 0;
                if (activeGelombang) {
                    jumlahBiaya = b.jenis_pembayaran === 'formulir' ? activeGelombang.biaya_formulir : activeGelombang.biaya_daftar_ulang;
                }
                
                html += `
                    <tr>
                        <td>${b.id_transaksi || '-'}</td>
                        <td>${b.jenis_pembayaran === 'formulir' ? 'Biaya Formulir' : 'Biaya Daftar Ulang'}</td>
                        <td>${formatRupiah(jumlahBiaya)}</td>
                        <td>
                            <button onclick="lihatBukti(${b.id_bukti_pembayaran})" class="btn-lihat-bukti" style="border:none; cursor:pointer;">
                                Lihat Bukti
                            </button>
                        </td>
                        <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                        <td>${kwitansiLink}</td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        } catch (e) {
            console.error(e);
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Gagal memuat riwayat</td></tr>';
        }
    }

    // Secure view functions
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

    async function lihatKwitansi(id) {
        try {
            const res = await fetch(`/api/file/kwitansi/${id}`, {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            if (!res.ok) {
                alert('Gagal mengambil kwitansi');
                return;
            }
            const blob = await res.blob();
            const url = URL.createObjectURL(blob);
            window.open(url, '_blank');
        } catch (e) {
            console.error(e);
            alert('Gagal membuka kwitansi');
        }
    }

    window.lihatBukti = lihatBukti;
    window.lihatKwitansi = lihatKwitansi;

    // ========== CEK STATUS UNTUK AKTIFKAN PEMBAYARAN DAFTAR ULANG ==========
    async function cekStatusPendaftaran() {
        const res = await fetch('/api/formulir-saya', {
            headers: { 'Authorization': 'Bearer ' + token }
        });
        const result = await res.json();
        if (result.data && result.data.status === 'diterima') {
            const daftarUlangOption = document.querySelector('#jenis_pembayaran option[value="daftar_ulang"]');
            if (daftarUlangOption) daftarUlangOption.disabled = false;
        }
    }

    // Copy nomor rekening
    window.copyAccountNumber = function() {
        const accNum = document.getElementById('account-number').innerText;
        navigator.clipboard.writeText(accNum);
        alert('Nomor rekening disalin');
    };

    // ========== LOAD SEMUA DATA SAAT SECTION DITAMPILKAN ==========
    // Fungsi utama yang dipanggil dari navigate() di dashboard
    window.loadPembayaranSection = async function() {
        await loadActiveGelombang();
        await loadMetodeUntukPendaftar();
        await loadRiwayatBukti();
        await cekStatusPendaftaran();
        initUploadArea();
    };

    // Jika perlu, panggil loadPembayaranSection saat section pembayaran di-load
    // Navigasi akan memanggil loadMetodeUntukPendaftar, loadRiwayatBukti, cekStatusPendaftaran
    // Kita override agar sesuai. Namun agar tidak bentrok dengan fungsi global, kita patch.
    // Di dalam navigate() sudah ada panggilan ke fungsi-fungsi tersebut, kita biarkan saja.
    // Tapi kita juga perlu memanggil loadActiveGelombang dan initUploadArea.
    // Maka kita tambahkan hooks.

    // Override fungsi yang dipanggil oleh navigate untuk pembayaran
    const originalLoadMetode = window.loadMetodeUntukPendaftar;
    window.loadMetodeUntukPendaftar = async function() {
        await loadActiveGelombang();
        if (originalLoadMetode) await originalLoadMetode();
        initUploadArea();
    };
</script>
@endpush

{{-- Tambahan CSS spesifik untuk halaman pembayaran --}}
<style>
/* Payment Page Styles - sesuai desain Figma */
#pembayaran .payment-container {
    max-width: 1200px;
    margin: 0 auto;
    background: #FAFAFA;
    border-radius: 5px;
    box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.25);
    padding: 30px 40px;
}

#pembayaran .school-header {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
    border-bottom: 1px solid #e0e0e0;
    padding-bottom: 20px;
}

#pembayaran .school-logo {
    width: 80px;
}

#pembayaran .school-title p {
    font-size: 14px;
    font-weight: 700;
    color: #000;
    margin: 0;
}

#pembayaran .school-title h2 {
    font-size: 28px;
    font-weight: 700;
    color: #000;
    margin: 0;
}

#pembayaran .page-title {
    margin: 20px 0 30px 0;
}

#pembayaran .page-title h2 {
    font-size: 20px;
    color: #006837;
    font-weight: 700;
    margin-bottom: 5px;
}

#pembayaran .page-title p {
    font-size: 15px;
    color: #000;
}

/* Two columns */
#pembayaran .payment-two-columns {
    display: flex;
    gap: 30px;
    margin-bottom: 40px;
    flex-wrap: wrap;
}

#pembayaran .payment-instructions-card,
#pembayaran .payment-confirmation-card {
    flex: 1;
    background: #FBFBFB;
    border: 1px solid #8C8C8C;
    border-radius: 10px;
    padding: 20px;
}

#pembayaran .card-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

#pembayaran .icon-badge {
    width: 50px;
    height: 50px;
    background: #006837;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

#pembayaran .header-text h3 {
    font-size: 17px;
    font-weight: 700;
    margin: 0;
    color: #000;
}

#pembayaran .header-text p {
    font-size: 15px;
    margin: 0;
    color: #000;
}

/* Bank info */
#pembayaran .bank-info {
    background: rgba(0, 104, 55, 0.25);
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
}

#pembayaran .bank-detail strong {
    font-size: 20px;
    color: #006837;
}

#pembayaran .account-number {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 25px;
    font-weight: 700;
    color: #00592F;
    margin: 10px 0;
}

#pembayaran .btn-copy {
    background: none;
    border: none;
    cursor: pointer;
    color: #028246;
}

#pembayaran .fee-breakdown .divider {
    height: 1px;
    background: rgba(169,169,169,0.5);
    margin: 15px 0;
}

#pembayaran .breakdown-title {
    font-weight: 700;
    font-size: 10px;
    color: #535353;
}

#pembayaran .fee-item {
    display: flex;
    justify-content: space-between;
    font-size: 16px;
    margin: 8px 0;
}

#pembayaran .fee-name {
    color: #626262;
    font-weight: 500;
}

#pembayaran .fee-amount {
    color: rgba(0,104,55,0.8);
    font-weight: 500;
}

/* Form confirmation */
#pembayaran .form-group {
    margin-bottom: 20px;
}

#pembayaran .form-group label {
    font-weight: 700;
    font-size: 15px;
    color: #535353;
    display: block;
    margin-bottom: 8px;
}

#pembayaran .form-control,
#pembayaran select.form-control {
    width: 100%;
    padding: 8px 12px;
    background: #EDEDED;
    border: 1px solid #BEBEBE;
    border-radius: 10px;
    font-size: 15px;
    color: #333;
}

#pembayaran .upload-area {
    border: 1px dashed #D9D9D9;
    background: rgba(0,0,0,0.02);
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: 0.2s;
}

#pembayaran .upload-area.dragover {
    background: #e8f0fe;
    border-color: #006837;
}

#pembayaran .upload-area i {
    font-size: 32px;
    color: #585858;
}

#pembayaran .upload-area p {
    font-size: 10px;
    color: rgba(0,0,0,0.88);
    margin: 10px 0 0;
}

#pembayaran .file-name-display {
    display: block;
    font-size: 12px;
    color: #006837;
    margin-top: 8px;
}

#pembayaran .btn-submit-kirim {
    background: #3F7659;
    border-radius: 10px;
    width: 100%;
    padding: 10px;
    border: none;
    color: white;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
}

/* History table */
#pembayaran .payment-history h3 {
    font-size: 17px;
    font-weight: 700;
    margin-bottom: 15px;
    color: #000;
}

#pembayaran .history-table-wrapper {
    overflow-x: auto;
}

#pembayaran .history-table {
    width: 100%;
    border-collapse: collapse;
    background: #FBFBFB;
    border: 1px solid #8C8C8C;
    border-radius: 10px;
    font-size: 12px;
}

#pembayaran .history-table th,
#pembayaran .history-table td {
    padding: 12px 10px;
    text-align: left;
    border-bottom: 0.5px solid rgba(169,169,169,0.5);
}

#pembayaran .history-table th {
    font-weight: 700;
    color: #535353;
    background: #FBFBFB;
}

#pembayaran .status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

#pembayaran .status-success {
    background: rgba(0,104,55,0.2);
    color: #006837;
}

#pembayaran .status-pending {
    background: rgba(255,199,88,0.4);
    color: #A87000;
}

#pembayaran .btn-download-kwitansi {
    color: #3F7659;
    text-decoration: none;
    font-weight: 600;
}

#pembayaran .btn-lihat-bukti {
    background: #006837;
    color: white;
    padding: 4px 8px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 600;
    display: inline-block;
    transition: background 0.2s;
}

#pembayaran .btn-lihat-bukti:hover {
    background: #004d28;
}

@media (max-width: 768px) {
    #pembayaran .payment-two-columns {
        flex-direction: column;
    }
    #pembayaran .payment-container {
        padding: 20px;
    }
}
</style>