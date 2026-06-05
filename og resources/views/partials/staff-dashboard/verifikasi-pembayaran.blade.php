{{-- ===== VERIFIKASI PEMBAYARAN (Staff) ===== --}}
<div id="verifikasi-pembayaran" class="section" style="display:none;">
    <h2>Verifikasi Bukti Pembayaran</h2>

    <!-- Tombol switch / tab -->
    <div>
        <button id="tabFormulir" class="tab-active" onclick="switchJenisPembayaran('formulir')">Pembayaran Formulir</button>
        <button id="tabMasuk" onclick="switchJenisPembayaran('masuk')">Pembayaran Masuk (Daftar Ulang)</button>
    </div>
    <br>

    <!-- Container untuk tabel formulir -->
    <div id="container-formulir">
        <h3>Menunggu Verifikasi</h3>
        <table border="1" width="100%" id="tabel-formulir-menunggu">
            <thead>
                <tr><th>No</th><th>Pendaftar</th><th>Jenis</th><th>Bukti</th><th>Status</th></tr>
            </thead>
            <tbody></tbody>
        </table>
        <br>
        <h3>Sudah Diverifikasi</h3>
        <table border="1" width="100%" id="tabel-formulir-sudah">
            <thead>
                <tr><th>No</th><th>Pendaftar</th><th>Jenis</th><th>Bukti</th><th>Status</th><th>Catatan / Kwitansi</th></tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Container untuk tabel masuk (daftar ulang) -->
    <div id="container-masuk" style="display:none;">
        <h3>Menunggu Verifikasi</h3>
        <table border="1" width="100%" id="tabel-masuk-menunggu">
            <thead>
                <tr><th>No</th><th>Pendaftar</th><th>Jenis</th><th>Bukti</th><th>Status</th></tr>
            </thead>
            <tbody></tbody>
        </table>
        <br>
        <h3>Sudah Diverifikasi</h3>
        <table border="1" width="100%" id="tabel-masuk-sudah">
            <thead>
                <tr><th>No</th><th>Pendaftar</th><th>Jenis</th><th>Bukti</th><th>Status</th><th>Catatan / Kwitansi</th></tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

@push('staff-scripts')
<script>
    // Variabel global untuk menyimpan data bukti yang sedang diverifikasi
    let currentBukti = null;
    let currentJenis = 'formulir'; // formulir atau masuk

    // Switch tab
    window.switchJenisPembayaran = function(jenis) {
        currentJenis = jenis;
        if (jenis === 'formulir') {
            document.getElementById('container-formulir').style.display = 'block';
            document.getElementById('container-masuk').style.display = 'none';
            document.getElementById('tabFormulir').classList.add('tab-active');
            document.getElementById('tabMasuk').classList.remove('tab-active');
            loadDataVerifikasiPembayaran('formulir');
        } else {
            document.getElementById('container-formulir').style.display = 'none';
            document.getElementById('container-masuk').style.display = 'block';
            document.getElementById('tabMasuk').classList.add('tab-active');
            document.getElementById('tabFormulir').classList.remove('tab-active');
            loadDataVerifikasiPembayaran('masuk');
        }
    };

    // Load data dari API dan render tabel
    async function loadDataVerifikasiPembayaran(jenis) {
        try {
            const res = await fetch('/api/bukti-pembayaran/semua', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const allData = await res.json();
            // Filter berdasarkan jenis_pembayaran
            const filtered = allData.filter(b => b.jenis_pembayaran === jenis);

            // Pisahkan menunggu dan sudah diverifikasi
            const menunggu = filtered.filter(b => b.status === 'menunggu');
            const sudah = filtered.filter(b => b.status !== 'menunggu');

            if (jenis === 'formulir') {
                renderTabel(menunggu, 'tabel-formulir-menunggu', false);
                renderTabelSudah(sudah, 'tabel-formulir-sudah');
            } else {
                renderTabel(menunggu, 'tabel-masuk-menunggu', false);
                renderTabelSudah(sudah, 'tabel-masuk-sudah');
            }
        } catch (err) {
            console.error(err);
        }
    }

    // Render tabel untuk status menunggu
    function renderTabel(data, tableId, withAction = false) {
        const tbody = document.querySelector(`#${tableId} tbody`);
        if (!tbody) return;
        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5">Tidak ada data.</td></tr>';
            return;
        }
        let html = '';
        data.forEach((b, i) => {
            html += `<tr>
                <td>${i + 1}</td>
                <td>${b.pendaftar?.name ?? '-'}</td>
                <td>${b.jenis_pembayaran === 'formulir' ? 'Formulir' : 'Daftar Ulang'}</td>
                <td><button onclick="bukaModalVerifikasi(${b.id_bukti_pembayaran})">Lihat Bukti</button></td>
                <td>${b.status}</td>
            </tr>`;
        });
        tbody.innerHTML = html;
    }

    // Render tabel untuk status sudah diverifikasi
    function renderTabelSudah(data, tableId) {
        const tbody = document.querySelector(`#${tableId} tbody`);
        if (!tbody) return;
        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6">Tidak ada data.</td></tr>';
            return;
        }
        let html = '';
        data.forEach((b, i) => {
            let info = '';
            if (b.status === 'diterima') {
                const kwitansiId = b.verifikasi?.kwitansi?.id_kwitansi;
                if (kwitansiId) {
                    info = `<a href="javascript:void(0)" onclick="lihatKwitansi(${kwitansiId})">Lihat Kwitansi</a>`;
                } else {
                    info = '-';
                }
            }
            html += `<tr>
                <td>${i + 1}</td>
                <td>${b.pendaftar?.name ?? '-'}</td>
                <td>${b.jenis_pembayaran === 'formulir' ? 'Formulir' : 'Daftar Ulang'}</td>
                <td><a href="javascript:void(0)" onclick="lihatBukti(${b.id_bukti_pembayaran})">Lihat Bukti</a></td>
                <td>${b.status}</td>
                <td>${info}</td>
            </tr>`;
        });
        tbody.innerHTML = html;
    }

    // Buka modal verifikasi
    window.bukaModalVerifikasi = async function(idBukti) {
        currentBukti = { id: idBukti };
        try {
            const res = await fetch(`/api/file/bukti/${idBukti}`, {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            if (!res.ok) {
                alert('Gagal memuat gambar bukti pembayaran.');
                return;
            }
            const blob = await res.blob();
            const imageUrl = URL.createObjectURL(blob);
            document.getElementById('modalGambar').src = imageUrl;
        } catch (e) {
            console.error(e);
            alert('Gagal memuat gambar.');
            return;
        }
        document.getElementById('modalHasil').value = 'diterima';
        document.getElementById('modalCatatanGroup').style.display = 'none';
        document.getElementById('modalKwitansiGroup').style.display = 'block';
        document.getElementById('modalCatatan').value = '';
        document.getElementById('modalKwitansi').value = '';
        document.getElementById('modalVerifikasi').style.display = 'block';
        document.getElementById('overlay').style.display = 'block';
    };

    // Event listener untuk change select hasil (dipasang setelah modal ada)
    document.getElementById('modalHasil')?.addEventListener('change', function () {
        const isTolak = this.value === 'ditolak';
        document.getElementById('modalCatatanGroup').style.display = isTolak ? 'block' : 'none';
        document.getElementById('modalKwitansiGroup').style.display = isTolak ? 'none' : 'block';
    });

    // Submit verifikasi
    window.submitVerifikasi = async function() {
        if (!currentBukti) return;
        const hasil = document.getElementById('modalHasil').value;
        const catatan = document.getElementById('modalCatatan').value;
        const fileInput = document.getElementById('modalKwitansi');

        const formData = new FormData();
        formData.append('id_bukti_pembayaran', currentBukti.id);
        formData.append('hasil_verifikasi', hasil);
        if (catatan) formData.append('catatan', catatan);
        if (hasil === 'diterima' && fileInput.files.length > 0) {
            formData.append('kwitansi', fileInput.files[0]);
        } else if (hasil === 'diterima' && fileInput.files.length === 0) {
            alert('Kwitansi wajib diupload jika menerima pembayaran.');
            return;
        }

        try {
            const res = await fetch('/api/verifikasi-pembayaran', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token },
                body: formData
            });
            if (res.ok) {
                alert('Verifikasi berhasil.');
                tutupModal();
                loadDataVerifikasiPembayaran(currentJenis);
            } else {
                const err = await res.json();
                alert('Gagal: ' + (err.message || JSON.stringify(err)));
            }
        } catch (e) {
            alert('Error: ' + e.message);
        }
    };

    // Secure view functions
    async function lihatBukti(id) {
        try {
            const res = await fetch(`/api/file/bukti/${id}`, {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            if (!res.ok) {
                alert('Gagal mengambil bukti pembayaran');
                return;
            }
            const blob = await res.blob();
            const url = URL.createObjectURL(blob);
            window.open(url, '_blank');
        } catch (e) {
            console.error(e);
            alert('Gagal membuka bukti pembayaran');
        }
    }

    async function lihatKwitansi(id) {
        try {
            const res = await fetch(`/api/file/kwitansi/${id}`, {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            if (!res.ok) {
                alert('Gagal mengambil kwitansi');
                return;
            }
            const blob = await res.blob();
            const url = URL.createObjectURL(blob);
            window.open(url, '_blank');
        } catch (e) {
            console.error(e);
            alert('Gagal membuka kwitansi');
        }
    }

    window.lihatBukti = lihatBukti;
    window.lihatKwitansi = lihatKwitansi;

    window.bukaGambarFull = function() {
        if (currentBukti) {
            lihatBukti(currentBukti.id);
        }
    };

    window.tutupModal = function() {
        document.getElementById('modalVerifikasi').style.display = 'none';
        document.getElementById('overlay').style.display = 'none';
        currentBukti = null;
    };
</script>
@endpush