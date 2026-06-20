{{-- ===== METODE PEMBAYARAN ===== --}}
<div id="metode-pembayaran" class="section" style="display:none;">
    <h2>Metode Pembayaran</h2>
    <button onclick="tampilkanFormMetode()">Tambah Metode</button>

    <!-- Form Tambah -->
    <div id="form-metode" style="display:none;">
        <form id="formMetode" enctype="multipart/form-data">
            <input type="text" name="nama_bank" placeholder="Nama Bank"><br>
            <input type="text" name="nomor_rekening" placeholder="Nomor Rekening"><br>
            <input type="text" name="atas_nama" placeholder="Atas Nama"><br>
            <input type="file" name="gambar_qris" accept="image/*"><br>
            <textarea name="keterangan" placeholder="Keterangan"></textarea><br>
            <button type="submit">Simpan</button>
        </form>
    </div>

    <!-- Form Edit -->
    <div id="form-edit-metode" style="display:none;">
        <h3>Edit Metode</h3>
        <form id="formEditMetode" enctype="multipart/form-data">
            <input type="hidden" id="edit_id">
            <input type="text" name="nama_bank" id="edit_nama_bank" placeholder="Nama Bank"><br>
            <input type="text" name="nomor_rekening" id="edit_nomor_rekening" placeholder="Nomor Rekening"><br>
            <input type="text" name="atas_nama" id="edit_atas_nama" placeholder="Atas Nama"><br>
            <input type="file" name="gambar_qris" id="edit_gambar_qris" accept="image/*"><br>
            <div id="container_edit_preview_gambar" style="display:none;">
                <label>Gambar Saat Ini:</label><br>
                <img id="edit_preview_gambar" width="100"><br>
            </div>
            <textarea name="keterangan" id="edit_keterangan" placeholder="Keterangan"></textarea><br>
            <button type="submit">Update</button>
            <button type="button" onclick="batalEdit()">Batal</button>
        </form>
    </div>

    <hr>
    <div id="daftar-metode">Memuat...</div>
</div>

@push('staff-scripts')
<script>
    // ========== METODE PEMBAYARAN ==========
    async function loadMetodePembayaran() {
        try {
            const res = await fetch('/api/metode-pembayaran?_t=' + Date.now(), {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const data = await res.json();
            let html = '';
            data.forEach(m => {
                let imgHtml = '';
                if (m.gambar_qris) {
                    imgHtml = `<img src="/api/file/metode/${m.id}?token=${token}&_t=${Date.now()}" width="100"><br>`;
                }
                html += `<div>
                    <strong>${escapeHtml(m.nama_bank || 'QRIS')}</strong><br>
                    No: ${escapeHtml(m.nomor_rekening || '-')}<br>
                    Atas Nama: ${escapeHtml(m.atas_nama || '-')}<br>
                    Keterangan: ${escapeHtml(m.keterangan || '')}<br>
                    ${imgHtml}
                    <button onclick="editMetode(${m.id}, '${escapeHtml(m.nama_bank || '')}', '${escapeHtml(m.nomor_rekening || '')}', '${escapeHtml(m.atas_nama || '')}', '${escapeHtml(m.keterangan || '')}', ${m.gambar_qris ? 'true' : 'false'})">Edit</button>
                    <button onclick="hapusMetode(${m.id})">Hapus</button>
                    <hr></div>`;
            });
            document.getElementById('daftar-metode').innerHTML = html || 'Belum ada metode.';
        } catch (err) {
            console.error(err);
        }
    }

    function tampilkanFormMetode() {
        document.getElementById('form-metode').style.display = 'block';
        document.getElementById('form-edit-metode').style.display = 'none';
    }

    function batalEdit() {
        document.getElementById('form-edit-metode').style.display = 'none';
        document.getElementById('form-metode').style.display = 'none';
    }

    function editMetode(id, nama_bank, nomor_rekening, atas_nama, keterangan, has_gambar) {
        document.getElementById('form-metode').style.display = 'none';
        document.getElementById('form-edit-metode').style.display = 'block';
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nama_bank').value = nama_bank;
        document.getElementById('edit_nomor_rekening').value = nomor_rekening;
        document.getElementById('edit_atas_nama').value = atas_nama;
        document.getElementById('edit_keterangan').value = keterangan;
        const previewContainer = document.getElementById('container_edit_preview_gambar');
        const previewImg = document.getElementById('edit_preview_gambar');
        if (has_gambar) {
            previewImg.src = `/api/file/metode/${id}?token=${token}&_t=${Date.now()}`;
            previewContainer.style.display = 'block';
        } else {
            previewContainer.style.display = 'none';
        }
    }

    async function hapusMetode(id) {
        confirmAction('Yakin ingin menghapus?', async () => {
            try {
                const res = await fetch(`/api/metode-pembayaran/${id}`, {
                    method: 'DELETE',
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                if (res.ok) {
                    alert('Metode dihapus.');
                    loadMetodePembayaran();
                } else {
                    alert('Gagal menghapus.');
                }
            } catch (error) {
                console.error(error);
            }
        });
    }

    // Event listener untuk form tambah
    document.getElementById('formMetode')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        try {
            const res = await fetch('/api/metode-pembayaran', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token },
                body: formData
            });
            if (res.ok) {
                alert('Metode disimpan!');
                this.reset();
                document.getElementById('form-metode').style.display = 'none';
                loadMetodePembayaran();
            } else {
                const err = await res.json();
                alert('Gagal: ' + JSON.stringify(err));
            }
        } catch (error) {
            console.error(error);
        }
    });

    // Event listener untuk form edit
    document.getElementById('formEditMetode')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const id = document.getElementById('edit_id').value;
        const formData = new FormData(this);
        formData.append('_method', 'PUT');
        try {
            const res = await fetch(`/api/metode-pembayaran/${id}`, {
                method: 'POST', // karena PUT tidak support multipart secara native di form, gunakan POST dengan _method
                headers: { 'Authorization': 'Bearer ' + token },
                body: formData
            });
            // Jika API menggunakan PUT, bisa juga tambahkan formData.append('_method', 'PUT')
            if (res.ok) {
                alert('Metode diperbarui!');
                batalEdit();
                loadMetodePembayaran();
            } else {
                const err = await res.json();
                alert('Gagal update: ' + JSON.stringify(err));
            }
        } catch (error) {
            console.error(error);
        }
    });
</script>
@endpush