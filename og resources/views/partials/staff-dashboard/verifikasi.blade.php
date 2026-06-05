<div id="verifikasi" class="section" style="display:none;">
    <h2>Verifikasi Formulir</h2>
    <h3>Menunggu Verifikasi</h3>
    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <thead><tr><th>No</th><th>Nama</th><th>Nama Siswa</th><th>Aksi</th></tr></thead>
        <tbody id="tabel-menunggu"></tbody>
    </table>
    <br>
    <h3>Sudah Diverifikasi</h3>
    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <thead><tr><th>No</th><th>Nama</th><th>Nama Siswa</th><th>Hasil</th><th>Detail</th></tr></thead>
        <tbody id="tabel-sudah"></tbody>
    </table>
</div>

@push('staff-scripts')
<script>
    async function loadVerifikasi() {
        const res = await fetch('/api/pendaftaran', { headers: { 'Authorization': 'Bearer ' + token } });
        const data = await res.json();
        let menungguHtml = '', sudahHtml = '';
        data.forEach((item, i) => {
            const baris = `<tr><td>${i+1}</td><td>${item.nama_pendaftar}</td><td>${item.nama_lengkap}</td><td><a href="/formulir/${item.id}" target="_blank">Lihat Detail</a></td></tr>`;
            if (item.status === 'menunggu') menungguHtml += baris;
            else sudahHtml += `<tr><td>${i+1}</td><td>${item.nama_pendaftar}</td><td>${item.nama_lengkap}</td><td>${item.status}</td><td><a href="/formulir/${item.id}" target="_blank">Detail</a></td></tr>`;
        });
        document.getElementById('tabel-menunggu').innerHTML = menungguHtml || '<tr><td colspan="4">Tidak ada</td></tr>';
        document.getElementById('tabel-sudah').innerHTML = sudahHtml || '<tr><td colspan="5">Tidak ada</td></tr>';
    }
</script>
@endpush