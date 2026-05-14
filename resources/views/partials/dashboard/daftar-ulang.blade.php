{{-- ===== DAFTAR ULANG ===== --}}
<div id="daftar-ulang" class="section" style="display:none;">
    <div class="section-wrapper">
        <h2>Daftar Ulang</h2>
        <div id="konten-daftar-ulang">
            <p>Setelah dinyatakan lulus seleksi, Anda dapat melakukan proses daftar ulang pada halaman ini.
                Pastikan semua berkas persyaratan telah disiapkan sebelum mengunggah.</p>
        </div>
    </div>
</div>

@push('section-scripts')
<script>
    // ========== DAFTAR ULANG: TEMPLATE LINKS ==========
    async function getTemplateLinks() {
        try {
            const resTemplates = await fetch('/api/template-surat', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const templates = await resTemplates.json();
            if (!templates.length) {
                return '<span style="color:#dc2626;">Template surat belum tersedia. Hubungi panitia.</span>';
            }
            return templates.map(t =>
                `<a href="/api/template-surat/download/${t.id}?token=${token}" target="_blank">Download ${t.nama}</a>`
            ).join(' | ');
        } catch (e) {
            return '<span style="color:#dc2626;">Gagal memuat template.</span>';
        }
    }

    async function tampilkanFormDaftarUlang() {
        const templateLinks = await getTemplateLinks();
        return `
            <div><strong>Persyaratan:</strong> Akte kelahiran, Ijazah TK (opsional), KTP orang tua/wali, Kartu Keluarga, scan NISN (jika ada), serta surat pernyataan dan pakta integritas (download template di bawah).</div>
            <br>
            <div>📄 Template Surat: ${templateLinks}</div>
            <hr>
            <form id="formDaftarUlang" enctype="multipart/form-data">
                <label>Akte Kelahiran (PDF/Image):</label><br><input type="file" name="akte_kelahiran" accept=".pdf,.jpg,.png" required><br><br>
                <label>Ijazah TK (opsional):</label><br><input type="file" name="ijazah_tk" accept=".pdf,.jpg,.png"><br><br>
                <label>KTP Orang Tua / Wali:</label><br><input type="file" name="ktp_orang_tua" accept=".pdf,.jpg,.png" required><br><br>
                <label>Kartu Keluarga:</label><br><input type="file" name="kartu_keluarga" accept=".pdf,.jpg,.png" required><br><br>
                <label>Scan NISN (jika punya):</label><br><input type="file" name="nisn_file" accept=".pdf,.jpg,.png"><br><br>
                <label>Surat Pernyataan Orang Tua/Wali (upload hasil scan/tanda tangan):</label><br><input type="file" name="surat_pernyataan" accept=".pdf,.jpg,.png" required><br><br>
                <label>Surat Pakta Integritas Orang Tua/Wali:</label><br><input type="file" name="surat_pakta_integritas" accept=".pdf,.jpg,.png" required><br><br>
                <button type="submit" class="btn-submit">Kirim Daftar Ulang</button>
            </form>
        `;
    }

    function attachFormListener() {
        const form = document.getElementById('formDaftarUlang');
        if (form) {
            form.onsubmit = async (e) => {
                e.preventDefault();
                const formData = new FormData(form);
                const res = await fetch('/api/daftar-ulang', {
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + token },
                    body: formData
                });
                if (res.ok) {
                    alert('Berkas terkirim. Menunggu verifikasi.');
                    loadDaftarUlangSection();
                } else {
                    const err = await res.json();
                    alert('Gagal: ' + (err.message || JSON.stringify(err)));
                }
            };
        }
    }

    async function loadDaftarUlangSection() {
        const container = document.getElementById('konten-daftar-ulang');
        try {
            const cekRes = await fetch('/api/daftar-ulang/cek', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const cek = await cekRes.json();

            if (!cek.eligible) {
                container.innerHTML = `<p>${cek.message}</p>`;
                return;
            }

            if (cek.sudah_mengirim) {
                let statusText = '';
                let catatanPenolakan = '';
                if (cek.status === 'menunggu') statusText = '⏳ Berkas sedang diverifikasi.';
                else if (cek.status === 'diterima') statusText = '✅ Daftar ulang diterima. Selamat bergabung!';
                else if (cek.status === 'ditolak') {
                    statusText = '❌ Daftar ulang ditolak. Silakan perbaiki dan kirim ulang.';
                    const duRes = await fetch('/api/daftar-ulang', {
                        headers: { 'Authorization': 'Bearer ' + token }
                    });
                    const duData = await duRes.json();
                    catatanPenolakan = duData.catatan ? `
                        <div style="background:#fde8e8; padding:12px; border-left:3px solid #dc2626; margin:10px 0;">
                            <strong>Catatan Penolakan:</strong> ${duData.catatan}
                        </div>` : '';
                }

                if (cek.status === 'ditolak') {
                    const formHtml = await tampilkanFormDaftarUlang();
                    container.innerHTML = `<p>Status: ${statusText}</p>${catatanPenolakan}${formHtml}`;
                    attachFormListener();
                } else {
                    container.innerHTML = `<p>Status: ${statusText}</p>`;
                }
                return;
            }

            const formHtml = await tampilkanFormDaftarUlang();
            container.innerHTML = formHtml;
            attachFormListener();
        } catch (e) {
            console.error('Error load daftar ulang:', e);
            container.innerHTML = '<p>Gagal memuat data. Silakan coba lagi.</p>';
        }
    }
</script>
@endpush