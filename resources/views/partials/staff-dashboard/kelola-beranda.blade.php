{{-- partials/staff-dashboard/kelola-beranda.blade.php --}}
<div id="kelola-beranda" class="section" style="display:none;">
    <!-- Pengaturan Beranda Pendaftar -->
    <div class="dashboard-panel">
        <div class="panel-header" style="border-bottom: 1px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-sliders" style="font-size: 20px; color: #1a4d2e;"></i>
            <h3 style="margin: 0; border: none; padding: 0; display: inline-block;">Kelola Konten Beranda Pendaftar</h3>
        </div>

        <form id="form-setting-beranda" onsubmit="saveBerandaSettings(event)">
            <!-- Grid 2 Kolom untuk Kontak & Alamat -->
            <div class="grid-2" style="margin-bottom: 25px;">
                <div>
                    <label class="form-label" for="setting-kontak"><strong>Kontak Kami</strong> (Satu kontak per baris)</label>
                    <textarea id="setting-kontak" class="form-input" rows="4" style="width: 100%; box-sizing: border-box; resize: vertical;" placeholder="Contoh: Ririn Asmarwati (0878...)"></textarea>
                </div>
                <div>
                    <label class="form-label" for="setting-alamat"><strong>Alamat Kami</strong></label>
                    <textarea id="setting-alamat" class="form-input" rows="4" style="width: 100%; box-sizing: border-box; resize: vertical;" placeholder="Masukkan alamat lengkap sekolah..."></textarea>
                </div>
            </div>

            <!-- Alur Pendaftaran Section -->
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 15px;">
                <h4 style="margin: 0; color: #1a4d2e; border: none; padding: 0;">Alur Pendaftaran (Timeline)</h4>
                <button type="button" onclick="tambahLangkahAlur()" style="background: #1a4d2e; color: white; border: none; padding: 6px 12px; border-radius: 4px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-plus"></i> Tambah Langkah
                </button>
            </div>
            
            <div id="timeline-steps-container" class="timeline-steps-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 25px;">
                <!-- Di-render dinamis menggunakan JavaScript -->
            </div>

            <!-- Submit Button -->
            <div style="text-align: right; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                <button type="submit" class="btn-primary" id="btn-save-settings" style="background: #1a4d2e; color: white; padding: 10px 24px; border-radius: 6px; font-weight: 600; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan Beranda
                </button>
            </div>
        </form>
    </div>
</div>

@push('staff-scripts')
<script>
    // Menambahkan langkah baru secara dinamis ke container
    function tambahLangkahAlur(title = '', date = '', description = '') {
        const container = document.getElementById('timeline-steps-container');
        const card = document.createElement('div');
        card.className = 'step-edit-card';
        card.style.cssText = 'background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px;';
        
        card.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px dashed #cbd5e1; padding-bottom: 6px;">
                <span class="step-badge" style="font-weight: 700; color: #1a4d2e;"><i class="fa-solid fa-circle-nodes"></i> Langkah</span>
                <input type="hidden" class="step-num" value="">
                <button type="button" onclick="hapusLangkahAlur(this)" style="background: #ef4444; color: white; border: none; padding: 3px 8px; border-radius: 4px; font-size: 11px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fa-solid fa-trash"></i> Hapus
                </button>
            </div>
            <div style="margin-bottom: 8px;">
                <label style="font-size: 12px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;">Judul Alur</label>
                <input type="text" class="step-title form-input" style="width: 100%; box-sizing: border-box;" required placeholder="Judul alur pendaftaran" value="${escapeHtml(title)}">
            </div>
            <div style="margin-bottom: 8px;">
                <label style="font-size: 12px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;">Tanggal (Opsional)</label>
                <input type="text" class="step-date form-input" style="width: 100%; box-sizing: border-box;" placeholder="Contoh: 5 Mei – 15 Mei" value="${escapeHtml(date)}">
            </div>
            <div>
                <label style="font-size: 12px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;">Deskripsi</label>
                <textarea class="step-desc form-input" rows="3" style="width: 100%; box-sizing: border-box; resize: vertical;" required placeholder="Penjelasan singkat alur ini">${escapeHtml(description)}</textarea>
            </div>
        `;
        
        container.appendChild(card);
        updateStepNumbers();
    }

    // Menghapus langkah alur pendaftaran
    function hapusLangkahAlur(button) {
        const card = button.closest('.step-edit-card');
        if (card) {
            card.remove();
            updateStepNumbers();
        }
    }

    // Memperbarui nomor langkah alur secara berurutan
    function updateStepNumbers() {
        const cards = document.querySelectorAll('#timeline-steps-container .step-edit-card');
        cards.forEach((card, index) => {
            const stepNum = index + 1;
            card.querySelector('.step-badge').innerHTML = `<i class="fa-solid fa-circle-nodes"></i> Langkah ${stepNum}`;
            card.querySelector('.step-num').value = stepNum;
        });
    }

    // Load editor data from API
    async function loadEditorSettings() {
        try {
            const response = await fetch('/api/settings/beranda', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });
            if (!response.ok) return;
            const data = await response.json();

            // Kontak
            if (data.kontak) {
                document.getElementById('setting-kontak').value = data.kontak;
            }
            // Alamat
            if (data.alamat) {
                document.getElementById('setting-alamat').value = data.alamat;
            }

            // Render timeline steps dynamically
            const container = document.getElementById('timeline-steps-container');
            container.innerHTML = '';
            
            if (data.alur && Array.isArray(data.alur) && data.alur.length > 0) {
                data.alur.forEach(step => {
                    tambahLangkahAlur(step.title, step.date, step.description);
                });
            } else {
                // Default render minimal satu langkah kosong jika belum ada data
                tambahLangkahAlur();
            }
        } catch (error) {
            console.error('Gagal memuat konfigurasi beranda untuk editor:', error);
        }
    }

    // Save editor data to API
    async function saveBerandaSettings(event) {
        event.preventDefault();
        const btn = document.getElementById('btn-save-settings');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';

        try {
            const alur = [];
            const stepCards = document.querySelectorAll('#timeline-steps-container .step-edit-card');
            stepCards.forEach((card, index) => {
                const stepVal = index + 1;
                const titleVal = card.querySelector('.step-title').value;
                const dateVal = card.querySelector('.step-date').value;
                const descVal = card.querySelector('.step-desc').value;

                alur.push({
                    step: stepVal,
                    title: titleVal,
                    date: dateVal,
                    description: descVal
                });
            });

            const kontak = document.getElementById('setting-kontak').value;
            const alamat = document.getElementById('setting-alamat').value;

            const response = await fetch('/api/settings/beranda', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ alur, kontak, alamat })
            });

            const result = await response.json();
            if (response.ok) {
                showAlert('success', 'Berhasil menyimpan pengaturan beranda!');
                loadEditorSettings();
            } else {
                showAlert('danger', result.message || 'Gagal menyimpan pengaturan.');
            }
        } catch (error) {
            console.error('Gagal menyimpan:', error);
            showAlert('danger', 'Terjadi kesalahan jaringan.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }
</script>
@endpush
