{{-- ===== STATUS PENDAFTARAN ===== --}}
<div id="status" class="section" style="display:none;">
    <div class="section-wrapper">
        <!-- Header Sekolah (sama seperti desain) -->
        <div class="status-header">
            <div class="school-brand-centered">
                <img src="{{ asset('storage/assets/logo-mizi.png') }}" alt="Logo MI Ziyadatul Ihsan">
                <div class="brand-text">
                    <p>YAYASAN PENDIDIKAN ISLAM ZIYADATUL IHSAN</p>
                    <h2>MADRASAH IBTIDAIYAH</h2>
                </div>
            </div>
        </div>
        
        <div class="section-wrapper">
            <!-- Judul -->
            <div class="status-title">
                <h2>Status Pendaftaran</h2>
                <p>Rangkaian status pendaftaran yang sedang berjalan</p>
            </div>

            <!-- Daftar Step -->
            <div  id="steps-list">
                <!-- Akan diisi oleh JS dengan 6 step -->
                <div style="text-align:center; padding:40px;">Memuat data status...</div>
            </div>
        </div>
    </div>
</div>

@push('section-scripts')
<script>
    // Fungsi utama load status pendaftaran (dipanggil dari navigate)
    async function loadStatusPendaftaran() {
        const container = document.getElementById('steps-list');
        if (!container) return;

        // Tampilkan loading
        container.innerHTML = '<div style="text-align:center; padding:40px;">Memuat data status...</div>';

        try {
            // Ambil semua data yang diperlukan secara paralel
            const [formulirRes, buktiRes, seleksiRes, daftarUlangRes] = await Promise.all([
                fetch('/api/formulir-saya', { headers: { 'Authorization': 'Bearer ' + token } }),
                fetch('/api/bukti-pembayaran', { headers: { 'Authorization': 'Bearer ' + token } }),
                fetch('/api/seleksi-saya', { headers: { 'Authorization': 'Bearer ' + token } }),
                fetch('/api/daftar-ulang/cek', { headers: { 'Authorization': 'Bearer ' + token } })
            ]);

            const formulir = await formulirRes.json();
            const buktiData = await buktiRes.json();
            const seleksi = await seleksiRes.json();
            const daftarUlang = await daftarUlangRes.json();

            // Data pembayaran formulir
            const buktiFormulir = buktiData.find(b => b.jenis_pembayaran === 'formulir');
            let statusPembayaranFormulir = 'belum'; // belum, proses, selesai
            if (buktiFormulir) {
                if (buktiFormulir.status === 'diterima') statusPembayaranFormulir = 'selesai';
                else if (buktiFormulir.status === 'menunggu') statusPembayaranFormulir = 'proses';
                else statusPembayaranFormulir = 'belum';
            }

            // Status formulir pendaftaran
            let statusFormulir = 'belum';
            if (formulir.data) {
                if (formulir.data.status === 'diterima') statusFormulir = 'selesai';
                else if (formulir.data.status === 'menunggu') statusFormulir = 'proses';
                else statusFormulir = 'belum';
            }

            // Status jadwal tes
            let statusJadwal = 'belum';
            if (seleksi && seleksi.jadwal_tes && seleksi.jadwal_tes !== 'Belum ada jadwal tes. Silakan cek secara berkala.') {
                statusJadwal = 'selesai';
            } else if (formulir.data && formulir.data.status === 'diterima') {
                // Jika formulir sudah diterima tapi jadwal belum ada, bisa dianggap proses
                statusJadwal = 'proses';
            }

            // Status pengumuman (lihat hasil tes)
            let statusPengumuman = 'belum';
            if (seleksi && seleksi.penilaian && (seleksi.kelulusan_tes === 'lulus' || seleksi.kelulusan_tes === 'tidak_lulus')) {
                statusPengumuman = 'selesai';
            } else if (statusJadwal === 'selesai') {
                // Jika jadwal sudah selesai tapi pengumuman belum keluar, bisa proses
                statusPengumuman = 'proses';
            }

            // Status daftar ulang (isi formulir)
            let statusDaftarUlang = 'belum';
            if (daftarUlang.eligible) {
                if (daftarUlang.sudah_mengirim) {
                    if (daftarUlang.status === 'diterima') statusDaftarUlang = 'selesai';
                    else if (daftarUlang.status === 'menunggu') statusDaftarUlang = 'proses';
                    else statusDaftarUlang = 'belum';
                } else {
                    statusDaftarUlang = 'belum';
                }
            } else {
                // Jika tidak eligible (misal belum lulus tes), tetap belum
                statusDaftarUlang = 'belum';
            }

            // Status pembayaran daftar ulang
            const buktiDaftarUlang = buktiData.find(b => b.jenis_pembayaran === 'daftar_ulang');
            let statusPembayaranDaftarUlang = 'belum';
            if (buktiDaftarUlang) {
                if (buktiDaftarUlang.status === 'diterima') statusPembayaranDaftarUlang = 'selesai';
                else if (buktiDaftarUlang.status === 'menunggu') statusPembayaranDaftarUlang = 'proses';
                else statusPembayaranDaftarUlang = 'belum';
            }

            // Susunan steps sesuai Figma
            const steps = [
                { name: 'Pembayaran Formulir Pendaftaran', status: statusPembayaranFormulir },
                { name: 'Isi Formulir Pendaftaran', status: statusFormulir },
                { name: 'Lihat Jadwal Tes', status: statusJadwal },
                { name: 'Lihat Pengumuman Hasil Tes', status: statusPengumuman },
                { name: 'Isi Formulir Daftar Ulang', status: statusDaftarUlang },
                { name: 'Pembayaran Daftar Ulang', status: statusPembayaranDaftarUlang }
            ];

            // Render HTML
            let html = '';
            steps.forEach((step, index) => {
                let badgeClass = '';
                let badgeText = '';
                if (step.status === 'selesai') {
                    badgeClass = 'badge-selesai';
                    badgeText = 'SELESAI';
                } else if (step.status === 'proses') {
                    badgeClass = 'badge-proses';
                    badgeText = 'PROSES';
                } else {
                    badgeClass = 'badge-belum';
                    badgeText = 'BELUM';
                }

                html += `
                    <div class="step-item">
                        <div class="step-number">${index + 1}</div>
                        <div class="step-name">${step.name}</div>
                        <div class="step-badge ${badgeClass}">${badgeText}</div>
                    </div>
                `;
            });
            container.innerHTML = html;
        } catch (error) {
            console.error(error);
            container.innerHTML = '<p style="color:#888; text-align:center;">Gagal memuat status pendaftaran.</p>';
        }
    }
</script>
@endpush

{{-- CSS khusus untuk halaman status --}}
<style>
/* Status Container - sesuai Figma */
#status .status-container {
    max-width: 932px;
    margin: 0 auto;
    background: #FAFAFA;
    border-radius: 5px;
    box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.25);
    padding: 30px 40px 40px 40px;
}

/* Header sekolah */
#status .school-brand-centered {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e0e0e0;
}

#status .school-brand-centered img {
    width: 80px;
}

#status .brand-text p {
    font-size: 14px;
    font-weight: 700;
    color: #000;
    margin: 0;
}

#status .brand-text h2 {
    font-size: 28px;
    font-weight: 700;
    color: #000;
    margin: 0;
}

/* Judul */
#status .status-title {
    margin-bottom: 30px;
}

#status .status-title h2 {
    font-size: 20px;
    color: #006837;
    font-weight: 700;
    margin-bottom: 8px;
}

#status .status-title p {
    font-size: 20px;
    color: #4D4D4D;
    font-weight: 400;
    margin: 0;
}

/* Daftar step */
#status .steps-list {
    background: #FBFBFB;
    border: 1px solid #B5B5B5;
    border-radius: 15px;
    box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
    overflow: hidden;
}

#status .step-item {
    display: flex;
    align-items: center;
    padding: 18px 30px;
    margin-bottom: 12px;          /* jarak antar item */
    background: #FBFBFB;
    border: 1px solid #8C8C8C;    /* border setiap item */
    border-radius: 10px;          /* sudut membulat */
}

#status .step-item:last-child {
    margin-bottom: 0;
}

#status .step-number {
    width: 40px;
    height: 40px;
    background: #006837;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Pontano Sans', sans-serif;
    font-weight: 700;
    font-size: 20px;
    color: white;
    margin-right: 25px;
    flex-shrink: 0;
}

#status .step-name {
    flex: 1;
    font-family: 'Inter', sans-serif;
    font-weight: 700;
    font-size: 15px;
    color: #3F7659;
    text-align: left;
}

#status .step-badge {
    padding: 6px 20px;
    border-radius: 20px;
    font-family: 'Inter', sans-serif;
    font-weight: 700;
    font-size: 12px;
    text-align: center;
    min-width: 100px;
}

#status .badge-selesai {
    background: rgba(0, 104, 55, 0.2);
    color: #006837;
}

#status .badge-proses {
    background: rgba(255, 199, 88, 0.4);
    color: #A87000;
}

#status .badge-belum {
    background: rgba(98, 98, 98, 0.2);
    color: #626262;
}

/* Responsive */
@media (max-width: 768px) {
    #status .status-container {
        padding: 20px;
    }
    #status .step-item {
        flex-wrap: wrap;
        padding: 15px;
    }
    #status .step-number {
        margin-bottom: 10px;
    }
    #status .step-name {
        width: 100%;
        margin-bottom: 10px;
        text-align: left;
    }
    #status .step-badge {
        margin-left: 0;
    }
}
</style>