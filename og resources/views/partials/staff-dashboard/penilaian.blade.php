{{-- ===== PENILAIAN TES ===== --}}
<div id="penilaian" class="section" style="display:none;">
    <h2>Penilaian Tes</h2>

    <!-- Tabel Pendaftar Menunggu Penilaian -->
    <h3>Pendaftar Menunggu Penilaian</h3>
    <table border="1" width="100%" cellpadding="5">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pendaftar</th>
                <th>Jadwal Tes</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="tabel-belum-dinilai"></tbody>
    </table>

    <!-- Tabel Riwayat Penilaian -->
    <h3>Riwayat Penilaian</h3>
    <table border="1" width="100%" cellpadding="5">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pendaftar</th>
                <th>Membaca</th>
                <th>Menulis</th>
                <th>Berhitung</th>
                <th>Baca Alquran</th>
                <th>Kelulusan</th>
                <th>Penilai</th>
            </tr>
        </thead>
        <tbody id="tabel-riwayat-penilaian"></tbody>
    </table>
</div>

@push('staff-scripts')
<script>
    // ========== PENILAIAN: Fungsi untuk memuat data ==========
    async function loadBelumDinilai() {
        try {
            const res = await fetch('/api/seleksi', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const data = await res.json();
            // Filter yang belum memiliki id_penilaian
            const belum = data.filter(item => !item.id_penilaian && item.pendaftar);
            let html = '';
            if (belum.length === 0) {
                html = '<tr><td colspan="4">Tidak ada pendaftar yang perlu dinilai.</td></tr>';
            } else {
                belum.forEach((item, i) => {
                    html += `<tr>
                        <td>${i + 1}</td>
                        <td>${escapeHtml(item.pendaftar?.name ?? '-')}</td>
                        <td>${item.jadwal_tes ? new Date(item.jadwal_tes).toLocaleString() : '-'}</td>
                        <td><button onclick="bukaModalPenilaian('${item.id_pendaftar}', '${escapeHtml(item.pendaftar?.name)}')">Nilai</button></td>
                    </tr>`;
                });
            }
            document.getElementById('tabel-belum-dinilai').innerHTML = html;
        } catch (err) {
            console.error(err);
            document.getElementById('tabel-belum-dinilai').innerHTML = '<tr><td colspan="4">Gagal memuat data.</td></tr>';
        }
    }

    async function loadRiwayatPenilaian() {
        try {
            const res = await fetch('/api/penilaian', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const data = await res.json();
            let html = '';
            if (data.length === 0) {
                html = '<tr><td colspan="8">Belum ada penilaian.</td></tr>';
            } else {
                data.forEach((n, i) => {
                    html += `<tr>
                        <td>${i + 1}</td>
                        <td>${escapeHtml(n.pendaftar?.name ?? '-')}</td>
                        <td>${escapeHtml(n.kemampuan_membaca ?? '-')}</td>
                        <td>${escapeHtml(n.kemampuan_menulis ?? '-')}</td>
                        <td>${escapeHtml(n.kemampuan_berhitung ?? '-')}</td>
                        <td>${escapeHtml(n.baca_alquran ?? '-')}</td>
                        <td>${escapeHtml(n.seleksi_tes?.kelulusan_tes ?? '-')}</td>
                        <td>${escapeHtml(n.penilai?.name ?? '-')}</td>
                    </tr>`;
                });
            }
            document.getElementById('tabel-riwayat-penilaian').innerHTML = html;
        } catch (err) {
            console.error(err);
            document.getElementById('tabel-riwayat-penilaian').innerHTML = '<tr><td colspan="8">Gagal memuat data.</td></tr>';
        }
    }

    // ========== MODAL PENILAIAN ==========
    function bukaModalPenilaian(idPendaftar, namaPendaftar) {
        document.getElementById('modalIdPendaftar').value = idPendaftar;
        document.getElementById('modalNamaPendaftar').innerText = namaPendaftar;
        // Reset form
        document.getElementById('modalMembaca').value = '';
        document.getElementById('modalMenulis').value = '';
        document.getElementById('modalBerhitung').value = '';
        document.getElementById('modalBacaQuran').value = '';
        document.getElementById('modalCatatanPenilaian').value = '';
        document.getElementById('modalKelulusan').value = 'lulus';
        document.getElementById('modalPenilaian').style.display = 'block';
        document.getElementById('overlayPenilaian').style.display = 'block';
    }

    function tutupModalPenilaian() {
        document.getElementById('modalPenilaian').style.display = 'none';
        document.getElementById('overlayPenilaian').style.display = 'none';
    }

    async function simpanPenilaianModal() {
        const id_pendaftar = document.getElementById('modalIdPendaftar').value;
        const payload = {
            id_pendaftar: id_pendaftar,
            kemampuan_membaca: document.getElementById('modalMembaca').value,
            kemampuan_menulis: document.getElementById('modalMenulis').value,
            kemampuan_berhitung: document.getElementById('modalBerhitung').value,
            baca_alquran: document.getElementById('modalBacaQuran').value,
            catatan: document.getElementById('modalCatatanPenilaian').value,
            kelulusan_tes: document.getElementById('modalKelulusan').value,
        };
        try {
            const res = await fetch('/api/penilaian', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify(payload)
            });
            if (res.ok) {
                alert('Penilaian berhasil disimpan');
                tutupModalPenilaian();
                loadBelumDinilai();
                loadRiwayatPenilaian();
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