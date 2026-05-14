{{-- ===== PENGUMUMAN ===== --}}
<div id="pengumuman" class="section" style="display:none;">
    <div class="section-wrapper">
        <h2>Pengumuman Hasil Seleksi</h2>
        <div id="info-pengumuman">
            <p style="color:#888; font-size:0.9rem;">Memuat data pengumuman...</p>
        </div>
    </div>
</div>

@push('section-scripts')
<script>
    async function loadPengumuman() {
        const container = document.getElementById('info-pengumuman');
        try {
            const res = await fetch('/api/seleksi-saya', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const data = await res.json();

            if (!data || !data.penilaian) {
                container.innerHTML = '<p>Belum ada pengumuman kelulusan tes.</p>';
                return;
            }

            const p = data.penilaian;
            const kelulusan = data.kelulusan_tes || 'belum ditentukan';
            const isLulus = kelulusan === 'lulus';
            const isTidakLulus = kelulusan === 'tidak_lulus';
            let statusClass = '';
            if (isLulus) statusClass = 'pengumuman-lulus';
            else if (isTidakLulus) statusClass = 'pengumuman-tidak-lulus';

            let html = `
                <div class="${statusClass}">
                    <h3>Hasil Tes Seleksi</h3>
                    <p><strong>Kelulusan:</strong> ${isLulus ? '✅ LULUS' : (isTidakLulus ? '❌ TIDAK LULUS' : '-')}</p>
                </div>
                <h4 style="margin-top:20px;">Nilai Tes:</h4>
                <div class="nilai-grid">
                    <div class="nilai-item"><label>Kemampuan Membaca</label><span>${p.kemampuan_membaca || '-'}</span></div>
                    <div class="nilai-item"><label>Kemampuan Menulis</label><span>${p.kemampuan_menulis || '-'}</span></div>
                    <div class="nilai-item"><label>Kemampuan Berhitung</label><span>${p.kemampuan_berhitung || '-'}</span></div>
                    <div class="nilai-item"><label>Baca Alquran</label><span>${p.baca_alquran || '-'}</span></div>
                </div>
            `;
            container.innerHTML = html;
        } catch (error) {
            console.error(error);
            container.innerHTML = '<p>Gagal memuat data pengumuman.</p>';
        }
    }
</script>
@endpush