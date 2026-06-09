{{-- partials/staff-dashboard/manajemen-staff.blade.php --}}
<div id="manajemen-staff" class="section" style="display:none;">
    <h2>Manajemen Staff (Panitia & Bendahara)</h2>
    <button onclick="tampilkanFormStaff()">Tambah Staff</button>
    <br><br>

    <!-- Form Tambah/Edit -->
    <div id="form-staff" style="display:none; border:1px solid #ccc; padding:10px; margin-bottom:20px;">
        <h3 id="form-staff-title">Tambah Staff</h3>
        <input type="hidden" id="staff-id">
        <label>Nama Lengkap:</label><br>
        <input type="text" id="staff-name"><br><br>
        <label>Email:</label><br>
        <input type="email" id="staff-email"><br><br>
        <label>Role:</label><br>
        <select id="staff-role">
            <option value="panitia">Panitia PPDB</option>
            <option value="bendahara">Bendahara</option>
        </select><br><br>
        <label>Password:</label><br>
        <input type="password" id="staff-password"><br><br>
        <button onclick="simpanStaff()">Simpan</button>
        <button onclick="batalFormStaff()">Batal</button>
    </div>

    <!-- Daftar Staff -->
    <table border="1" width="100%" cellpadding="5">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="tabel-staff"></tbody>
    </table>
</div>

@push('staff-scripts')
<script>
    // ========================
    // MANAJEMEN STAFF (khusus kepala sekolah)
    // ========================
    async function loadStaff() {
        try {
            const res = await fetch('/api/admin/users', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const data = await res.json();
            let html = '';
            data.forEach((u, i) => {
                let roleLabel = u.role === 'panitia' ? 'Panitia PPDB' : 'Bendahara';
                html += `<tr>
                    <td>${i + 1}</td>
                    <td>${escapeHtml(u.name)}</td>
                    <td>${escapeHtml(u.email)}</td>
                    <td>${escapeHtml(roleLabel)}</td>
                    <td>
                        <button onclick="editStaff(${u.id})">Edit</button>
                        <button onclick="hapusStaff(${u.id})">Hapus</button>
                    </td>
                </tr>`;
            });
            document.getElementById('tabel-staff').innerHTML = html || '<tr><td colspan="5">Tidak ada staff.</td></tr>';
        } catch (err) {
            console.error(err);
        }
    }

    function tampilkanFormStaff() {
        document.getElementById('form-staff').style.display = 'block';
        document.getElementById('form-staff-title').innerText = 'Tambah Staff';
        document.getElementById('staff-id').value = '';
        document.getElementById('staff-name').value = '';
        document.getElementById('staff-email').value = '';
        document.getElementById('staff-role').value = 'panitia';
        document.getElementById('staff-password').value = '';
    }

    function batalFormStaff() {
        document.getElementById('form-staff').style.display = 'none';
    }

    async function simpanStaff() {
        const id = document.getElementById('staff-id').value;
        const payload = {
            name: document.getElementById('staff-name').value,
            email: document.getElementById('staff-email').value,
            role: document.getElementById('staff-role').value,
            password: document.getElementById('staff-password').value
        };
        const url = id ? `/api/admin/users/${id}` : '/api/admin/users';
        const method = id ? 'PUT' : 'POST';
        try {
            const res = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify(payload)
            });
            if (res.ok) {
                alert('Staff berhasil disimpan.');
                batalFormStaff();
                loadStaff();
            } else {
                const err = await res.json();
                alert('Gagal: ' + (err.message || JSON.stringify(err)));
            }
        } catch (err) {
            alert('Error: ' + err.message);
        }
    }

    async function editStaff(id) {
        try {
            const res = await fetch(`/api/admin/users/${id}`, {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const u = await res.json();
            document.getElementById('staff-id').value = u.id;
            document.getElementById('staff-name').value = u.name;
            document.getElementById('staff-email').value = u.email;
            document.getElementById('staff-role').value = u.role;
            document.getElementById('staff-password').value = ''; // kosongkan
            document.getElementById('form-staff').style.display = 'block';
            document.getElementById('form-staff-title').innerText = 'Edit Staff';
        } catch (err) {
            alert('Gagal mengambil data staff.');
        }
    }

    async function hapusStaff(id) {
        confirmAction('Yakin hapus staff ini?', async () => {
            try {
                const res = await fetch(`/api/admin/users/${id}`, {
                    method: 'DELETE',
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                if (res.ok) {
                    alert('Staff dihapus.');
                    loadStaff();
                } else {
                    const err = await res.json();
                    alert('Gagal: ' + err.message);
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }
        });
    }
</script>
@endpush