{{-- ===== JADWAL TES ===== --}}
<div id="seleksi" class="section" style="display:none;">
    <div class="section-wrapper">
        <div class="form-header-card">
            <div class="school-brand">
                <img src="{{ asset('storage/assets/logo-mizi.png') }}" alt="Logo">
                <div class="brand-text">
                    <p>YAYASAN PENDIDIKAN ISLAM ZIYADATUL IHSAN</p>
                    <h2>MADRASAH IBTIDAIYAH</h2>
                </div>
            </div>
        </div>
        <div class="jadwal-tes-header">
            <h3>Jadwal Tes</h3>
            <p>Informasi jadwal seleksi calon siswa baru</p>
        </div>
        <div id="info-seleksi">...</div>
    </div>
</div>

@push('section-scripts')
<script>
    async function loadSeleksiSaya() {
        const container = document.getElementById('info-seleksi');
        try {
            const res = await fetch('/api/seleksi-saya', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const data = await res.json();

            let jadwalText = data?.jadwal_tes || 'Belum ada jadwal tes. Silakan cek secara berkala.';
            let dateTime = 'Belum ditentukan';
            let location = 'MI Ziyadatul Ihsan';

            if (jadwalText !== 'Belum ada jadwal tes. Silakan cek secara berkala.') {
                dateTime = jadwalText;
                location = 'MI Ziyadatul Ihsan, Jl. Sadar No.33';
            }

            const html = `
                <div class="jadwal-tes-grid">
                    <div class="jadwal-tes-card">
                        <div class="jadwal-tes-card-header">
                            <div class="tes-icon"><i class="fa-solid fa-book"></i></div>
                            <div class="tes-title">
                                <h4>Tes Akademik</h4>
                                <p>Pengetahuan umum & kemampuan dasar</p>
                            </div>
                        </div>
                        <div class="jadwal-tes-card-body">
                            <div class="jadwal-tes-detail-item">
                                <i class="fi fi-rr-calendar"></i>
                                <div class="detail-text">
                                    <label>Tanggal & Waktu</label>
                                    <strong>${escapeHtml(dateTime)}</strong>
                                </div>
                            </div>
                            <div class="jadwal-tes-detail-item">
                                <i class="fa-solid fa-location-dot"></i>
                                <div class="detail-text">
                                    <label>Lokasi</label>
                                    <strong>${escapeHtml(location)}</strong>
                                </div>
                            </div>
                            <div class="jadwal-tes-detail-item">
                                <i class="fa-regular fa-clock"></i>
                                <div class="detail-text">
                                    <label>Durasi</label>
                                    <strong>90 Menit</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="jadwal-tes-card">
                        <div class="jadwal-tes-card-header">
                            <div class="tes-icon"><i class="fa-solid fa-star"></i></div>
                            <div class="tes-title">
                                <h4>Tes Baca Al-Qur'an</h4>
                                <p>Tajwid, kelancaran, dan hafalan</p>
                            </div>
                        </div>
                        <div class="jadwal-tes-card-body">
                            <div class="jadwal-tes-detail-item">
                                <i class="fi fi-rr-calendar"></i>
                                <div class="detail-text">
                                    <label>Tanggal & Waktu</label>
                                    <strong>${escapeHtml(dateTime)}</strong>
                                </div>
                            </div>
                            <div class="jadwal-tes-detail-item">
                                <i class="fi fi-rr-marker"></i>
                                <div class="detail-text">
                                    <label>Lokasi</label>
                                    <strong>${escapeHtml(location)}</strong>
                                </div>
                            </div>
                            <div class="jadwal-tes-detail-item">
                                <i class="fi fi-rr-clock"></i>
                                <div class="detail-text">
                                    <label>Durasi</label>
                                    <strong>30 Menit</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.innerHTML = html;
        } catch (error) {
            console.error(error);
            container.innerHTML = '<div class="jadwal-card"><p style="color:#888;">Gagal memuat jadwal tes.</p></div>';
        }
    }
</script>
@endpush