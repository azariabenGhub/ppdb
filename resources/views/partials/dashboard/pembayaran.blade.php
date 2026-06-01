{{-- ===== PEMBAYARAN ===== --}}
<div id="pembayaran" class="section" style="display:none;">
    <div class="section-wrapper">
        <h2>Pembayaran</h2>

        <h3 style="font-size:1rem; color:#333; margin-bottom:15px; font-weight:600;">Metode Pembayaran</h3>
        <div id="metode-pembayaran-list" class="payment-methods-grid">
            <p style="color:#888; font-size:0.9rem;">Memuat...</p>
        </div>

        <div class="upload-section">
            <h3>Upload Bukti Pembayaran</h3>
            <form id="form-bukti" enctype="multipart/form-data">
                <label style="font-size:0.85rem; font-weight:500; color:#444; margin-bottom:6px; display:block;">Jenis Pembayaran</label>
                <select name="jenis_pembayaran" id="jenis_pembayaran">
                    <option value="formulir">Pembayaran Formulir</option>
                    <option value="daftar_ulang" disabled>Pembayaran Masuk (menunggu formulir diterima)</option>
                </select>

                <label style="font-size:0.85rem; font-weight:500; color:#444; margin-bottom:6px; display:block;">Bukti Pembayaran</label>
                <input type="file" name="bukti_pembayaran" accept="image/*" required>

                <button type="submit" class="btn-submit" style="margin-top:10px;">Kirim Bukti</button>
            </form>
        </div>

        <div style="margin-top:30px;">
            <h3 style="font-size:1rem; color:#333; margin-bottom:10px; font-weight:600;">Riwayat Pembayaran & Status</h3>
            <div id="riwayat-bukti" style="overflow-x:auto;">
                <p style="color:#888; font-size:0.9rem;">Memuat...</p>
            </div>
        </div>
    </div>
</div>

@push('section-scripts')
<script>
    // ========== PEMBAYARAN: LOAD METODE ==========
    async function loadMetodeUntukPendaftar() {
        const res = await fetch('/api/metode-pembayaran', {
            headers: { 'Authorization': 'Bearer ' + token }
        });
        const data = await res.json();
        let html = '';
        data.forEach(m => {
            let imgHtml = '';
            if (m.gambar_qris) {
                imgHtml =
                    `<img src="/api/file/metode/${m.id}?token=${token}" width="500" style="display:block; margin-top:6px;">`;
            }
            html += `<div class="payment-method-card">
                <strong>${m.nama_bank || 'QRIS'}</strong>
                <small>No Rek: ${m.nomor_rekening || '-'} (${m.atas_nama || '-'})</small>
                ${imgHtml}
                <small>${m.keterangan || ''}</small>
            </div>`;
        });
        document.getElementById('metode-pembayaran-list').innerHTML = html || '<p>Belum ada metode</p>';
    }

    // ========== PEMBAYARAN: UPLOAD BUKTI ==========
    document.getElementById('form-bukti').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const res = await fetch('/api/bukti-pembayaran', {
            method: 'POST',
            headers: { 'Authorization': 'Bearer ' + token },
            body: formData
        });
        if (res.ok) {
            alert('Bukti dikirim');
            loadRiwayatBukti();
        }
    });

    // ========== PEMBAYARAN: RIWAYAT BUKTI ==========
    async function loadRiwayatBukti() {
        const res = await fetch('/api/bukti-pembayaran', {
            headers: { 'Authorization': 'Bearer ' + token }
        });
        const data = await res.json();
        let html =
            '<table class="riwayat-table"><thead><tr><th>Jenis</th><th>Status</th><th>Bukti</th><th>Kwitansi</th><th>Catatan</th></tr></thead><tbody>';
        data.forEach(b => {
            const buktiLink =
                `<a href="/api/file/bukti/${b.id_bukti_pembayaran}?token=${token}" target="_blank">Lihat Bukti</a>`;
            let kwitansiLink = '-';
            if (b.verifikasi?.kwitansi) {
                kwitansiLink =
                    `<a href="/api/file/kwitansi/${b.verifikasi.kwitansi.id_kwitansi}?token=${token}" target="_blank">Unduh Kwitansi</a>`;
            }
            html += `<tr>
                <td>${b.jenis_pembayaran}</td>
                <td>${b.status}</td>
                <td>${buktiLink}</td>
                <td>${kwitansiLink}</td>
                <td>${b.verifikasi?.catatan || ''}</td>
            </tr>`;
        });
        html += '</tbody></table>';
        document.getElementById('riwayat-bukti').innerHTML = html;
    }

    // ========== PEMBAYARAN: CEK STATUS ==========
    async function cekStatusPendaftaran() {
        const res = await fetch('/api/formulir-saya', {
            headers: { 'Authorization': 'Bearer ' + token }
        });
        const result = await res.json();
        if (result.data && result.data.status === 'diterima') {
            const daftarUlangOption = document.querySelector('#jenis_pembayaran option[value="daftar_ulang"]');
            if (daftarUlangOption) {
                daftarUlangOption.disabled = false;
            }
        }
    }
</script>
@endpush