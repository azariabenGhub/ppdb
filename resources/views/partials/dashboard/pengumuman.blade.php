{{-- ===== PENGUMUMAN ===== --}}
<div id="pengumuman" class="section" style="display:none;">
    <div class="section-wrapper">
        {{-- Header card (logo & nama sekolah) --}}
        <div class="form-header-card">
            <div class="school-brand-centered">
                <img src="{{ asset('storage/assets/logo-mizi.png') }}" alt="Logo">
                <div class="brand-text">
                    <p>YAYASAN PENDIDIKAN ISLAM ZIYADATUL IHSAN</p>
                    <h2>MADRASAH IBTIDAIYAH</h2>
                </div>
            </div>
        </div>
    
        <div class="form-title-centered">
            <h3>Pengumuman Hasil Seleksi</h3>
        </div>
       
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

            // Jika data tidak lengkap atau kelulusan belum ditentukan
            if (!data || !data.penilaian || !data.kelulusan_tes || (data.kelulusan_tes !== 'lulus' && data.kelulusan_tes !== 'tidak_lulus')) {
                container.innerHTML = `
                    <div class="empty-state-box">
                        <div class="message-content">
                            <div class="icon-text-row">
                                <i class="fa-regular fa-clock"></i>
                                <strong>Hasil belum tersedia, silakan cek kembali nanti.</strong>
                            </div>
                            <hr class="message-divider">
                            <p class="sub-message">Pengumuman akan diperbarui. Pastikan anda terus memantau halaman ini secara berkala.</p>
                        </div>
                    </div>
                `;
                return;
            }

            const p = data.penilaian;
            const kelulusan = data.kelulusan_tes;
            const isLulus = kelulusan === 'lulus';

            if (isLulus) {
                // Hitung total nilai (jumlah keempat nilai)
                const nilaiMembaca = parseInt(p.kemampuan_membaca) || 0;
                const nilaiMenulis = parseInt(p.kemampuan_menulis) || 0;
                const nilaiBerhitung = parseInt(p.kemampuan_berhitung) || 0;
                const nilaiBacaAlquran = parseInt(p.baca_alquran) || 0;
                const totalNilai = nilaiMembaca + nilaiMenulis + nilaiBerhitung + nilaiBacaAlquran;

                let html = `
                    <div class="form-card" style="padding:0; background:transparent; box-shadow:none;">
                        <div class="status-banner-success">
                            <p>Selamat! Anda dinyatakan lulus seleksi PPDB MI Ziyadatul Ihsan 2025/2026</p>
                        </div>

                        <div class="score-container">
                            <div class="table-header">
                                <h4>DETAIL NILAI</h4>
                            </div>
                            <table class="score-table">
                                <tr>
                                    <td class="label-cell">Kemampuan Membaca</td>
                                    <td class="value-cell">${nilaiMembaca}</td>
                                </tr>
                                <tr>
                                    <td class="label-cell">Kemampuan Menulis</td>
                                    <td class="value-cell">${nilaiMenulis}</td>
                                </tr>
                                <tr>
                                    <td class="label-cell">Kemampuan Berhitung</td>
                                    <td class="value-cell">${nilaiBerhitung}</td>
                                </tr>
                                <tr>
                                    <td class="label-cell">Baca Alquran</td>
                                    <td class="value-cell">${nilaiBacaAlquran}</td>
                                </tr>
                                <tr class="total-row">
                                    <td class="label-cell">Total Nilai</td>
                                    <td class="value-cell">${totalNilai}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="alert-info blue-alert">
                            <p>Harap segera lakukan daftar ulang</p>
                        </div>
                    </div>
                `;
                container.innerHTML = html;
            } else {
                // Tampilan untuk tidak lulus (tetap pakai gaya sebelumnya)
                let html = `
                    <div class="pengumuman-tidak-lulus">
                        <h3>Hasil Tes Seleksi</h3>
                        <p><strong>Kelulusan:</strong> ❌ TIDAK LULUS</p>
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
            }
        } catch (error) {
            console.error(error);
            container.innerHTML = '<p>Gagal memuat data pengumuman.</p>';
        }
    }
</script>
@endpush