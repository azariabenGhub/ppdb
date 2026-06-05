{{-- partials/staff-dashboard/laporan.blade.php --}}
<div id="laporan" class="section" style="display:none;">
    <h2>Laporan</h2>
    <div style="display: flex; gap: 20px; margin-top: 20px;">
        <button onclick="openExportModal()" style="padding: 10px 20px; background: #1a4d2e; color: white; border: none; border-radius: 5px; cursor: pointer;">
            📊 Export Excel Pendaftar Lulus (Pilih Kolom)
        </button>
        <button onclick="openModalFilterDU()" style="padding: 10px 20px; background: #1a4d2e; color: white; border: none; border-radius: 5px; cursor: pointer;">
            📦 Download Arsip Daftar Ulang (ZIP)
        </button>
        <button onclick="openModalFilterPembayaran()" style="padding: 10px 20px; background: #1a4d2e; color: white; border: none; border-radius: 5px; cursor: pointer;">
            💳 Download Arsip Pembayaran (Bukti & Kwitansi)
        </button>
    </div>
    <div id="laporan-message" style="margin-top: 20px; color: #555;"></div>
</div>

@push('staff-scripts')
<script>
    // Data kolom yang tersedia untuk export Excel
    const allExportColumns = [
        { value: 'no_pendaftaran', label: 'No. Induk Pendaftaran' },
        { value: 'nama_pendaftar', label: 'Nama Pendaftar (User)' },
        { value: 'email', label: 'Email' },
        { value: 'nama_siswa', label: 'Nama Siswa' },
        { value: 'tempat_lahir', label: 'Tempat Lahir' },
        { value: 'tanggal_lahir', label: 'Tanggal Lahir' },
        { value: 'jenis_kelamin', label: 'Jenis Kelamin' },
        { value: 'nik', label: 'NIK' },
        { value: 'agama', label: 'Agama' },
        { value: 'warga_negara', label: 'Warga Negara' },
        { value: 'anak_ke', label: 'Anak ke-' },
        { value: 'jumlah_saudara', label: 'Jumlah Saudara' },
        { value: 'alamat', label: 'Alamat Lengkap' },
        { value: 'pernah_tk', label: 'Pernah TK' },
        { value: 'asal_tk', label: 'Asal TK' },
        { value: 'punya_nisn', label: 'Punya NISN' },
        { value: 'nisn', label: 'NISN' },
        { value: 'nama_ayah', label: 'Nama Ayah' },
        { value: 'pekerjaan_ayah', label: 'Pekerjaan Ayah' },
        { value: 'agama_ayah', label: 'Agama Ayah' },
        { value: 'pendidikan_ayah', label: 'Pendidikan Ayah' },
        { value: 'nik_ayah', label: 'NIK Ayah' },
        { value: 'penghasilan_ayah', label: 'Penghasilan Ayah' },
        { value: 'no_telp_ayah', label: 'No. Telp Ayah' },
        { value: 'alamat_ayah', label: 'Alamat Ayah' },
        { value: 'nama_ibu', label: 'Nama Ibu' },
        { value: 'pekerjaan_ibu', label: 'Pekerjaan Ibu' },
        { value: 'agama_ibu', label: 'Agama Ibu' },
        { value: 'pendidikan_ibu', label: 'Pendidikan Ibu' },
        { value: 'nik_ibu', label: 'NIK Ibu' },
        { value: 'penghasilan_ibu', label: 'Penghasilan Ibu' },
        { value: 'no_telp_ibu', label: 'No. Telp Ibu' },
        { value: 'alamat_ibu', label: 'Alamat Ibu' },
        { value: 'tipe_wali', label: 'Tipe Wali' },
        { value: 'nama_wali', label: 'Nama Wali' },
        { value: 'pekerjaan_wali', label: 'Pekerjaan Wali' },
        { value: 'agama_wali', label: 'Agama Wali' },
        { value: 'pendidikan_wali', label: 'Pendidikan Wali' },
        { value: 'nik_wali', label: 'NIK Wali' },
        { value: 'penghasilan_wali', label: 'Penghasilan Wali' },
        { value: 'no_telp_wali', label: 'No. Telp Wali' },
        { value: 'alamat_wali', label: 'Alamat Wali' },
        { value: 'status_formulir', label: 'Status Formulir' },
        { value: 'kelulusan', label: 'Kelulusan Tes' },
        { value: 'jadwal_tes', label: 'Jadwal Tes' },
        { value: 'kemampuan_membaca', label: 'Kemampuan Membaca' },
        { value: 'kemampuan_menulis', label: 'Kemampuan Menulis' },
        { value: 'kemampuan_berhitung', label: 'Kemampuan Berhitung' },
        { value: 'baca_alquran', label: 'Baca Alquran' },
        { value: 'narahubung', label: 'Narahubung (Daftar Ulang)' },
        { value: 'alamat_domisili', label: 'Alamat Domisili (Daftar Ulang)' },
    ];

    // ========== EXPORT EXCEL ==========
    async function loadExportFilters() {
        // Tahun
        const tahunRes = await fetch('/api/pendaftar/tahun-options', { headers: { 'Authorization': 'Bearer ' + token } });
        if (tahunRes.ok) {
            const tahunList = await tahunRes.json();
            let tahunHtml = '<option value="">Semua Tahun</option>';
            tahunList.forEach(t => tahunHtml += `<option value="${t}">${t}</option>`);
            document.getElementById('export-filter-tahun').innerHTML = tahunHtml;
        }
        // Gelombang
        const gelRes = await fetch('/api/gelombang', { headers: { 'Authorization': 'Bearer ' + token } });
        const gelData = await gelRes.json();
        let gelHtml = '<option value="">Semua Gelombang</option>';
        gelData.forEach(g => gelHtml += `<option value="${g.id}">Gelombang ${g.nomor_gelombang} - ${g.tahun}</option>`);
        document.getElementById('export-filter-gelombang').innerHTML = gelHtml;
    }

    function renderExportColumnCheckboxes() {
        const container = document.getElementById('export-column-checkboxes');
        container.innerHTML = '';
        allExportColumns.forEach(col => {
            const div = document.createElement('div');
            div.innerHTML = `
                <label>
                    <input type="checkbox" value="${col.value}" class="export-column-checkbox" checked>
                    ${col.label}
                </label>
            `;
            container.appendChild(div);
        });
    }

    function selectAllColumns() {
        document.querySelectorAll('.export-column-checkbox').forEach(cb => cb.checked = true);
    }
    function deselectAllColumns() {
        document.querySelectorAll('.export-column-checkbox').forEach(cb => cb.checked = false);
    }

    function openExportModal() {
        loadExportFilters();
        renderExportColumnCheckboxes();
        document.getElementById('modalExportExcel').style.display = 'block';
        document.getElementById('overlayExport').style.display = 'block';
    }

    function closeExportModal() {
        document.getElementById('modalExportExcel').style.display = 'none';
        document.getElementById('overlayExport').style.display = 'none';
    }

    async function doExportExcel() {
        const filters = {
            tahun: document.getElementById('export-filter-tahun').value,
            gelombang: document.getElementById('export-filter-gelombang').value,
            status_formulir: document.getElementById('export-filter-status-formulir').value,
            kelulusan: document.getElementById('export-filter-kelulusan').value,
            status_daftar_ulang: document.getElementById('export-filter-status-du').value,
            nisn: document.getElementById('export-filter-nisn').value,
            search: document.getElementById('export-search').value
        };
        const selectedColumns = [];
        document.querySelectorAll('.export-column-checkbox:checked').forEach(cb => {
            selectedColumns.push(cb.value);
        });
        try {
            const res = await fetch('/api/laporan/export-excel', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify({ columns: selectedColumns, ...filters })
            });
            if (res.ok) {
                const blob = await res.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'pendaftar_lulus.xlsx';
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
                closeExportModal();
            } else {
                const err = await res.json();
                alert('Gagal export: ' + (err.message || 'Unknown error'));
            }
        } catch (err) {
            alert('Error: ' + err.message);
        }
    }

    // ========== ARSIP DAFTAR ULANG ==========
    async function loadTahunDU() {
        try {
            const res = await fetch('/api/laporan/daftar-ulang/tahun-options', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const tahunList = await res.json();
            let html = '<option value="">Semua Tahun</option>';
            tahunList.forEach(t => html += `<option value="${t}">${t}</option>`);
            document.getElementById('filter-tahun-du').innerHTML = html;
        } catch(e) { console.error(e); }
    }

    async function loadGelombangDU() {
        try {
            const res = await fetch('/api/laporan/daftar-ulang/gelombang-options', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const gelList = await res.json();
            let html = '<option value="">Semua Gelombang</option>';
            gelList.forEach(g => html += `<option value="${g.id}">Gelombang ${g.nomor_gelombang} - ${g.tahun}</option>`);
            document.getElementById('filter-gelombang-du').innerHTML = html;
        } catch(e) { console.error(e); }
    }

    function openModalFilterDU() {
        document.getElementById('modalFilterDU').style.display = 'block';
        document.getElementById('overlayFilterDU').style.display = 'block';
        loadTahunDU();
        loadGelombangDU();
    }

    function tutupModalFilterDU() {
        document.getElementById('modalFilterDU').style.display = 'none';
        document.getElementById('overlayFilterDU').style.display = 'none';
    }

    async function downloadArsipDUWithFilter() {
        const status = document.getElementById('filter-status-du-modal').value;
        const tahun = document.getElementById('filter-tahun-du').value;
        const gelombang = document.getElementById('filter-gelombang-du').value;
        const search = document.getElementById('filter-search-du').value;
        
        let url = '/api/laporan/download-zip-du?';
        const params = [];
        if (status) params.push(`status=${encodeURIComponent(status)}`);
        if (tahun) params.push(`tahun=${encodeURIComponent(tahun)}`);
        if (gelombang) params.push(`gelombang=${encodeURIComponent(gelombang)}`);
        if (search) params.push(`search=${encodeURIComponent(search)}`);
        url += params.join('&');
        
        const msg = document.getElementById('laporan-message');
        msg.innerHTML = '⏳ Sedang mengompres arsip daftar ulang... Proses ini mungkin memakan waktu.';
        try {
            const res = await fetch(url, { headers: { 'Authorization': 'Bearer ' + token } });
            if (res.ok) {
                const blob = await res.blob();
                const blobUrl = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = blobUrl;
                a.download = 'arsip_daftar_ulang.zip';
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(blobUrl);
                msg.innerHTML = '✅ Download arsip daftar ulang berhasil.';
                tutupModalFilterDU();
            } else {
                const err = await res.json();
                msg.innerHTML = '❌ Gagal: ' + (err.message || 'Unknown error');
            }
        } catch (err) {
            msg.innerHTML = '❌ Error: ' + err.message;
        }
    }

    // ========== ARSIP PEMBAYARAN ==========
    async function loadTahunPembayaran() {
        try {
            const res = await fetch('/api/laporan/pembayaran/tahun-options', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const tahunList = await res.json();
            let html = '<option value="">Semua Tahun</option>';
            tahunList.forEach(t => html += `<option value="${t}">${t}</option>`);
            document.getElementById('filter-tahun-pembayaran').innerHTML = html;
        } catch(e) { console.error(e); }
    }

    async function loadGelombangPembayaran() {
        try {
            const res = await fetch('/api/laporan/pembayaran/gelombang-options', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const gelList = await res.json();
            let html = '<option value="">Semua Gelombang</option>';
            gelList.forEach(g => html += `<option value="${g.id}">Gelombang ${g.nomor_gelombang} - ${g.tahun}</option>`);
            document.getElementById('filter-gelombang-pembayaran').innerHTML = html;
        } catch(e) { console.error(e); }
    }

    function openModalFilterPembayaran() {
        loadTahunPembayaran();
        loadGelombangPembayaran();
        document.getElementById('modalFilterPembayaran').style.display = 'block';
        document.getElementById('overlayFilterPembayaran').style.display = 'block';
    }

    function tutupModalFilterPembayaran() {
        document.getElementById('modalFilterPembayaran').style.display = 'none';
        document.getElementById('overlayFilterPembayaran').style.display = 'none';
    }

    async function downloadArsipPembayaranWithFilter() {
        const jenis = document.getElementById('filter-jenis-pembayaran').value;
        const tahun = document.getElementById('filter-tahun-pembayaran').value;
        const gelombang = document.getElementById('filter-gelombang-pembayaran').value;
        
        let url = '/api/laporan/download-zip-pembayaran?';
        const params = [];
        if (jenis) params.push(`jenis=${encodeURIComponent(jenis)}`);
        if (tahun) params.push(`tahun=${encodeURIComponent(tahun)}`);
        if (gelombang) params.push(`gelombang=${encodeURIComponent(gelombang)}`);
        url += params.join('&');
        
        const msg = document.getElementById('laporan-message');
        msg.innerHTML = '⏳ Sedang mengompres arsip pembayaran... Proses ini mungkin memakan waktu.';
        try {
            const res = await fetch(url, { headers: { 'Authorization': 'Bearer ' + token } });
            if (res.ok) {
                const blob = await res.blob();
                const blobUrl = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = blobUrl;
                a.download = 'arsip_pembayaran.zip';
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(blobUrl);
                msg.innerHTML = '✅ Download arsip pembayaran berhasil.';
                tutupModalFilterPembayaran();
            } else {
                const err = await res.json();
                msg.innerHTML = '❌ Gagal: ' + (err.message || 'Unknown error');
            }
        } catch (err) {
            msg.innerHTML = '❌ Error: ' + err.message;
        }
    }
</script>
@endpush