{{-- ===== JADWAL TES ===== --}}
<div id="seleksi" class="section" style="display:none;">
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

        {{-- Konten dinamis jadwal --}}
        <div id="info-seleksi">
            <p style="color:#888; font-size:0.9rem;">Memuat data jadwal tes...</p>
        </div>
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

            // Jika tidak ada jadwal tes
            if (!data || !data.jadwal_tes || data.jadwal_tes === 'Belum ada jadwal tes. Silakan cek secara berkala.') {
                container.innerHTML = `
                    <div class="form-card animate-slide-up">
                        <div class="form-title-centered">
                            <h3>Jadwal Tes</h3>
                        </div>
                        <div class="empty-state-box">
                            <div class="message-content">
                                <div class="icon-text-row">
                                    <i class="fa-regular fa-clock"></i>
                                    <strong>Jadwal tes belum tersedia, silakan cek kembali nanti.</strong>
                                </div>
                                <hr class="message-divider">
                                <p class="sub-message">
                                    Jadwal akan diperbarui. Pastikan anda terus memantau halaman ini secara berkala.
                                </p>
                            </div>
                        </div>
                    </div>
                `;
                return;
            }

            // Jika ada jadwal, tampilkan card tes (seperti sebelumnya)
            let jadwalText = data.jadwal_tes;
            let dateTime = jadwalText;
            let location = 'MI Ziyadatul Ihsan, Jl. Sadar No.33';

            container.innerHTML = `
                <div class="jadwal-tes-header">
                    <h3>Jadwal Tes</h3>
                    <p>Informasi jadwal seleksi calon siswa baru</p>
                </div>
                <div class="jadwal-tes-grid">
                    <div class="jadwal-tes-card">
                        <div class="jadwal-tes-card-header">
                            <div class="tes-icon"><i class="fa-solid fa-layer-group"></i></div>
                            <div class="tes-title">
                                <h4>Tes Akademik</h4>
                                <p>Pengetahuan umum & kemampuan dasar</p>
                            </div>
                        </div>
                        <div class="jadwal-tes-card-body">
                            <div class="jadwal-tes-detail-item">
                                <i class="fa-solid fa-calendar-days"></i>
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
                            <div class="tes-icon"><i class="fa-solid fa-square-poll-horizontal"></i></div>
                            <div class="tes-title">
                                <h4>Tes Baca Al-Qur'an</h4>
                                <p>Tajwid, kelancaran, dan hafalan</p>
                            </div>
                        </div>
                        <div class="jadwal-tes-card-body">
                            <div class="jadwal-tes-detail-item">
                                <i class="fa-solid fa-calendar-days"></i>
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
                                    <strong>30 Menit</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } catch (error) {
            console.error(error);
            container.innerHTML = '<div class="jadwal-card"><p style="color:#888;">Gagal memuat jadwal tes.</p></div>';
        }
    }
</script>
@endpush