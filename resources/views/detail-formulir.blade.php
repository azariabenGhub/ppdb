<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Formulir #{{ $id }}</title>
</head>
<body>
    <h1>Detail Formulir #{{ $id }}</h1>
    <div id="detail">Memuat...</div>

    <script>
        // Ambil token & user
        const token = localStorage.getItem('access_token');
        const user = JSON.parse(localStorage.getItem('user') || 'null');

        // Proteksi: hanya panitia & kepsek yang boleh akses
        if (!token || !user || (user.role !== 'panitia' && user.role !== 'kepala_sekolah')) {
            alert('Anda tidak memiliki akses.');
            window.location.href = '/login';
        }

        const id = {{ $id }}; // Diisi dari route Laravel

        async function load() {
            const detailContainer = document.getElementById('detail');
            try {
                const res = await fetch(`/api/pendaftaran/${id}`, {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });

                if (!res.ok) {
                    detailContainer.innerHTML = '<p>Gagal memuat data.</p>';
                    return;
                }

                const result = await res.json();
                const d = result.data; // <-- konsisten pakai "d"

                let html = `
                    <h4>Biodata Siswa</h4>
                    <p>Nama: ${d.nama_lengkap}</p>
                    <p>Tempat, Tanggal Lahir: ${d.tempat_lahir}, ${d.tanggal_lahir}</p>
                    <p>NIK: ${d.nik}</p>
                    <p>Agama: ${d.agama}</p>
                    <p>Warga Negara: ${d.warga_negara}</p>
                    <p>Anak ke-: ${d.anak_ke || '-'}, Jumlah Saudara: ${d.jumlah_saudara || '-'}</p>
                    <p>Alamat: ${d.alamat_lengkap}</p>

                    <h4>Data ${d.tipe_wali === 'orang_tua' ? 'Orang Tua' : 'Wali'}</h4>
                    ${d.tipe_wali === 'orang_tua' ? `
                        <p><strong>Ayah:</strong> ${d.nama_ayah} (${d.pekerjaan_ayah}) - Agama: ${d.agama_ayah}, Pendidikan: ${d.pendidikan_ayah}</p>
                        <p>No KTP: ${d.no_ktp_ayah}, Penghasilan: ${d.penghasilan_ayah}, Telp: ${d.no_telp_ayah}</p>
                        <p>Alamat Ayah: ${d.alamat_ayah}</p>
                        <hr>
                        <p><strong>Ibu:</strong> ${d.nama_ibu} (${d.pekerjaan_ibu}) - Agama: ${d.agama_ibu}, Pendidikan: ${d.pendidikan_ibu}</p>
                        <p>No KTP: ${d.no_ktp_ibu}, Penghasilan: ${d.penghasilan_ibu}, Telp: ${d.no_telp_ibu}</p>
                        <p>Alamat Ibu: ${d.alamat_ibu}</p>
                    ` : `
                        <p><strong>Wali:</strong> ${d.nama_wali} (${d.pekerjaan_wali}) - Agama: ${d.agama_wali}, Pendidikan: ${d.pendidikan_wali}</p>
                        <p>No KTP: ${d.no_ktp_wali}, Penghasilan: ${d.penghasilan_wali}, Telp: ${d.no_telp_wali}</p>
                        <p>Alamat Wali: ${d.alamat_wali}</p>
                    `}

                    <h4>Akademik</h4>
                    ${d.is_bukan_pindahan ? '<p>Bukan murid pindahan</p>' : `
                        <p>Asal Sekolah: ${d.asal_sekolah}</p>
                        <p>No Ijazah: ${d.no_ijazah} (${d.tahun_ijazah})</p>
                        <p>Diterima di Kelas: ${d.diterima_kelas}</p>
                        <p>Pindah dari: ${d.pindah_dari}, No Pindah: ${d.no_pindah}, Tgl Pindah: ${d.tanggal_pindah}</p>
                    `}
                    <p>Status: ${d.status}</p>
                `;

                // Tombol verifikasi hanya jika status menunggu
                if (d.status === 'menunggu') {
                    html += `
                        <button id="btn-terima">Terima</button>
                        <button id="btn-tolak">Tolak</button>
                    `;
                } else if (d.status === 'ditolak') {
                    html += `<p><b>Catatan Penolakan:</b> ${d.verifikasi?.catatan ?? '-'}</p>`;
                }

                detailContainer.innerHTML = html;

                // Pasang event listener jika tombol ada
                if (d.status === 'menunggu') {
                    document.getElementById('btn-terima').addEventListener('click', () => verifikasi('diterima'));
                    document.getElementById('btn-tolak').addEventListener('click', () => {
                        const catatan = prompt('Masukkan catatan penolakan:');
                        if (catatan !== null) verifikasi('ditolak', catatan);
                    });
                }
            } catch (error) {
                console.error(error);
                detailContainer.innerHTML = '<p>Terjadi kesalahan jaringan.</p>';
            }
        }

        async function verifikasi(hasil, catatan = '') {
            if (!catatan && hasil === 'ditolak') {
                alert('Catatan wajib diisi untuk penolakan.');
                return;
            }
            try {
                const res = await fetch('/api/verifikasi', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({
                        id_formulir: id,
                        hasil_verifikasi: hasil,
                        catatan: catatan
                    })
                });
                const r = await res.json();
                if (res.ok) {
                    alert(r.message || 'Verifikasi berhasil.');
                    location.reload();
                } else {
                    alert('Gagal: ' + (r.message || 'Terjadi kesalahan.'));
                }
            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan jaringan.');
            }
        }

        // Panggil load saat halaman siap
        load();
    </script>
</body>
</html>