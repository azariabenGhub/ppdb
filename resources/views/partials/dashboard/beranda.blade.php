{{-- ===== BERANDA ===== --}}
<div id="beranda" class="section">
    <div class="section-wrapper">

        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <p style="font-size:0.9rem; opacity:0.8; margin-bottom:4px;">Selamat Datang di Portal PPDB</p>
            <h1>MI Ziyadatul Ihsan</h1>
            <p>Tahun Ajaran 2025/2026</p>
        </div>

        <!-- Timeline -->
        <div class="timeline-card">
            <h3 style="text-align:center; font-size:1.1rem; color:#1a4d2e; font-weight:700;">Alur Pendaftaran</h3>

            <div class="timeline-wrapper" id="beranda-timeline-container">
                <!-- Garis vertikal di kolom tengah -->
                <div class="vertical-line"></div>
                <!-- Dynamic steps will be rendered here -->
            </div>
        </div>

        <!-- Footer Info -->
        <div class="footer-card">
            <div class="footer-col">
                <h5>Kontak Kami</h5>
                <h3>MI Ziyadatul Ihsan</h3>
                <p id="beranda-kontak-container">
                    Ririn Asmarwati, S.Pd.I (0878 8751 8892)<br>
                    Hayatun Nufus, S.Pd. I (0878 7707 0284)<br>
                    Mamluatul Mukarromah (0822 1073 3866)
                </p>
            </div>
            <div class="footer-col">
                <h5>Alamat Kami</h5>
                <h3 style="color: #1a4d2e; font-weight: 700; margin-bottom: 8px;">MI Ziyadatul Ihsan</h3>
                <p id="beranda-alamat-container">
                    Jl. Sadar No. 33 Rt.001/014 Jatinegara, Cipinang<br>
                    Muara, Kota Jakarta Timur, D.K.I. Jakarta
                </p>
            </div>
        </div>
    </div>
</div><!-- /beranda -->

@push('section-scripts')
<script>
    async function loadBerandaSettings() {
        try {
            const response = await fetch('/api/settings/beranda', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });
            if (!response.ok) return;
            const data = await response.json();

            // 1. Render Timeline (Alur Pendaftaran)
            const timelineContainer = document.getElementById('beranda-timeline-container');
            if (timelineContainer && data.alur) {
                let html = '<div class="vertical-line"></div>';
                data.alur.forEach((step, index) => {
                    const stepNum = index + 1;
                    const isOdd = stepNum % 2 !== 0;
                    
                    const dateHtml = step.date ? `<div class="step-date"><i class="fa-regular fa-calendar-days"></i> ${escapeHtml(step.date)}</div>` : '';
                    const contentHtml = `
                        <div class="step-content">
                            ${dateHtml}
                            <h4>${escapeHtml(step.title)}</h4>
                            <p>${escapeHtml(step.description).replace(/\n/g, '<br>')}</p>
                        </div>
                    `;

                    if (isOdd) {
                        html += `
                            <!-- Step ${stepNum} -->
                            <div class="step-left">
                                ${contentHtml}
                            </div>
                            <div class="step-center">
                                <div class="step-circle">${stepNum}</div>
                            </div>
                            <div class="step-right"></div>
                        `;
                    } else {
                        html += `
                            <!-- Step ${stepNum} -->
                            <div class="step-left"></div>
                            <div class="step-center">
                                <div class="step-circle">${stepNum}</div>
                            </div>
                            <div class="step-right">
                                ${contentHtml}
                            </div>
                        `;
                    }
                });
                timelineContainer.innerHTML = html;
            }

            // 2. Render Kontak
            const kontakContainer = document.getElementById('beranda-kontak-container');
            if (kontakContainer && data.kontak) {
                kontakContainer.innerHTML = escapeHtml(data.kontak).replace(/\n/g, '<br>');
            }

            // 3. Render Alamat
            const alamatContainer = document.getElementById('beranda-alamat-container');
            if (alamatContainer && data.alamat) {
                alamatContainer.innerHTML = escapeHtml(data.alamat).replace(/\n/g, '<br>');
            }

        } catch (error) {
            console.error('Gagal memuat pengaturan beranda:', error);
        }
    }

    // Load immediately if token is available
    if (token) {
        loadBerandaSettings();
    }
</script>
@endpush