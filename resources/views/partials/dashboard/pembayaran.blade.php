{{-- ===== PEMBAYARAN ===== --}}
<div id="pembayaran" class="section" style="display:none;">
    <div class="content-wrapper animate-fade-in">
        
        <!-- HEADER LOGO YAYASAN -->
        <div class="school-brand-header">
            <img src="{{ asset('storage/assets/logo-mizi.png') }}" alt="Logo Yayasan">
            <div class="brand-text">
                <p>YAYASAN PENDIDIKAN ISLAM ZIYADATUL IHSAN</p>
                <h2>MADRASAH IBTIDAIYAH</h2>
            </div>
        </div>

        <!-- MAIN PAYMENT CARD -->
        <div class="form-card animate-slide-up mt-20">
            <div class="form-title">
                <h3 class="title-green">Pembayaran</h3>
                <p>Bayar dan verifikasi biaya pendaftaran</p>
            </div>

            <div class="payment-grid">
                <!-- LEFT PANEL: INSTRUCTIONS -->
                <div class="payment-panel">
                    <div class="panel-header">
                        <div class="panel-icon"><i class="fa-solid fa-building-columns"></i></div>
                        <div class="panel-title-text">
                            <h4>Instruksi Pembayaran</h4>
                            <span>Transfer ke rekening di bawah ini:</span>
                        </div>
                    </div>

                    <!-- Dynamic container for Bank Methods -->
                    <div id="metode-pembayaran-list">
                        <p style="color:#888; font-size:0.9rem;">Memuat rekening...</p>
                    </div>

                    <!-- Static Cost Breakdown -->
                    <div class="cost-breakdown mt-30">
                        <h6>Rincian Tahapan Biaya:</h6>
                        <ul>
                            <li>
                                <div class="cost-item">
                                    <span class="cost-label"><i class="fa-solid fa-circle"></i> Tahap 1: Biaya Formulir</span>
                                    <span class="cost-value">Rp 50.000</span>
                                </div>
                            </li>
                            <li>
                                <div class="cost-item">
                                    <span class="cost-label"><i class="fa-solid fa-circle"></i> Tahap 2: Biaya Daftar Ulang</span>
                                    <span class="cost-value">Rp 1.500.000</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- RIGHT PANEL: UPLOAD FORM -->
                <div class="payment-panel">
                    <div class="panel-header">
                        <div class="panel-icon"><i class="fa-solid fa-file-invoice"></i></div>
                        <div class="panel-title-text">
                            <h4>Konfirmasi Pembayaran</h4>
                            <span>Kirim Bukti Pembayaran</span>
                        </div>
                    </div>

                    <form id="form-bukti" enctype="multipart/form-data" class="payment-form">
                        <div class="input-group">
                            <label>Jenis Pembayaran</label>
                            <select name="jenis_pembayaran" id="jenis_pembayaran" class="form-select" required>
                                <option value="" disabled selected>Pilih Jenis Pembayaran</option>
                                <option value="formulir">Biaya Formulir</option>
                                <option value="daftar_ulang" disabled>Biaya Daftar Ulang (Menunggu Kelulusan)</option>
                            </select>
                        </div>

                        <div class="input-group mt-15">
                            <label>Tanggal Transfer</label>
                            <input type="text" name="tanggal_transfer" class="form-input" placeholder="Format: dd/mm/yyyy (Contoh: 21/12/2021)">
                        </div>

                        <div class="input-group mt-15">
                            <label>Bukti Transfer (JPG/PNG/PDF)</label>
                            <div class="file-drop-area">
                                <i class="fa-solid fa-inbox"></i>
                                <span class="file-msg">Klik atau seret file ke sini untuk mengunggah</span>
                                <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" accept="image/*,.pdf" required>
                            </div>
                        </div>

                        <button type="submit" class="btn-solid btn-full mt-20">Kirim Konfirmasi</button>
                    </form>
                </div>
            </div>

            <!-- BOTTOM PANEL: PAYMENT HISTORY -->
            <div class="payment-history-panel mt-30">
                <h4>Riwayat Pembayaran</h4>
                <div class="table-responsive" id="riwayat-bukti">
                    <p style="color:#888; font-size:0.9rem;">Memuat riwayat...</p>
                </div>
            </div>
            
        </div>
    </div>
</div>

@push('section-scripts')
<script>
    // Drag & Drop UI Update
    const fileInput = document.getElementById('bukti_pembayaran');
    const fileMsg = document.querySelector('.file-msg');
    fileInput.addEventListener('change', function() {
        if(this.files && this.files.length > 0) {
            fileMsg.textContent = this.files[0].name;
            fileMsg.style.color = '#1a4d2e';
            fileMsg.style.fontWeight = 'bold';
        } else {
            fileMsg.textContent = 'Klik atau seret file ke sini untuk mengunggah';
            fileMsg.style.color = '#888';
            fileMsg.style.fontWeight = 'normal';
        }
    });

    // ========== PEMBAYARAN: LOAD METODE ==========
    async function loadMetodeUntukPendaftar() {
        const res = await fetch('/api/metode-pembayaran', {
            headers: { 'Authorization': 'Bearer ' + token }
        });
        const data = await res.json();
        let html = '';
        data.forEach(m => {
            html += `
            <div class="bank-account-card">
                <h5>${m.nama_bank || 'BANK DKI'}</h5>
                <div class="account-number-wrapper">
                    <h2 class="account-number">${m.nomor_rekening || '1234567891111'}</h2>
                    <button class="btn-copy" title="Salin Rekening"><i class="fa-regular fa-copy"></i></button>
                </div>
                <span class="account-name">Atas Nama: <strong>${m.atas_nama || 'MI ZIYADATUL IHSAN'}</strong></span>
            </div>`;
        });
        document.getElementById('metode-pembayaran-list').innerHTML = html || '<p>Belum ada metode pembayaran tersedia.</p>';
    }

    // ========== PEMBAYARAN: UPLOAD BUKTI ==========
    document.getElementById('form-bukti').addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = e.target.querySelector('button[type="submit"]');
        submitBtn.innerText = 'Mengirim...';
        submitBtn.disabled = true;

        const formData = new FormData(e.target);
        const res = await fetch('/api/bukti-pembayaran', {
            method: 'POST',
            headers: { 'Authorization': 'Bearer ' + token },
            body: formData
        });
        
        submitBtn.innerText = 'Kirim Konfirmasi';
        submitBtn.disabled = false;

        if (res.ok) {
            alert('Bukti pembayaran berhasil dikirim!');
            e.target.reset(); 
            fileMsg.textContent = 'Klik atau seret file ke sini untuk mengunggah';
            fileMsg.style.color = '#888';
            fileMsg.style.fontWeight = 'normal';
            loadRiwayatBukti(); 
        } else {
            alert('Gagal mengirim bukti pembayaran.');
        }
    });

    // ========== PEMBAYARAN: RIWAYAT BUKTI ==========
    async function loadRiwayatBukti() {
        const res = await fetch('/api/bukti-pembayaran', {
            headers: { 'Authorization': 'Bearer ' + token }
        });
        const data = await res.json();
        
        let html = `
        <table class="history-table">
            <thead>
                <tr>
                    <th>ID Transaksi</th>
                    <th>Keterangan</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Kwitansi</th>
                </tr>
            </thead>
            <tbody>`;
            
        data.forEach((b, index) => {
            let badgeClass = 'pending';
            let statusText = 'Menunggu Verifikasi';
            if(b.status === 'terverifikasi' || b.status === 'diterima') { badgeClass = 'verified'; statusText = 'Terverifikasi'; } 
            else if(b.status === 'ditolak') { badgeClass = 'rejected'; statusText = 'Ditolak'; }

            let kwitansiLink = '-';
            if (b.verifikasi?.kwitansi) {
                kwitansiLink = `<a href="/api/file/kwitansi/${b.verifikasi.kwitansi.id_kwitansi}?token=${token}" target="_blank" class="btn-download-sm"><i class="fa-solid fa-download"></i> Unduh</a>`;
            } else if(badgeClass === 'verified'){
                 kwitansiLink = `<a href="#" class="btn-download-sm"><i class="fa-solid fa-download"></i> Unduh</a>`;
            }

            const labelJenis = b.jenis_pembayaran === 'formulir' ? 'Biaya Formulir' : 'Biaya Daftar Ulang';
            const jumlahHarga = b.jenis_pembayaran === 'formulir' ? 'Rp 50.000' : 'Rp 1.500.000';
            
            // Format ID Transaksi Palsu untuk UI
            const idTx = "TF202605" + String(b.id_bukti_pembayaran || (index+1)).padStart(4, '0');
            const tgl = b.created_at ? new Date(b.created_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) : '12 Juni 2026';

            html += `<tr>
                <td style="color:#666;">${idTx}</td>
                <td>${labelJenis}</td>
                <td>${jumlahHarga}</td>
                <td style="color:#666;">${tgl}</td>
                <td><span class="badge-status ${badgeClass}">${statusText}</span></td>
                <td>${kwitansiLink}</td>
            </tr>`;
        });
        
        html += '</tbody></table>';
        if(data.length === 0) { html = '<p style="color:#888; font-size:0.9rem;">Belum ada riwayat transaksi.</p>'; }
        document.getElementById('riwayat-bukti').innerHTML = html;
    }

    // ========== PEMBAYARAN: CEK STATUS ==========
    async function cekStatusPendaftaran() {
        const res = await fetch('/api/formulir-saya', {
            headers: { 'Authorization': 'Bearer ' + token }
        });
        const result = await res.json();
        if (result.data && result.data.status === 'diterima') {
            document.querySelector('#jenis_pembayaran option[value="daftar_ulang"]').disabled = false;
        }
    }
</script>
@endpush