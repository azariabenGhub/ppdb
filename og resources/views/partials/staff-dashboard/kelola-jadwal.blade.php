{{-- ===== KELOLA JADWAL TES ===== --}}
<div id="kelola-jadwal" class="section" style="display:none;">
    <h2>Kelola Jadwal Tes</h2>

    <!-- Tabel 1: Belum Terjadwal -->
    <h3>Pendaftar Belum Terjadwal</h3>
    <table border="1" width="100%" cellpadding="5">
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

    <!-- Tabel 2: Sudah Terjadwal -->
    <h3>Pendaftar Sudah Terjadwal</h3>
    <table border="1" width="100%" cellpadding="5">
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

    <!-- Form Jadwal -->
    <div id="form-jadwal-container" style="display:none; border:1px solid #ccc; padding:10px; margin-top:10px; background:#f9f9f9;">
        <h3 id="form-jadwal-title">Atur Jadwal Tes</h3>
        <input type="hidden" id="jadwal-action" value="new">
        <input type="hidden" id="jadwal-id" value="">
        <input type="hidden" id="jadwal-nama" value="">
        <label>Pendaftar: <span id="jadwal-nama-tampil"></span></label><br><br>
        <label>Jadwal Tes:</label>
        <input type="datetime-local" id="jadwal-datetime" style="width:200px;"><br><br>
        <button onclick="submitJadwal()">Simpan</button>
        <button onclick="tutupFormJadwal()">Batal</button>
    </div>
</div>

@push('staff-scripts')
<script>
    // ========== KELOLA JADWAL TES ==========
    async function loadBelumTerjadwal() {
        const res = await fetch('/api/seleksi/eligible', {
            headers: { 'Authorization': 'Bearer ' + token }
        });
        const data = await res.json();
        let html = '';
        data.forEach((item, i) => {
            html += `<tr>
                <td>${i + 1}</td>
                <td>${escapeHtml(item.name)}</td>
                <td>${escapeHtml(item.email)}</td>
                <td><button onclick="aturJadwalBaru('${item.id}', '${escapeHtml(item.name)}')">Atur Jadwal</button></td>
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
                <td>${escapeHtml(item.pendaftar?.name ?? '-')}</td>
                <td>${escapeHtml(item.jadwal_tes)}</td>
                <td>${escapeHtml(item.penjadwal?.name ?? '-')}</td>
                <td>${escapeHtml(item.kelulusan_tes ?? '-')}</td>
                <td><button onclick="ubahJadwal('${item.id_seleksi_tes}', '${escapeHtml(item.jadwal_tes)}', '${escapeHtml(item.pendaftar?.name)}')">Ubah Jadwal</button></td>
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
        document.getElementById('jadwal-datetime').value = '';
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
            datetimeValue = date + 'T' + time.substring(0, 5);
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
</script>
@endpush