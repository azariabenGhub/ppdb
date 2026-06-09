{{-- ===== STATUS PENDAFTARAN ===== --}}
<div id="status" class="section" style="display:none;">
    <div class="content-wrapper animate-fade-in">
        
        <div class="school-brand-header">
            <img src="{{ asset('storage/assets/logo-mizi.png') }}" alt="Logo Yayasan">
            <div class="brand-text">
                <p>YAYASAN PENDIDIKAN ISLAM ZIYADATUL IHSAN</p>
                <h2>MADRASAH IBTIDAIYAH</h2>
            </div>
        </div>

        <div class="form-card animate-slide-up mt-20">
            <div class="form-title" style="margin-bottom: 25px;">
                <h3 class="title-green">Status Pendaftaran</h3>
                <p>Rangkaian status pendaftaran yang sedang berjalan</p>
            </div>

            <div id="status-content">
                <p style="color:#888; font-size:0.9rem;">Memuat data status...</p>
            </div>
        </div>

    </div>
</div>

@push('section-scripts')
<script>
    async function loadStatusPendaftaran() {
        const container = document.getElementById('status-content');
        try {
            // 1. Fetch Data Formulir
            const res = await fetch('/api/formulir-saya', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const result = await res.json();
            const data = result.data || null;

            // Template 6 Langkah sesuai Figma
            const steps = [
                { label: 'Pembayaran Formulir Pendaftaran', status: 'pending' },
                { label: 'Isi Formulir Pendaftaran', status: 'pending' },
                { label: 'Lihat Jadwal Tes', status: 'pending' },
                { label: 'Lihat Pengumuman Hasil Tes', status: 'pending' },
                { label: 'Isi Formulir Daftar Ulang', status: 'pending' },
                { label: 'Pembayaran Daftar Ulang', status: 'pending' }
            ];

            // LOGIC PENENTUAN STATUS DARI BACKEND BEN
            // Cek Status Pembayaran (Langkah 1 & 6)
            try {
                const buktiRes = await fetch('/api/bukti-pembayaran', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const buktiData = await buktiRes.json();
                
                // Pembayaran Formulir (Langkah 1)
                const buktiFormulir = buktiData.find(b => b.jenis_pembayaran === 'formulir');
                if (buktiFormulir) {
                    steps[0].status = (buktiFormulir.status === 'terverifikasi' || buktiFormulir.status === 'diterima') ? 'done' : 'current';
                }

                // Pembayaran Daftar Ulang (Langkah 6)
                const buktiDaftarUlang = buktiData.find(b => b.jenis_pembayaran === 'daftar_ulang');
                if (buktiDaftarUlang) {
                    steps[5].status = (buktiDaftarUlang.status === 'terverifikasi' || buktiDaftarUlang.status === 'diterima') ? 'done' : 'current';
                }
            } catch (e) {}

            // Cek Status Isi Formulir (Langkah 2)
            if (data) {
                steps[1].status = data.status === 'diterima' ? 'done' : (data.status === 'menunggu' ? 'current' : 'done');
            } else {
                // Jika belum isi formulir sama sekali tapi sudah bayar
                if(steps[0].status === 'done') steps[1].status = 'current';
            }

            // Cek Jadwal Tes & Pengumuman (Langkah 3 & 4)
            try {
                const seleksiRes = await fetch('/api/seleksi-saya', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const seleksiData = await seleksiRes.json();
                
                if (seleksiData && seleksiData.jadwal_tes) {
                    steps[2].status = 'done'; // Jadwal sudah ada
                    steps[3].status = 'current'; // Menunggu hasil
                    
                    if (seleksiData.penilaian) {
                        steps[3].status = 'done'; // Hasil sudah keluar
                        steps[4].status = 'current'; // Lanjut isi formulir daftar ulang
                    }
                }
            } catch (e) {}

            // Cek Status Daftar Ulang (Langkah 5)
            try {
                const duRes = await fetch('/api/daftar-ulang/cek', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const duData = await duRes.json();
                if (duData && duData.sudah_mengirim) {
                    if (duData.status === 'diterima') {
                        steps[4].status = 'done';
                        if (steps[5].status !== 'done') {
                            steps[5].status = 'current';
                        }
                    } else if (duData.status === 'menunggu') {
                        steps[4].status = 'done'; // Sudah mengisi & mengirim
                    }
                }
            } catch (e) {}

            // RENDER HTML KE DALAM CONTAINER
            let html = '<div class="status-steps-container">';
            steps.forEach((step, index) => {
                // Konversi status ke badge dan warna sesuai desain Figma
                let badgeClass = '';
                let badgeText = '';
                let textClass = '';

                if (step.status === 'done') {
                    badgeClass = 'badge-selesai';
                    badgeText = 'SELESAI';
                    textClass = 'text-green';
                } else if (step.status === 'current') {
                    badgeClass = 'badge-proses';
                    badgeText = 'PROSES';
                    textClass = 'text-green';
                } else {
                    badgeClass = 'badge-belum';
                    badgeText = 'BELUM';
                    textClass = 'text-grey';
                }

                html += `
                    <div class="status-item">
                        <div class="status-left">
                            <div class="status-number ${step.status === 'done' || step.status === 'current' ? 'num-green' : 'num-grey'}">${index + 1}</div>
                            <span class="status-text ${textClass}">${step.label}</span>
                        </div>
                        <div class="status-right">
                            <span class="status-badge ${badgeClass}">${badgeText}</span>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            container.innerHTML = html;

        } catch (error) {
            console.error(error);
            container.innerHTML = '<p style="color:red;">Gagal memuat status pendaftaran. Pastikan Anda sudah login.</p>';
        }
    }
</script>
@endpush