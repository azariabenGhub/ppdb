{{-- ===== STATUS PENDAFTARAN ===== --}}
<div id="status" class="section" style="display:none;">
    <div class="section-wrapper">
        <h2>Status Pendaftaran</h2>
        <p style="color:#888; font-size:0.85rem; margin-bottom:20px;">Berikut adalah status pendaftaran Anda saat ini.</p>
        <div id="status-content">
            <p style="color:#888; font-size:0.9rem;">Memuat data status...</p>
        </div>
    </div>
</div>

@push('section-scripts')
<script>
    async function loadStatusPendaftaran() {
        const container = document.getElementById('status-content');
        try {
            const res = await fetch('/api/formulir-saya', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const result = await res.json();
            const data = result.data;

            if (!data) {
                container.innerHTML = '<p>Anda belum mengisi formulir pendaftaran.</p>';
                return;
            }

            const steps = [
                { label: 'Formulir Pendaftaran', status: data.status === 'diterima' ? 'done' : (data.status ===
                        'menunggu' ? 'current' : 'pending') },
                { label: 'Pembayaran Formulir', status: 'pending' },
                { label: 'Jadwal Tes', status: 'pending' },
                { label: 'Pengumuman', status: 'pending' },
                { label: 'Daftar Ulang', status: 'pending' }
            ];

            // Cek status pembayaran formulir
            if (data.status === 'diterima') {
                try {
                    const buktiRes = await fetch('/api/bukti-pembayaran', {
                        headers: { 'Authorization': 'Bearer ' + token }
                    });
                    const buktiData = await buktiRes.json();
                    const buktiFormulir = buktiData.find(b => b.jenis_pembayaran === 'formulir');
                    if (buktiFormulir) {
                        steps[1].status = buktiFormulir.status === 'diterima' ? 'done' : 'current';
                    }
                } catch (e) {}
            }

            // Cek jadwal tes
            try {
                const seleksiRes = await fetch('/api/seleksi-saya', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const seleksiData = await seleksiRes.json();
                if (seleksiData && seleksiData.jadwal_tes) {
                    steps[2].status = 'done';
                    if (seleksiData.penilaian) {
                        steps[3].status = 'done';
                    }
                }
            } catch (e) {}

            let html = '<div class="status-steps">';
            steps.forEach((step, index) => {
                html += `
                    <div class="status-step">
                        <div class="status-dot ${step.status}">${index + 1}</div>
                        <div class="status-step-info">
                            <h4>${step.label}</h4>
                            <p>${step.status === 'done' ? 'Selesai' : (step.status === 'current' ? 'Sedang diproses' : 'Menunggu')}</p>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            container.innerHTML = html;
        } catch (error) {
            console.error(error);
            container.innerHTML = '<p>Gagal memuat status pendaftaran.</p>';
        }
    }
</script>
@endpush