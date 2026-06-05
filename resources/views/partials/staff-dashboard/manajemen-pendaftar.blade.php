{{-- partials/staff-dashboard/manajemen-pendaftar.blade.php --}}
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
            <label>Status NISN: </label><br>
            <select id="filter-nisn" style="padding: 6px 12px;">
                <option value="">Semua</option>
                <option value="ya">Sudah Punya NISN</option>
                <option value="tidak">Belum Punya NISN</option>
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
            <input type="text" id="search-input" placeholder="Ketik kata kunci..." style="padding: 6px 12px; width: 220px;">
        </div>
        <div>
            <button onclick="loadPendaftar()" style="padding: 7px 18px; margin-bottom: 15px;">Terapkan Filter</button>
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
            <tr><td colspan="7" style="text-align:center;">Memuat data...</td></tr>
        </tbody>
    </table>

    <!-- Pagination -->
    <div id="pagination-pendaftar" style="margin-top: 25px; display: flex; justify-content: center; gap: 8px;"></div>
</div>

@push('staff-scripts')
<script>
    // ========================
    // MANAJEMEN PENDAFTAR
    // ========================
    let currentPendaftarId = null;

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
        const filterNisn = document.getElementById('filter-nisn').value;
        
        let url = `/api/pendaftar?page=${page}&sort_by=${sortBy}&sort_order=${sortOrder}`;
        if (tahun) url += `&tahun=${tahun}`;
        if (gelombang) url += `&gelombang=${gelombang}`;
        if (statusFormulir) url += `&status_formulir=${statusFormulir}`;
        if (filterNisn) url += `&filter_nisn=${filterNisn}`;
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
            
            // Cek apakah role yang login adalah kepala_sekolah
            const canDelete = (user.role === 'kepala_sekolah');
            const deleteButton = canDelete ? `<button onclick="hapusPendaftar(${p.id})" style="color:red; margin-left:8px;">Hapus</button>` : '';

            html += `<tr>
                <td>${escapeHtml(noInduk)}</td>
                <td>${escapeHtml(p.name)}</td>
                <td>${escapeHtml(p.email)}</td>
                <td>${statusForm}</td>
                <td>${kelulusan}</td>
                <td>${statusDu}</td>
                <td>
                    <button onclick="window.open('/staff/pendaftar/${p.id}', '_blank')">Detail</button>
                    ${deleteButton}
                 </td>
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

    async function lihatDetailPendaftar(id) {
        currentPendaftarId = id;
        try {
            const res = await fetch(`/api/pendaftar/${id}`, {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const userDetail = await res.json();

            let statusForm = '-';
            if (userDetail.status_formulir === 'belum_isi') statusForm = 'Belum mengisi formulir';
            else if (userDetail.status_formulir === 'menunggu') statusForm = 'Menunggu verifikasi';
            else if (userDetail.status_formulir === 'diterima') statusForm = 'Diterima';
            else if (userDetail.status_formulir === 'ditolak') statusForm = 'Ditolak';
            else statusForm = userDetail.status_formulir || '-';

            let kelulusan = '-';
            if (userDetail.kelulusan === 'lulus') kelulusan = 'Lulus';
            else if (userDetail.kelulusan === 'tidak_lulus') kelulusan = 'Tidak Lulus';
            else if (userDetail.kelulusan === null) kelulusan = 'Belum ada jadwal/nilai';
            else kelulusan = userDetail.kelulusan;

            let statusDU = '-';
            if (userDetail.status_daftar_ulang === 'menunggu') statusDU = 'Menunggu verifikasi';
            else if (userDetail.status_daftar_ulang === 'diterima') statusDU = 'Diterima';
            else if (userDetail.status_daftar_ulang === 'ditolak') statusDU = 'Ditolak';
            else if (userDetail.status_daftar_ulang === null) statusDU = 'Belum daftar ulang';
            else statusDU = userDetail.status_daftar_ulang;

            let detailHtml = `
                <table style="width:100%; border-collapse:collapse;">
                    <tr><td style="padding:8px; width:160px;"><strong>Nama:</strong></td><td>${escapeHtml(userDetail.name)}</td></tr>
                    <tr><td style="padding:8px;"><strong>Email:</strong></td><td>${escapeHtml(userDetail.email)}</td></tr>
                    <tr><td style="padding:8px;"><strong>No. Induk Pendaftaran:</strong></td><td>${escapeHtml(userDetail.no_pendaftaran || '-')}</td></tr>
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

    async function hapusPendaftar(id) {
        if (!confirm('Anda yakin ingin menghapus pendaftar ini? Semua data termasuk formulir, pembayaran, jadwal tes, penilaian, dan dokumen daftar ulang akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.')) {
            return;
        }
        try {
            const res = await fetch(`/api/pendaftar/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const result = await res.json();
            if (res.ok) {
                alert(result.message || 'Pendaftar berhasil dihapus.');
                loadPendaftar();
            } else {
                alert('Gagal: ' + (result.message || 'Terjadi kesalahan.'));
            }
        } catch (err) {
            alert('Error: ' + err.message);
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
</script>
@endpush