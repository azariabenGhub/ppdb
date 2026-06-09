{{-- ===== FORMULIR PENDAFTARAN ===== --}}
<div id="formulir" class="section" style="display:none;">
    <div class="section-wrapper">
        <!-- <h2>Formulir Pendaftaran</h2> -->
        <div id="konten-formulir">
            <p style="color:#888;">Memuat...</p>
        </div>
    </div>
</div>

@push('section-scripts')
    <script>
        // ========== STEPPER & NAVIGATION ==========
        function showStep(stepNumber) {
            ['step-1', 'step-2', 'step-3'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.display = 'none';
            });
            const target = document.getElementById('step-' + stepNumber);
            if (target) target.style.display = 'block';
            updateStepper(stepNumber);
        }

        function updateStepper(currentStep) {
            const stepper = document.getElementById('form-stepper');
            if (!stepper) return;
            const steps = stepper.querySelectorAll('.step');
            const lines = stepper.querySelectorAll('.step-line');
            steps.forEach((step, i) => {
                const num = i + 1;
                step.classList.toggle('active', num <= currentStep);
            });
            lines.forEach((line, i) => {
                line.classList.toggle('active', i < currentStep - 1);
            });
        }

        function nextStep(step) { showStep(step); }
        function prevStep(step) { showStep(step); }

        // ========== TOGGLE ORANG TUA / WALI ==========
        function setTipeWali(tipe) {
            const ortu = document.getElementById('form-orang-tua');
            const wali = document.getElementById('form-wali');
            const btns = document.querySelectorAll('#formulir .toggle-btn');
            if (tipe === 'orang_tua') {
                if (ortu) ortu.style.display = 'block';
                if (wali) wali.style.display = 'none';
                btns[0]?.classList.add('active');
                btns[1]?.classList.remove('active');
                document.getElementById('tipe_wali_input').value = 'orang_tua';
            } else {
                if (ortu) ortu.style.display = 'none';
                if (wali) wali.style.display = 'block';
                btns[0]?.classList.remove('active');
                btns[1]?.classList.add('active');
                document.getElementById('tipe_wali_input').value = 'wali';
            }
        }

        // ========== CHECKBOX AKADEMIK ==========
        function toggleInputAkademik(checkbox) {
            const form = document.getElementById('form-akademik');
            if (form) form.style.display = checkbox.checked ? 'none' : 'block';
        }

        // ========== TOGGLE PENDIDIKAN PRA-SEKOLAH ==========
        function toggleTkInput(checkbox) {
            const tkGroup = document.getElementById('tk-group');
            if (tkGroup) {
                tkGroup.style.display = checkbox.checked ? 'block' : 'none';
            }
        }

        // ========== TOGGLE NISN ==========
        function toggleNisnInput(checkbox) {
            const nisnGroup = document.getElementById('nisn-group');
            if (nisnGroup) {
                nisnGroup.style.display = checkbox.checked ? 'block' : 'none';
            }
        }

        // ========== GENERATE HTML ==========

        function stepperHtml(currentStep = 1) {
            const steps = [
                { label: 'Biodata Siswa', num: 1 },
                { label: 'Orang Tua / Wali', num: 2 },
                { label: 'Data Akademik', num: 3 }
            ];
            let html = '';
            steps.forEach((s, i) => {
                html += `<div class="step ${(i + 1) <= currentStep ? 'active' : ''}">
                                        <div class="step-number">${(i + 1) < currentStep ? '<i class="fa-solid fa-check"></i>' : s.num}</div>
                                        <span class="step-label">${s.label}</span>
                                    </div>`;
                if (i < steps.length - 1) {
                    html += `<div class="step-line ${i < currentStep - 1 ? 'active' : ''}"></div>`;
                }
            });
            return `<div class="stepper-container" id="form-stepper">${html}</div>`;
        }

        function stepperHtml(currentStep = 1) {
            const steps = [
                { label: 'Biodata Siswa', num: 1 },
                { label: 'Orang Tua / Wali', num: 2 },
                { label: 'Data Akademik', num: 3 }
            ];
            let html = '';
            steps.forEach((s, i) => {
                html += `<div class="step ${(i + 1) <= currentStep ? 'active' : ''}">
                                <div class="step-number">${(i + 1) < currentStep ? '<i class="fa-solid fa-check"></i>' : s.num}</div>
                                <span class="step-label">${s.label}</span>
                            </div>`;
                if (i < steps.length - 1) {
                    html += `<div class="step-line ${i < currentStep - 1 ? 'active' : ''}"></div>`;
                }
            });
            return `<div class="stepper-container" id="form-stepper">${html}</div>`;
        }

        function step1Fields(data = {}) {
            const v = (key) => {
                const val = (data && data[key] !== undefined && data[key] !== null) ? data[key] : '';
                return escapeHtml(String(val));
            };
            
            // Ambil nilai no_pendaftaran dari data
            const noPendaftaran = data.no_pendaftaran ? data.no_pendaftaran : '';
            // Selalu readonly
            const readonly = 'readonly';
            const placeholder = noPendaftaran ? '' : 'Akan terisi otomatis setelah pengiriman';

            // Pilihan jenis kelamin
            const selectedL = data.jenis_kelamin === 'Laki-laki' ? 'selected' : '';
            const selectedP = data.jenis_kelamin === 'Perempuan' ? 'selected' : '';

            // Ambil nilai checkbox TK
            const pernahTkChecked = data.pernah_tk ? 'checked' : '';
            const tkGroupDisplay = data.pernah_tk ? 'block' : 'none';
            
            // Nilai checkbox NISN
            const punyaNisnChecked = data.punya_nisn ? 'checked' : '';
            const nisnGroupDisplay = data.punya_nisn ? 'block' : 'none';
            
            return `
                <div class="input-group">
                    <label>No. Pendaftaran/Induk:</label>
                    <input type="text" id="no_pendaftaran" 
                        value="${escapeHtml(noPendaftaran)}" 
                        ${readonly} 
                        placeholder="${placeholder}">
                </div>
<div class="input-row">
        <div class="input-group"><label>Nama Lengkap</label><input type="text" id="nama_lengkap" value="${v('nama_lengkap')}" placeholder="Penulisan nama harus sesuai dengan kartu keluarga"></div>
        <div class="input-group">
            <label>Jenis Kelamin</label>
            <select id="jenis_kelamin">
                <option value="">Pilih Jenis Kelamin</option>
                <option value="Laki-laki" ${selectedL}>Laki-laki</option>
                <option value="Perempuan" ${selectedP}>Perempuan</option>
            </select>
        </div>
    </div>
                <div class="input-row">
                    <div class="input-group"><label>Tempat Lahir</label><input type="text" id="tempat_lahir" value="${v('tempat_lahir')}" placeholder="Contoh: Jakarta"></div>
                    <div class="input-group"><label>Tanggal Lahir</label><input type="date" id="tanggal_lahir" value="${v('tanggal_lahir')}"></div>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="pernah_tk" onchange="toggleTkInput(this)" ${pernahTkChecked}>
                    <label for="pernah_tk">Apakah pernah mengikuti pendidikan pra-sekolah (RA/TK/PAUD)?</label>
                </div>
                <div id="tk-group" style="display: ${tkGroupDisplay};">
                    <div class="input-group">
                        <label>Asal RA/TK/PAUD</label>
                        <input type="text" id="asal_tk" value="${v('asal_tk')}" placeholder="Contoh: TK Pertiwi 01">
                    </div>
                </div>
                <div class="input-group"><label>NIK</label><input type="text" id="nik" value="${v('nik')}" placeholder="Nomor Induk Kependudukan"></div>
                <div class="input-row">
                    <div class="input-group"><label>Agama</label><select id="agama">${agamaOptions(data.agama)}</select></div>
                    <div class="input-group"><label>Warga Negara</label><select id="warga_negara">
                        <option ${data.warga_negara === 'WNI' ? 'selected' : ''}>WNI</option>
                        <option ${data.warga_negara === 'WNA' ? 'selected' : ''}>WNA</option>
                    </select></div>
                </div>
                <div class="input-row">
                    <div class="input-group"><label>Anak ke</label><input type="number" id="anak_ke" value="${v('anak_ke')}" min="1" placeholder="Isi dengan angka"></div>
                    <div class="input-group"><label>Jumlah Saudara</label><input type="number" id="jumlah_saudara" value="${v('jumlah_saudara')}" min="0" placeholder="Isi dengan angka"></div>
                </div>
                <div class="input-group"><label>Alamat</label><textarea id="alamat_lengkap" rows="4" placeholder="Alamat Lengkap">${v('alamat_lengkap')}</textarea></div>
                
                <!-- Checkbox NISN -->
                <div class="checkbox-group">
                    <input type="checkbox" id="punya_nisn" onchange="toggleNisnInput(this)" ${punyaNisnChecked}>
                    <label for="punya_nisn">Sudah punya NISN?</label>
                </div>
                <div id="nisn-group" style="display: ${nisnGroupDisplay};">
                    <div class="input-group">
                        <label>Nomor NISN</label>
                        <input type="text" id="nisn" value="${v('nisn')}" placeholder="Masukkan NISN (10 digit)">
                    </div>
                </div>
            `;
        }

        function agamaOptions(selected) {
            const list = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
            return list.map(a => `<option value="${a}" ${selected === a ? 'selected' : ''}>${a}</option>`).join('');
        }

        function step2Fields(data = {}) {
            const v = (key) => escapeHtml(data[key] || '');
            const tipe = data.tipe_wali || 'orang_tua';
            const ortuActive = tipe === 'orang_tua' ? 'active' : '';
            const waliActive = tipe === 'wali' ? 'active' : '';
            const ortuDisplay = tipe === 'orang_tua' ? '' : 'style="display:none"';
            const waliDisplay = tipe === 'wali' ? '' : 'style="display:none"';
            const ortuForm = `
                                    <h4 class="section-badge">AYAH</h4>
                                    <div class="input-group"><label>Nama Lengkap Ayah</label><input type="text" id="nama_ayah" value="${v('nama_ayah')}" placeholder="Nama sesuai kartu keluarga"></div>
                                    <div class="input-row">
                                        <div class="input-group"><label>Pekerjaan</label><input type="text" id="pekerjaan_ayah" value="${v('pekerjaan_ayah')}" placeholder="Pekerjaan Ayah"></div>
                                        <div class="input-group"><label>Agama</label><select id="agama_ayah">${agamaOptions(data.agama_ayah)}</select></div>
                                    </div>
                                    <div class="input-row">
                                        <div class="input-group"><label>Pendidikan Terakhir</label><input type="text" id="pendidikan_ayah" value="${v('pendidikan_ayah')}" placeholder="Pendidikan Terakhir Ayah"></div>
                                        <div class="input-group"><label>No. KTP</label><input type="text" id="no_ktp_ayah" value="${v('no_ktp_ayah')}" placeholder="Nomor KTP Ayah"></div>
                                    </div>
                                    <div class="input-row">
                                        <div class="input-group"><label>Penghasilan Per Bulan</label><input type="text" id="penghasilan_ayah" value="${v('penghasilan_ayah')}" placeholder="Penghasilan Ayah"></div>
                                        <div class="input-group"><label>No. Telp/HP</label><input type="text" id="no_telp_ayah" value="${v('no_telp_ayah')}" placeholder="0812-3456-7890"></div>
                                    </div>
                                    <div class="input-group"><label>Alamat</label><textarea id="alamat_ayah" rows="3" placeholder="Alamat Lengkap Ayah">${v('alamat_ayah')}</textarea></div>
                                    <hr class="form-divider">
                                    <h4 class="section-badge">IBU</h4>
                                    <div class="input-group"><label>Nama Lengkap Ibu</label><input type="text" id="nama_ibu" value="${v('nama_ibu')}" placeholder="Nama sesuai kartu keluarga"></div>
                                    <div class="input-row">
                                        <div class="input-group"><label>Pekerjaan</label><input type="text" id="pekerjaan_ibu" value="${v('pekerjaan_ibu')}" placeholder="Pekerjaan Ibu"></div>
                                        <div class="input-group"><label>Agama</label><select id="agama_ibu">${agamaOptions(data.agama_ibu)}</select></div>
                                    </div>
                                    <div class="input-row">
                                        <div class="input-group"><label>Pendidikan Terakhir</label><input type="text" id="pendidikan_ibu" value="${v('pendidikan_ibu')}" placeholder="Pendidikan Terakhir Ibu"></div>
                                        <div class="input-group"><label>No. KTP</label><input type="text" id="no_ktp_ibu" value="${v('no_ktp_ibu')}" placeholder="Nomor KTP Ibu"></div>
                                    </div>
                                    <div class="input-row">
                                        <div class="input-group"><label>Penghasilan Per Bulan</label><input type="text" id="penghasilan_ibu" value="${v('penghasilan_ibu')}" placeholder="Penghasilan Ibu"></div>
                                        <div class="input-group"><label>No. Telp/HP</label><input type="text" id="no_telp_ibu" value="${v('no_telp_ibu')}" placeholder="0812-3456-7890"></div>
                                    </div>
                                    <div class="input-group"><label>Alamat</label><textarea id="alamat_ibu" rows="3" placeholder="Alamat Lengkap Ibu">${v('alamat_ibu')}</textarea></div>
                                `;
            const waliForm = `
                                    <h4 class="section-badge">WALI</h4>
                                    <div class="input-group"><label>Nama Lengkap Wali</label><input type="text" id="nama_wali" value="${v('nama_wali')}" placeholder="Nama sesuai kartu keluarga wali"></div>
                                    <div class="input-row">
                                        <div class="input-group"><label>Pekerjaan</label><input type="text" id="pekerjaan_wali" value="${v('pekerjaan_wali')}" placeholder="Pekerjaan Wali"></div>
                                        <div class="input-group"><label>Agama</label><select id="agama_wali">${agamaOptions(data.agama_wali)}</select></div>
                                    </div>
                                    <div class="input-row">
                                        <div class="input-group"><label>Pendidikan Terakhir</label><input type="text" id="pendidikan_wali" value="${v('pendidikan_wali')}" placeholder="Pendidikan Terakhir Wali"></div>
                                        <div class="input-group"><label>No. KTP</label><input type="text" id="no_ktp_wali" value="${v('no_ktp_wali')}" placeholder="Nomor KTP Wali"></div>
                                    </div>
                                    <div class="input-row">
                                        <div class="input-group"><label>Penghasilan Per Bulan</label><input type="text" id="penghasilan_wali" value="${v('penghasilan_wali')}" placeholder="Penghasilan Wali"></div>
                                        <div class="input-group"><label>No. Telp/HP</label><input type="text" id="no_telp_wali" value="${v('no_telp_wali')}" placeholder="0812-3456-7890"></div>
                                    </div>
                                    <div class="input-group"><label>Alamat</label><textarea id="alamat_wali" rows="3" placeholder="Alamat Lengkap Wali">${v('alamat_wali')}</textarea></div>
                                `;
            return `
                                    <div class="form-title"><h3>Formulir Pendaftaran</h3><p>Siapa yang bertanggung jawab atas siswa ini?</p></div>
                                    <div class="toggle-container">
                                        <button type="button" class="toggle-btn ${ortuActive}" onclick="setTipeWali('orang_tua')">Orang Tua</button>
                                        <button type="button" class="toggle-btn ${waliActive}" onclick="setTipeWali('wali')">Wali</button>
                                    </div>
                                    <input type="hidden" id="tipe_wali_input" value="${tipe}">
                                    <div id="form-orang-tua" ${ortuDisplay}>${ortuForm}</div>
                                    <div id="form-wali" ${waliDisplay}>${waliForm}</div>
                                `;
        }

        function step3Fields(data = {}) {
            const v = (key) => escapeHtml(data[key] || '');
            const checked = data.is_bukan_pindahan ? 'checked' : '';
            const display = data.is_bukan_pindahan ? 'style="display:none"' : '';
            return `
                                    <div class="form-title"><h3>Formulir Pendaftaran</h3><p>Isi data akademik calon siswa</p></div>
                                    <div id="form-akademik" ${display}>
                                        <div class="input-group"><label>Asal Sekolah/Madrasah</label><input type="text" id="asal_sekolah" value="${v('asal_sekolah')}" placeholder="Nama Sekolah/Madrasah Asal"></div>
                                        <div class="input-row">
                                            <div class="input-group"><label>No. Ijazah/STTB</label><input type="text" id="no_ijazah" value="${v('no_ijazah')}" placeholder="Nomor Ijazah/Surat Tanda Tamat Belajar"></div>
                                            <div class="input-group"><label>Tahun</label><input type="text" id="tahun_ijazah" value="${v('tahun_ijazah')}" placeholder="Tahun Ijazah"></div>
                                        </div>
                                        <div class="input-group"><label>Diterima di Kelas</label><input type="text" id="diterima_kelas" value="${v('diterima_kelas')}" placeholder="Diterima di Kelas"></div>
                                        <div class="input-group"><label>Pindah dari</label><input type="text" id="pindah_dari" value="${v('pindah_dari')}" placeholder="Pindah dari (jika pindahan)"></div>
                                        <div class="input-row">
                                            <div class="input-group"><label>No. Pindah</label><input type="text" id="no_pindah" value="${v('no_pindah')}"></div>
                                            <div class="input-group"><label>Tanggal Pindah</label><input type="date" id="tanggal_pindah" value="${v('tanggal_pindah')}"></div>
                                        </div>
                                    </div>
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="bukan-pindahan" ${checked} onchange="toggleInputAkademik(this)">
                                        <label for="bukan-pindahan">Bukan Murid Pindahan</label>
                                    </div>
                                    <div class="alert-info"><p>Pastikan semua data sudah sesuai.</p></div>
                                `;
        }

        function formHtmlKosong(gel) {
            return `
                                    <div class="form-header-card">
                                        <div class="school-brand">
                                            <img src="{{ asset('storage/assets/logo-mizi.png') }}" alt="Logo">
                                            <div class="brand-text">
                                                <p>YAYASAN PENDIDIKAN ISLAM ZIYADATUL IHSAN</p>
                                                <h2>MADRASAH IBTIDAIYAH</h2>
                                            </div>
                                        </div>
                                        ${stepperHtml(1)}
                                    </div>
                                    <div class="form-card-new">
                                        <div id="step-1">
                                            ${step1Fields()}
                                            <div class="form-actions" style="justify-content:flex-end;"><button class="btn-primary" onclick="nextStep(2)">LANJUT</button></div>
                                        </div>
                                        <div id="step-2" style="display:none;">
                                            ${step2Fields()}
                                            <div class="form-actions space-between">
                                                <button class="btn-secondary" onclick="prevStep(1)">KEMBALI</button>
                                                <button class="btn-primary" onclick="nextStep(3)">LANJUT</button>
                                            </div>
                                        </div>
                                        <div id="step-3" style="display:none;">
                                            ${step3Fields()}
                                            <div class="form-actions space-between">
                                                <button class="btn-secondary" onclick="prevStep(2)">KEMBALI</button>
                                                <button class="btn-primary" onclick="submitForm()">KIRIM</button>
                                            </div>
                                        </div>
                                    </div>
                                `;
        }

        function formHtmlEdit(data, gel) {
            return `
                                    <div class="form-header-card">
                                        <div class="school-brand">
                                            <img src="{{ asset('storage/assets/logo-mizi.png') }}" alt="Logo">
                                            <div class="brand-text">
                                                <p>YAYASAN PENDIDIKAN ISLAM ZIYADATUL IHSAN</p>
                                                <h2>MADRASAH IBTIDAIYAH</h2>
                                            </div>
                                        </div>
                                        ${stepperHtml(1)}
                                    </div>
                                    <div class="form-card-new">
                                        <div id="step-1">
                                            ${step1Fields(data)}
                                            <div class="form-actions" style="justify-content:flex-end;"><button class="btn-primary" onclick="nextStep(2)">LANJUT</button></div>
                                        </div>
                                        <div id="step-2" style="display:none;">
                                            ${step2Fields(data)}
                                            <div class="form-actions space-between">
                                                <button class="btn-secondary" onclick="prevStep(1)">KEMBALI</button>
                                                <button class="btn-primary" onclick="nextStep(3)">LANJUT</button>
                                            </div>
                                        </div>
                                        <div id="step-3" style="display:none;">
                                            ${step3Fields(data)}
                                            <div class="form-actions space-between">
                                                <button class="btn-secondary" onclick="prevStep(2)">KEMBALI</button>
                                                <button class="btn-primary" onclick="submitForm()">KIRIM</button>
                                            </div>
                                        </div>
                                    </div>
                                `;
        }

        // TAMPILAN SUKSES setelah formulir terkirim (status menunggu)
        function formHtmlSukses(gel) {
            return `
                                    <div class="form-header-card">
                                        <div class="school-brand">
                                            <img src="{{ asset('storage/assets/logo-mizi.png') }}" alt="Logo">
                                            <div class="brand-text">
                                                <p>YAYASAN PENDIDIKAN ISLAM ZIYADATUL IHSAN</p>
                                                <h2>MADRASAH IBTIDAIYAH</h2>
                                            </div>
                                        </div>
                                        ${stepperHtml(3)}  <!-- semua langkah aktif -->
                                    </div>
                                    <div class="success-card">
                                        <div class="success-icon-wrapper">
                                            <i class="fa-regular fa-clock"></i>
                                        </div>
                                        <h2 class="success-title">Formulir Berhasil Dikirim!</h2>
                                        <div class="success-text">
                                            <p>Terima kasih telah mendaftarkan putra/putri Anda.</p>
                                            <p>Panitia sedang memeriksa kelengkapan dan keabsahan data anda.</p>
                                            <p>Proses ini memakan waktu 1-3 hari kerja.</p>
                                        </div>
                                    </div>
                                `;
        }

        async function getGelombangAktif() {
            try {
                const res = await fetch('/api/gelombang/aktif', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const gel = await res.json();
                return gel && gel.id ? gel : null;
            } catch (e) {
                console.error(e);
                return null;
            }
        }

        // ========== LOAD FORMULIR ==========
        async function loadFormulirSection() {
            const konten = document.getElementById('konten-formulir');
            if (!token) {
                konten.innerHTML = '<p>Sesi habis. Silakan login ulang.</p>';
                return;
            }
            try {
                const resp = await fetch('/api/formulir-saya', { headers: { Authorization: 'Bearer ' + token } });
                if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
                const result = await resp.json();
                const data = result.data;

                if (data && data.status === 'diterima') {
                    konten.innerHTML = `
                        <div class="form-header-card">
                            <div class="school-brand">
                                <img src="/storage/assets/logo-mizi.png" alt="Logo">
                                <div class="brand-text">
                                    <p>YAYASAN PENDIDIKAN ISLAM ZIYADATUL IHSAN</p>
                                    <h2>MADRASAH IBTIDAIYAH</h2>
                                </div>
                            </div>
                            ${stepperHtml(3)}
                        </div>
                        <div class="success-card">
                            <div class="success-icon-wrapper">
                                <i class="fa-regular fa-check-circle"></i>
                            </div>
                            <h2 class="success-title">Formulir Pendaftaran Diterima</h2>
                            <div class="success-text">
                                <p>Selamat! Formulir pendaftaran Anda telah diterima dan diverifikasi oleh panitia.</p>
                                <p>Silakan melanjutkan ke tahap berikutnya.</p>
                            </div>
                        </div>
                    `;
                    return;
                }
                const gel = await getGelombangAktif();
                if (!gel) {
                    konten.innerHTML = '<p>Tidak ada gelombang pendaftaran aktif.</p>';
                    return;
                }
                if (data === null) {
                    konten.innerHTML = formHtmlKosong(gel);
                    showStep(1);
                } else if (data.status === 'menunggu') {
                    // Tampilan sukses setelah kirim formulir
                    konten.innerHTML = formHtmlSukses(gel);
                } else if (data.status === 'ditolak') {
                    konten.innerHTML = `<p>Ditolak: ${data.verifikasi?.catatan || '-'}</p>` + formHtmlEdit(data, gel);
                    showStep(1);
                }
            } catch (e) {
                console.error('Error loadFormulirSection:', e);
                konten.innerHTML = '<p>Gagal memuat formulir.</p>';
            }
        }

async function submitForm() {
    const payload = getFormData();

    // 1. VALIDASI REQUIRED MANUAL
    // Cek apakah data-data krusial kosong
    if (!payload.nama_lengkap || !payload.jenis_kelamin || !payload.tempat_lahir || !payload.tanggal_lahir || !payload.nik || !payload.agama) {
        alert('Mohon lengkapi semua data wajib pada formulir!');
        return; // Hentikan proses kalau ada yang kosong
    }

    // 2. MENCEGAH DOUBLE SUBMIT (Frontend Lock)
    // Cari tombol kirim di step 3 dan matikan sementara
    const btnSubmit = document.querySelector('#step-3 .btn-primary');
    if (btnSubmit) {
        btnSubmit.disabled = true;
        btnSubmit.innerText = 'MENGIRIM...';
    }

    try {
        const resp = await fetch('/api/formulir', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Authorization: 'Bearer ' + token },
            body: JSON.stringify(payload)
        });
        
        if (resp.ok) {
            alert('Pendaftaran berhasil!');
            loadFormulirSection(); 
        } else {
            const err = await resp.json();
            alert('Gagal: ' + (err.message || JSON.stringify(err.errors)));
            
            // Nyalakan tombol lagi kalau gagal biar bisa coba lagi
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.innerText = 'KIRIM';
            }
        }
    } catch (e) {
        alert('Kesalahan jaringan. Silakan coba lagi.');
        if (btnSubmit) {
            btnSubmit.disabled = false;
            btnSubmit.innerText = 'KIRIM';
        }
    }
}

        // getFormData dengan tambahan punya_nisn dan nisn
        function getFormData() {
            const tipe_wali = document.getElementById('tipe_wali_input')?.value || 'orang_tua';
            const isBukanPindahan = document.getElementById('bukan-pindahan')?.checked || false;
            const data = {
                nama_lengkap: document.getElementById('nama_lengkap')?.value || '',
                tempat_lahir: document.getElementById('tempat_lahir')?.value || '',
                tanggal_lahir: document.getElementById('tanggal_lahir')?.value || '',
                jenis_kelamin: document.getElementById('jenis_kelamin')?.value || '',
                pernah_tk: document.getElementById('pernah_tk')?.checked || false,
                asal_tk: document.getElementById('asal_tk')?.value || '',
                nik: document.getElementById('nik')?.value || '',
                agama: document.getElementById('agama')?.value || '',
                warga_negara: document.getElementById('warga_negara')?.value || '',
                anak_ke: document.getElementById('anak_ke')?.value || '',
                jumlah_saudara: document.getElementById('jumlah_saudara')?.value || '',
                alamat_lengkap: document.getElementById('alamat_lengkap')?.value || '',
                tipe_wali: tipe_wali,
                is_bukan_pindahan: isBukanPindahan,
                punya_nisn: document.getElementById('punya_nisn')?.checked || false,
                nisn: document.getElementById('nisn')?.value || '',
            };
            if (tipe_wali === 'orang_tua') {
                Object.assign(data, {
                    nama_ayah: document.getElementById('nama_ayah')?.value || '',
                    pekerjaan_ayah: document.getElementById('pekerjaan_ayah')?.value || '',
                    agama_ayah: document.getElementById('agama_ayah')?.value || '',
                    pendidikan_ayah: document.getElementById('pendidikan_ayah')?.value || '',
                    no_ktp_ayah: document.getElementById('no_ktp_ayah')?.value || '',
                    penghasilan_ayah: document.getElementById('penghasilan_ayah')?.value || '',
                    no_telp_ayah: document.getElementById('no_telp_ayah')?.value || '',
                    alamat_ayah: document.getElementById('alamat_ayah')?.value || '',
                    nama_ibu: document.getElementById('nama_ibu')?.value || '',
                    pekerjaan_ibu: document.getElementById('pekerjaan_ibu')?.value || '',
                    agama_ibu: document.getElementById('agama_ibu')?.value || '',
                    pendidikan_ibu: document.getElementById('pendidikan_ibu')?.value || '',
                    no_ktp_ibu: document.getElementById('no_ktp_ibu')?.value || '',
                    penghasilan_ibu: document.getElementById('penghasilan_ibu')?.value || '',
                    no_telp_ibu: document.getElementById('no_telp_ibu')?.value || '',
                    alamat_ibu: document.getElementById('alamat_ibu')?.value || '',
                });
            } else {
                Object.assign(data, {
                    nama_wali: document.getElementById('nama_wali')?.value || '',
                    pekerjaan_wali: document.getElementById('pekerjaan_wali')?.value || '',
                    agama_wali: document.getElementById('agama_wali')?.value || '',
                    pendidikan_wali: document.getElementById('pendidikan_wali')?.value || '',
                    no_ktp_wali: document.getElementById('no_ktp_wali')?.value || '',
                    penghasilan_wali: document.getElementById('penghasilan_wali')?.value || '',
                    no_telp_wali: document.getElementById('no_telp_wali')?.value || '',
                    alamat_wali: document.getElementById('alamat_wali')?.value || '',
                });
            }
            if (!isBukanPindahan) {
                Object.assign(data, {
                    asal_sekolah: document.getElementById('asal_sekolah')?.value || '',
                    no_ijazah: document.getElementById('no_ijazah')?.value || '',
                    tahun_ijazah: document.getElementById('tahun_ijazah')?.value || '',
                    diterima_kelas: document.getElementById('diterima_kelas')?.value || '',
                    pindah_dari: document.getElementById('pindah_dari')?.value || '',
                    no_pindah: document.getElementById('no_pindah')?.value || '',
                    tanggal_pindah: document.getElementById('tanggal_pindah')?.value || '',
                });
            }
            return data;
        }
    </script>
@endpush