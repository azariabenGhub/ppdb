{{-- partials/staff-dashboard/template-surat.blade.php --}}
<div id="template-surat" class="section" style="display:none;">
    <h2>Manajemen Template Surat</h2>
    <button onclick="tampilkanFormTemplate()">Tambah Template</button>
    <br><br>
    <div id="form-template" style="display:none;">
        <h3 id="form-template-title">Tambah Template</h3>
        <input type="hidden" id="template-id">
        <label>Nama Template:</label><br>
        <select id="template-nama">
            <option value="Surat Pernyataan">Surat Pernyataan Orang Tua/Wali</option>
            <option value="Pakta Integritas">Pakta Integritas Orang Tua/Wali</option>
        </select><br><br>
        <label>File (PDF/DOC/DOCX):</label><br>
        <input type="file" id="template-file" accept=".pdf,.doc,.docx"><br><br>
        <button onclick="simpanTemplate()">Simpan</button>
        <button onclick="batalFormTemplate()">Batal</button>
    </div>
    <table border="1">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Template</th>
                <th>Download</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="tabel-template"></tbody>
    </table>
</div>

@push('staff-scripts')
<script>
    // Template Surat
    async function loadTemplateSurat() {
        const res = await fetch('/api/template-surat', { headers: { 'Authorization': 'Bearer ' + token } });
        const data = await res.json();
        let html = '';
        if (data.length === 0) {
            html = '<tr><td colspan="4" align="center">Belum ada template surat</td></tr>';
        } else {
            data.forEach((t, i) => {
                html += `<tr>
                    <td>${i + 1}</td>
                    <td>${t.nama}</td>
                    <td><a href="/api/template-surat/download/${t.id}?token=${token}" target="_blank">Download</a></td>
                    <td><button onclick="editTemplate(${t.id}, '${t.nama}')">Edit</button>
                           <button onclick="hapusTemplate(${t.id})">Hapus</button></td>
                </tr>`;
            });
        }
        document.getElementById('tabel-template').innerHTML = html;
    }

    function tampilkanFormTemplate() {
        document.getElementById('form-template').style.display = 'block';
        document.getElementById('form-template-title').innerText = 'Tambah Template';
        document.getElementById('template-id').value = '';
        document.getElementById('template-nama').value = 'Surat Pernyataan';
        document.getElementById('template-file').value = '';
    }

    function batalFormTemplate() {
        document.getElementById('form-template').style.display = 'none';
    }

    async function simpanTemplate() {
        const id = document.getElementById('template-id').value;
        const formData = new FormData();
        formData.append('nama', document.getElementById('template-nama').value);
        const fileInput = document.getElementById('template-file');
        if (fileInput.files.length > 0) formData.append('file', fileInput.files[0]);
        const url = id ? `/api/template-surat/${id}` : '/api/template-surat';
        const method = id ? 'POST' : 'POST';
        if (id) formData.append('_method', 'PUT');
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Authorization': 'Bearer ' + token },
            body: formData
        });
        if (res.ok) {
            alert('Template tersimpan');
            batalFormTemplate();
            loadTemplateSurat();
        } else {
            alert('Gagal menyimpan template');
        }
    }

    async function editTemplate(id, nama) {
        document.getElementById('template-id').value = id;
        document.getElementById('template-nama').value = nama;
        document.getElementById('template-file').value = '';
        document.getElementById('form-template').style.display = 'block';
        document.getElementById('form-template-title').innerText = 'Edit Template';
    }

    async function hapusTemplate(id) {
        confirmAction('Yakin hapus template?', async () => {
            const res = await fetch(`/api/template-surat/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': 'Bearer ' + token }
            });
            if (res.ok) {
                alert('Dihapus');
                loadTemplateSurat();
            } else {
                alert('Gagal menghapus');
            }
        });
    }
</script>
@endpush