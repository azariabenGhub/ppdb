{{-- partials/staff-dashboard/gelombang.blade.php --}}
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
        <input type="number" id="tahun" min="2000" max="2100" value="{{ date('Y') }}"><br><br>
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

@push('staff-scripts')
<script>
    // ========================
    // GELOMBANG
    // ========================
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
        document.getElementById('biaya_formulir').value = '';
        document.getElementById('biaya_daftar_ulang').value = '';
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
</script>
@endpush