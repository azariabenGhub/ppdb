{{-- partials/staff-dashboard/verifikasi-daftar-ulang.blade.php --}}
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
</div>

@push('staff-scripts')
<script>
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
                const btnLihatBerkas = `<button onclick="bukaModalDaftarUlang(${du.id}, '${du.user?.name}', '${du.status}')">Lihat Berkas</button>`;
                const btnVerifikasi = `<button onclick="bukaModalDaftarUlang(${du.id}, '${du.user?.name}', 'menunggu')">Verifikasi & Lihat Berkas</button>`;
                html += `<tr>
                    <td>${i + 1}</td>
                    <td>${du.user?.name || '-'}</td>
                    <td>${du.status}</td>
                    <td>
                        ${du.status === 'menunggu' ? btnVerifikasi : btnLihatBerkas}
                        <button onclick="lihatFormulirDaftarUlang(${du.id})">Lihat Formulir</button>
                    </td>
                </tr>`;
            });
            document.getElementById('tabel-daftar-ulang').innerHTML = html || '<tr><td colspan="4">Tidak ada data daftar ulang.</td></tr>';
        } catch (err) {
            console.error(err);
        }
    }

    // Fungsi untuk menampilkan modal formulir daftar ulang
    async function lihatFormulirDaftarUlang(idDaftarUlang) {
        try {
            const res = await fetch(`/api/staff/daftar-ulang-form/${idDaftarUlang}`, {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            if (!res.ok) {
                const err = await res.json();
                alert('Gagal mengambil data: ' + (err.message || 'Unknown error'));
                return;
            }
            const data = await res.json();
            renderFormulirDaftarUlang(data);
            document.getElementById('modalFormulirDaftarUlang').style.display = 'block';
            document.getElementById('overlayFormulirDU').style.display = 'block';
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan saat mengambil data.');
        }
    }

    function renderFormulirDaftarUlang(data) {
        const container = document.getElementById('formulirDaftarUlangContent');
        let html = '';
        if (data.tipe_wali === 'orang_tua') {
            html = `
                <h4>Data Siswa</h4>
                <table style="width:100%; border-collapse:collapse;">
                    <tr><td style="padding:6px;"><strong>Nama Lengkap</strong></td><td>${escapeHtml(data.siswa.nama_lengkap || '-')}</td></tr>
                    <tr><td style="padding:6px;"><strong>Tempat Lahir</strong></td><td>${escapeHtml(data.siswa.tempat_lahir || '-')}</td></tr>
                    <tr><td style="padding:6px;"><strong>Tanggal Lahir</strong></td><td>${escapeHtml(data.siswa.tanggal_lahir || '-')}</td></tr>
                    <tr><td style="padding:6px;"><strong>Jenis Kelamin</strong></td><td>${escapeHtml(data.siswa.jenis_kelamin || '-')}</td></tr>
                    <tr><td style="padding:6px;"><strong>Asal RA/TK/PAUD</strong></td><td>${escapeHtml(data.siswa.asal_sekolah || '-')}</td></tr>
                    <tr><td style="padding:6px;"><strong>Alamat Domisili</strong></td><td>${escapeHtml(data.siswa.alamat_domisili || '-')}</td></tr>
                </table>
                <h4>Data Orang Tua</h4>
                <table style="width:100%; border-collapse:collapse;">
                    <tr><td style="padding:6px;"><strong>Nama Ayah</strong></td><td>${escapeHtml(data.orang_tua.nama_ayah || '-')}</td></tr>
                    <tr><td style="padding:6px;"><strong>Pendidikan Ayah</strong></td><td>${escapeHtml(data.orang_tua.pendidikan_ayah || '-')}</td></tr>
                    <tr><td style="padding:6px;"><strong>Pekerjaan Ayah</strong></td><td>${escapeHtml(data.orang_tua.pekerjaan_ayah || '-')}</td></tr>
                    <tr><td style="padding:6px;"><strong>Alamat KTP (Ayah)</strong></td><td>${escapeHtml(data.orang_tua.alamat_ktp || '-')}</td></tr>
                    <tr><td style="padding:6px;"><strong>No HP Ayah</strong></td><td>${escapeHtml(data.orang_tua.no_hp || '-')}</td></tr>
                    <tr><td style="padding:6px;"><strong>Nama Ibu</strong></td><td>${escapeHtml(data.orang_tua.nama_ibu || '-')}</td></tr>
                    <tr><td style="padding:6px;"><strong>Pendidikan Ibu</strong></td><td>${escapeHtml(data.orang_tua.pendidikan_ibu || '-')}</td></tr>
                    <tr><td style="padding:6px;"><strong>Pekerjaan Ibu</strong></td><td>${escapeHtml(data.orang_tua.pekerjaan_ibu || '-')}</td></tr>
                    <tr><td style="padding:6px;"><strong>Narahubung</strong></td><td>${escapeHtml(data.orang_tua.narahubung || '-')}</td></tr>
                </table>
            `;
        } else if (data.tipe_wali === 'wali') {
            html = `
                <h4>Data Siswa</h4>
                <table style="width:100%; border-collapse:collapse;">
                    <tr><td style="padding:6px;"><strong>Nama Lengkap</strong></td><td>${escapeHtml(data.siswa.nama_lengkap || '-')}</td></tr>
                    <tr><td style="padding:6px;"><strong>Tempat Lahir</strong></td><td>${escapeHtml(data.siswa.tempat_lahir || '-')}</td></tr>
                    <tr><td style="padding:6px;"><strong>Tanggal Lahir</strong></td><td>${escapeHtml(data.siswa.tanggal_lahir || '-')}</td></tr>
                    <tr><td style="padding:6px;"><strong>Jenis Kelamin</strong></td><td>${escapeHtml(data.siswa.jenis_kelamin || '-')}</td></tr>
                    <tr><td style="padding:6px;"><strong>Asal RA/TK/PAUD</strong></td><td>${escapeHtml(data.siswa.asal_sekolah || '-')}</td></tr>
                    <tr><td style="padding:6px;"><strong>Alamat Domisili</strong></td><td>${escapeHtml(data.siswa.alamat_domisili || '-')}</td></tr>
                </table>
                <h4>Data Wali</h4>
                <table style="width:100%; border-collapse:collapse;">
                    <tr><td style="padding:6px;"><strong>Nama Wali</strong></td><td>${escapeHtml(data.wali.nama_wali || '-')}</td></tr>
                    <tr><td style="padding:6px;"><strong>Pendidikan Wali</strong></td><td>${escapeHtml(data.wali.pendidikan_wali || '-')}</td></tr>
                    <tr><td style="padding:6px;"><strong>Pekerjaan Wali</strong></td><td>${escapeHtml(data.wali.pekerjaan_wali || '-')}</td></tr>
                    <tr><td style="padding:6px;"><strong>Alamat KTP (Wali)</strong></td><td>${escapeHtml(data.wali.alamat_ktp || '-')}</td></tr>
                    <tr><td style="padding:6px;"><strong>No HP Wali</strong></td><td>${escapeHtml(data.wali.no_hp || '-')}</td></tr>
                    <tr><td style="padding:6px;"><strong>Narahubung</strong></td><td>${escapeHtml(data.wali.narahubung || '-')}</td></tr>
                </table>
            `;
        } else {
            html = '<p>Data tidak lengkap.</p>';
        }
        container.innerHTML = html;
    }

    function tutupModalFormulirDaftarUlang() {
        document.getElementById('modalFormulirDaftarUlang').style.display = 'none';
        document.getElementById('overlayFormulirDU').style.display = 'none';
    }

    async function bukaModalDaftarUlang(id, nama, status = 'menunggu') {
        document.getElementById('du-id').value = id;
        document.getElementById('du-nama').innerText = nama;
        document.getElementById('du-status').value = 'diterima';
        document.getElementById('du-catatan').value = '';
        document.getElementById('du-catatan-group').style.display = 'none';

        // Tampilkan/sembunyikan form verifikasi & tombol simpan berdasarkan status
        if (status === 'menunggu') {
            document.getElementById('du-verifikasi-form').style.display = 'block';
            document.getElementById('du-simpan-btn').style.display = 'inline-block';
        } else {
            document.getElementById('du-verifikasi-form').style.display = 'none';
            document.getElementById('du-simpan-btn').style.display = 'none';
        }

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

    // Event listener untuk menampilkan catatan jika ditolak (dipasang di sini, karena modal sudah ada di parent)
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
</script>
@endpush