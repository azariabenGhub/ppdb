{{-- ===== DAFTAR ULANG ===== --}}
<div id="daftar-ulang" class="section" style="display:none;">
    <div class="content-wrapper animate-fade-in" style="padding: 30px 40px; max-width: 1200px; margin: 0 auto;">
        <!-- Header sekolah -->
        <div class="form-header-card">
            <div class="school-brand-centered">
                <img src="{{ asset('storage/assets/logo-mizi.png') }}" alt="Logo">
                <div class="brand-text">
                    <p>YAYASAN PENDIDIKAN ISLAM ZIYADATUL IHSAN</p>
                    <h2>MADRASAH IBTIDAIYAH</h2>
                </div>
            </div>
        </div>

        <!-- Kartu utama dengan toggle -->
        <div class="form-card">
            <div class="form-title">
                <h3>Formulir Pendaftaran</h3>
                <p>Selesaikan dua langkah berikut untuk melengkapi proses daftar ulang</p>
            </div>

            <!-- Tombol toggle -->
            <div class="toggle-container">
                <button type="button" class="toggle-btn active" id="tabFormBtn">Formulir Daftar Ulang</button>
                <button type="button" class="toggle-btn" id="tabPersyaratanBtn">Surat Pernyataan & Persyaratan</button>
            </div>

            <!-- TAB 1: Formulir Daftar Ulang (dinamis, dua versi) -->
            <div id="tabFormulir" style="display: block;">
                <form id="formDaftarUlangData">
                    <div class="form-section">
                        <span class="badge-label">DATA SISWA</span>
                        <div class="input-group">
                            <label>No. Pendaftaran/Induk:</label>
                            <input type="text" id="no_pendaftaran" name="no_pendaftaran" readonly
                                class="input-readonly">
                        </div>
                        <div class="input-group">
                            <label>Nama Lengkap</label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap"
                                placeholder="Penulisan nama harus sesuai dengan kartu keluarga" required>
                        </div>
                        <div class="input-row-three">
                            <div class="input-group">
                                <label>Tempat Lahir</label>
                                <input type="text" id="tempat_lahir" name="tempat_lahir" placeholder="Contoh: Jakarta"
                                    required>
                            </div>
                            <div class="input-group">
                                <label>Tanggal Lahir</label>
                                <input type="text" id="tanggal_lahir" name="tanggal_lahir"
                                    placeholder="Format: dd/mm/yyyy (Contoh: 21-12-2021)" required>
                            </div>
                            <div class="input-group">
                                <label>Jenis Kelamin</label>
                                <input type="text" id="jenis_kelamin" name="jenis_kelamin"
                                    placeholder="Laki-laki / Perempuan">
                            </div>
                        </div>
                        <div class="input-group">
                            <label>Asal RA/TK/PAUD</label>
                            <input type="text" id="asal_tk" name="asal_tk" placeholder="Asal RA/TK/PAUD">
                        </div>
                    </div>

                    <!-- KONTAINER DINAMIS: akan diisi JS dengan form versi orang tua atau wali -->
                    <div id="dynamicWaliContainer"></div>

                    <!-- Hidden fields untuk data tambahan (diisi JS) -->
                    <input type="hidden" id="tipe_wali" name="tipe_wali">
                    <input type="hidden" id="nik_siswa" name="nik">
                    <input type="hidden" id="agama_siswa" name="agama">
                    <input type="hidden" id="warga_negara" name="warga_negara" value="WNI">
                    <input type="hidden" id="anak_ke" name="anak_ke">
                    <input type="hidden" id="jumlah_saudara" name="jumlah_saudara">
                    <input type="hidden" id="is_bukan_pindahan" name="is_bukan_pindahan" value="1">
                    <input type="hidden" id="asal_sekolah_pindahan" name="asal_sekolah_pindahan">
                    <div id="hiddenFieldsOrangTua" style="display:none;"></div>
                    <div id="hiddenFieldsWali" style="display:none;"></div>

                    <div class="form-actions-end">
                        <button type="button" class="btn-solid" id="btnLanjutKeUpload">LANJUT</button>
                    </div>
                </form>
            </div>

            <!-- TAB 2: Surat Pernyataan & Persyaratan (upload file) -->
            <div id="tabPersyaratan" style="display: none;">
                <div id="persyaratan-content">
                    <div style="text-align: center; padding: 40px;">Memuat data...</div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('section-scripts')
    <script>
        // ========== GLOBAL ==========
        let rawDataAwal = null;
        let sudahLoadPersyaratan = false;

        // ========== FUNGSI TEMPLATE SURAT (untuk tab persyaratan) ==========
        async function getTemplateLinks() {
            try {
                const resTemplates = await fetch('/api/template-surat', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const templates = await resTemplates.json();
                if (!templates.length) {
                    return '<span style="color:#dc2626;">Template surat belum tersedia. Hubungi panitia.</span>';
                }
                return templates.map(t => {
                    const namaAman = escapeHtml(t.nama);
                    const idAman = escapeHtml(String(t.id));
                    return `<a href="/api/template-surat/download/${idAman}?token=${token}" target="_blank" class="btn-download" style="display:inline-flex; margin-right:10px;">
                        <i class="fa-solid fa-file-pdf"></i> Download ${namaAman}
                        <i class="fa-solid fa-download icon-small"></i>
                    </a>`;
                }).join('');
            } catch (e) {
                return '<span style="color:#dc2626;">Gagal memuat template.</span>';
            }
        }

        // ========== AMBIL DATA FORMULIR PENDAFTARAN AWAL ==========
        async function loadDataAwal() {
            try {
                const res = await fetch('/api/daftar-ulang/form-data', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const result = await res.json();
                if (result.eligible === false) {
                    document.getElementById('tabFormulir').innerHTML = `
                                                <div class="empty-state-box">
                                                    <div class="message-content">
                                                        <i class="fa-solid fa-circle-info"></i>
                                                        <strong>Informasi</strong>
                                                        <hr class="message-divider">
                                                        <p class="sub-message">${result.message}</p>
                                                    </div>
                                                </div>`;
                    return null;
                }
                rawDataAwal = result;
                return result;
            } catch (e) {
                console.error(e);
                return null;
            }
        }

        // ========== RENDER FORM SESUAI TIPE WALI (dengan ayah & ibu bersebelahan) ==========
        function renderDynamicForm(tipe, header, detail) {
            const container = document.getElementById('dynamicWaliContainer');
            const v = (obj, key, def = '') => (obj && obj[key] !== undefined && obj[key] !== null) ? obj[key] : def;

            let html = `<div class="form-section mt-30"><span class="badge-label">DATA ${tipe === 'orang_tua' ? 'ORANG TUA' : 'WALI'}</span>`;

            if (tipe === 'orang_tua') {
                // Baris 1: Nama Ayah dan Nama Ibu (bersebelahan)
                html += `
                                            <div class="input-row">
                                                <div class="input-group">
                                                    <label>Nama Ayah</label>
                                                    <input type="text" name="nama_ayah" value="${v(detail, 'nama_ayah')}" placeholder="Nama lengkap sesuai KK" required>
                                                </div>
                                                <div class="input-group">
                                                    <label>Nama Ibu</label>
                                                    <input type="text" name="nama_ibu" value="${v(detail, 'nama_ibu')}" placeholder="Nama lengkap sesuai KK" required>
                                                </div>
                                            </div>
                                        `;
                // Baris 2: Pendidikan Ayah dan Pendidikan Ibu
                html += `
                                            <div class="input-row">
                                                <div class="input-group">
                                                    <label>Pendidikan Terakhir Ayah</label>
                                                    <input type="text" name="pendidikan_ayah" value="${v(detail, 'pendidikan_ayah')}" placeholder="Pendidikan Terakhir Ayah">
                                                </div>
                                                <div class="input-group">
                                                    <label>Pendidikan Terakhir Ibu</label>
                                                    <input type="text" name="pendidikan_ibu" value="${v(detail, 'pendidikan_ibu')}" placeholder="Pendidikan Terakhir Ibu">
                                                </div>
                                            </div>
                                        `;
                // Baris 3: Pekerjaan Ayah dan Pekerjaan Ibu
                html += `
                                            <div class="input-row">
                                                <div class="input-group">
                                                    <label>Pekerjaan Ayah</label>
                                                    <input type="text" name="pekerjaan_ayah" value="${v(detail, 'pekerjaan_ayah')}" placeholder="Pekerjaan Ayah">
                                                </div>
                                                <div class="input-group">
                                                    <label>Pekerjaan Ibu</label>
                                                    <input type="text" name="pekerjaan_ibu" value="${v(detail, 'pekerjaan_ibu')}" placeholder="Pekerjaan Ibu">
                                                </div>
                                            </div>
                                        `;
                // Alamat KTP (dari ayah) dan Alamat Domisili (input baru)
                html += `
                                            <div class="input-group">
                                                <label>Alamat KTP (sesuai KTP)</label>
                                                <input type="text" name="alamat_ktp" value="${v(detail, 'alamat_ktp')}" placeholder="Alamat sesuai KTP">
                                            </div>
                                            <div class="input-group">
                                                <label>Alamat Domisili (tempat tinggal sekarang)</label>
                                                <input type="text" name="alamat_domisili" placeholder="Alamat lengkap tempat tinggal sekarang">
                                            </div>
                                        `;
                // Nomor HP (dari ayah) dan Narahubung (input baru) dalam satu baris
                html += `
                                            <div class="input-row">
                                                <div class="input-group">
                                                    <label>Nomor HP / Telp</label>
                                                    <input type="text" name="no_hp" value="${v(detail, 'no_hp')}" placeholder="Kontak orang tua">
                                                </div>
                                                <div class="input-group">
                                                    <label>Narahubung (kontak darurat selain orang tua)</label>
                                                    <input type="text" name="narahubung" placeholder="Kontak darurat">
                                                </div>
                                            </div>
                                        `;
                // Hidden fields untuk data ayah & ibu (lengkap)
                document.getElementById('hiddenFieldsOrangTua').innerHTML = `
                                            <input type="hidden" name="nik_ayah" value="${v(detail, 'nik_ayah')}">
                                            <input type="hidden" name="agama_ayah" value="${v(detail, 'agama_ayah')}">
                                            <input type="hidden" name="penghasilan_ayah" value="${v(detail, 'penghasilan_ayah')}">
                                            <input type="hidden" name="alamat_ayah" value="${v(detail, 'alamat_ayah')}">
                                            <input type="hidden" name="nik_ibu" value="${v(detail, 'nik_ibu')}">
                                            <input type="hidden" name="agama_ibu" value="${v(detail, 'agama_ibu')}">
                                            <input type="hidden" name="penghasilan_ibu" value="${v(detail, 'penghasilan_ibu')}">
                                            <input type="hidden" name="no_telp_ibu" value="${v(detail, 'no_telp_ibu')}">
                                            <input type="hidden" name="alamat_ibu" value="${v(detail, 'alamat_ibu')}">
                                        `;
                document.getElementById('hiddenFieldsWali').innerHTML = '';
            } else {
                // Wali form (single column) sama seperti sebelumnya
                html += `
                                            <div class="input-group">
                                                <label>Nama Wali</label>
                                                <input type="text" name="nama_wali" value="${v(detail, 'nama_wali')}" placeholder="Nama Wali" required>
                                            </div>
                                            <div class="input-group">
                                                <label>Pendidikan Terakhir Wali</label>
                                                <input type="text" name="pendidikan_wali" value="${v(detail, 'pendidikan_wali')}" placeholder="Pendidikan Terakhir Wali">
                                            </div>
                                            <div class="input-group">
                                                <label>Pekerjaan Wali</label>
                                                <input type="text" name="pekerjaan_wali" value="${v(detail, 'pekerjaan_wali')}" placeholder="Pekerjaan Wali">
                                            </div>
                                            <div class="input-group">
                                                <label>Alamat KTP (sesuai KTP)</label>
                                                <input type="text" name="alamat_ktp" value="${v(detail, 'alamat_ktp')}" placeholder="Alamat sesuai KTP">
                                            </div>
                                            <div class="input-group">
                                                <label>Alamat Domisili (tempat tinggal sekarang)</label>
                                                <input type="text" name="alamat_domisili" placeholder="Alamat lengkap tempat tinggal sekarang">
                                            </div>
                                            <div class="input-row">
                                                <div class="input-group">
                                                    <label>Nomor HP / Telp</label>
                                                    <input type="text" name="no_hp" value="${v(detail, 'no_hp')}" placeholder="Kontak wali">
                                                </div>
                                                <div class="input-group">
                                                    <label>Narahubung (kontak darurat selain wali)</label>
                                                    <input type="text" name="narahubung" placeholder="Kontak darurat">
                                                </div>
                                            </div>
                                        `;
                document.getElementById('hiddenFieldsWali').innerHTML = `
                                            <input type="hidden" name="nik_wali" value="${v(detail, 'nik_wali')}">
                                            <input type="hidden" name="agama_wali" value="${v(detail, 'agama_wali')}">
                                            <input type="hidden" name="penghasilan_wali" value="${v(detail, 'penghasilan_wali')}">
                                            <input type="hidden" name="no_telp_wali" value="${v(detail, 'no_telp_wali')}">
                                            <input type="hidden" name="alamat_wali" value="${v(detail, 'alamat_wali')}">
                                        `;
                document.getElementById('hiddenFieldsOrangTua').innerHTML = '';
            }
            html += `</div>`;
            container.innerHTML = html;
        }

        // ========== ISI FORMULIR DAFTAR ULANG DENGAN DATA AWAL ==========
        async function populateFormulirDaftarUlang() {
            const data = await loadDataAwal();
            if (!data || !data.data) return;

            const header = data.data;
            const detail = data.detail;
            const tipe = header.tipe_wali || 'orang_tua';
            document.getElementById('tipe_wali').value = tipe;

            // Data siswa
            document.getElementById('no_pendaftaran').value = header.no_pendaftaran || '';
            document.getElementById('nama_lengkap').value = header.nama_lengkap || '';
            document.getElementById('tempat_lahir').value = header.tempat_lahir || '';
            document.getElementById('tanggal_lahir').value = header.tanggal_lahir || '';
            document.getElementById('jenis_kelamin').value = header.jenis_kelamin || '';
            document.getElementById('asal_tk').value = header.asal_tk || '';

            // Hidden data siswa
            document.getElementById('nik_siswa').value = header.nik || '';
            document.getElementById('agama_siswa').value = header.agama || '';
            document.getElementById('anak_ke').value = header.anak_ke || '';
            document.getElementById('jumlah_saudara').value = header.jumlah_saudara || '';
            document.getElementById('is_bukan_pindahan').value = header.is_bukan_pindahan ? '1' : '0';
            document.getElementById('asal_sekolah_pindahan').value = header.asal_sekolah || '';

            // Render form dinamis sesuai tipe
            renderDynamicForm(tipe, header, detail);
        }

        document.getElementById('btnLanjutKeUpload').addEventListener('click', function () {
            // Pindah ke tab persyaratan
            document.getElementById('tabPersyaratanBtn').click();
        });

        // ========== RENDER TAB PERSYARATAN (UPLOAD) ==========
        async function loadDaftarUlangSection() {
            const container = document.getElementById('persyaratan-content');
            try {
                const cekRes = await fetch('/api/daftar-ulang/cek', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const cek = await cekRes.json();
                if (!cek.eligible) {
                    container.innerHTML = `<div class="empty-state-box"><div class="message-content"><i class="fa-solid fa-circle-info"></i><strong>Informasi</strong><hr class="message-divider"><p class="sub-message">${cek.message}</p></div></div>`;
                    return;
                }
                if (cek.sudah_mengirim) {
                    if (cek.status === 'menunggu') {
                        document.querySelector('.toggle-container').style.display = 'none';
                        document.getElementById('tabFormulir').style.display = 'none';
                        document.getElementById('tabPersyaratan').style.display = 'block';
                        container.innerHTML = `<div class="success-inner-box mt-30"><div class="success-icon-wrapper"><i class="fa-regular fa-clock"></i></div><h2 class="success-heading">Daftar Ulang Dalam Proses</h2><div class="success-description"><p>Berkas sedang diverifikasi.</p><p>Panitia akan memproses dalam 1-3 hari kerja.</p></div></div>`;
                        return;
                    } else if (cek.status === 'diterima') {
                        document.querySelector('.toggle-container').style.display = 'none';
                        document.getElementById('tabFormulir').style.display = 'none';
                        document.getElementById('tabPersyaratan').style.display = 'block';
                        container.innerHTML = `<div class="success-inner-box mt-30"><div class="success-icon-wrapper"><i class="fa-regular fa-check-circle"></i></div><h2 class="success-heading">Daftar Ulang Diterima</h2><div class="success-description"><p>Selamat bergabung!</p></div></div>`;
                        return;
                    } else if (cek.status === 'ditolak') {
                        document.querySelector('.toggle-container').style.display = 'flex';
                        const uploadHtml = await tampilkanFormDaftarUlang();
                        container.innerHTML = `<div class="mt-30">${uploadHtml}</div>`;
                        attachFormListener();
                        initUploadBoxes();
                        return;
                    }
                }
                document.querySelector('.toggle-container').style.display = 'flex';
                const uploadHtml = await tampilkanFormDaftarUlang();
                container.innerHTML = `<div class="mt-30">${uploadHtml}</div>`;
                attachFormListener();
                initUploadBoxes();
            } catch (e) {
                console.error(e);
                container.innerHTML = '<p>Gagal memuat data.</p>';
            }
        }

        async function tampilkanFormDaftarUlang() {
            const templateLinks = await getTemplateLinks();
            return `
                                <div class="requirement-section">
                                    <h4 class="sub-label">Unduh Surat Pernyataan</h4>
                                    <div class="download-grid">${templateLinks}</div>
                                </div>
                                <div class="requirement-section mt-30">
                                    <h4 class="sub-label">Upload surat pernyataan dan dokumen persyaratan</h4>
                                    <form id="formDaftarUlang" enctype="multipart/form-data">
                                        <div class="upload-list">${generateUploadItems()}</div>
                                        <!-- Hidden fields untuk data isian dari tab formulir -->
                                        <div id="hiddenDataForm"></div>
                                        <div class="form-actions space-between mt-30">
                                            <button type="button" class="btn-outline" onclick="loadDaftarUlangSection()">KEMBALI</button>
                                            <button type="submit" class="btn-solid">KIRIM</button>
                                        </div>
                                    </form>
                                </div>
                            `;
        }

        function initUploadBoxes() {
            const fileInputs = document.querySelectorAll('#formDaftarUlang input[type="file"]');
            fileInputs.forEach(input => {
                // Hapus listener lama jika ada (hindari duplikasi)
                input.removeEventListener('change', handleFileChange);
                input.addEventListener('change', handleFileChange);
            });
        }

        function handleFileChange(e) {
            const input = e.target;
            const uploadItem = input.closest('.upload-item');
            if (!uploadItem) return;

            const uploadBox = uploadItem.querySelector('.upload-box');
            if (!uploadBox) return;

            // Cari atau buat span .file-name
            let fileNameSpan = uploadBox.querySelector('.file-name');
            if (!fileNameSpan) {
                fileNameSpan = document.createElement('span');
                fileNameSpan.className = 'file-name';
                fileNameSpan.style.marginLeft = '5px';
                fileNameSpan.style.fontSize = '11px';
                fileNameSpan.style.color = '#2d6a4f';
                uploadBox.appendChild(fileNameSpan);
            }

            // Cari ikon dan teks "Upload" / "Ganti"
            const icon = uploadBox.querySelector('i');
            let textSpan = uploadBox.querySelector('span:not(.file-name)');
            if (!textSpan) {
                // fallback: cari span yang teksnya mengandung 'Upload' atau 'Ganti'
                const allSpans = uploadBox.querySelectorAll('span');
                for (let span of allSpans) {
                    if (span.textContent.includes('Upload') || span.textContent.includes('Ganti')) {
                        textSpan = span;
                        break;
                    }
                }
            }

            if (input.files.length > 0) {
                const fileName = input.files[0].name;
                fileNameSpan.textContent = fileName.length > 25 ? fileName.substring(0, 22) + '...' : fileName;
                if (icon) icon.className = 'fa-solid fa-check-circle';
                if (textSpan) textSpan.textContent = ' Ganti';
            } else {
                fileNameSpan.textContent = '';
                if (icon) icon.className = 'fa-solid fa-plus';
                if (textSpan) textSpan.textContent = 'Upload';
            }
        }

        function generateUploadItems() {
            return `
                    <div class="upload-item">
                        <div class="upload-info">
                            <i class="fa-solid fa-file-lines doc-icon"></i>
                            <div class="text-content">
                                <strong>Surat Pernyataan Orang Tua/Wali</strong>
                                <p>Scan surat pernyataan - PDF, JPG, PNG - Maks 2MB</p>
                            </div>
                            <span class="badge badge-required">Wajib</span>
                        </div>
                        <div class="upload-box" onclick="document.getElementById('surat_pernyataan').click();">
                            <i class="fa-solid fa-plus"></i>
                            <span>Upload</span>
                            <span class="file-name" style="margin-left: 5px; font-size: 11px; color: #2d6a4f;"></span>
                            <input type="file" name="surat_pernyataan" id="surat_pernyataan" accept=".pdf,.jpg,.png" style="display:none;"
                                required>
                        </div>
                    </div>
                    <div class="upload-item">
                        <div class="upload-info"><i class="fa-solid fa-file-lines doc-icon"></i>
                            <div class="text-content"><strong>Surat Pernyataan Peserta Didik</strong>
                                <p>Scan surat pernyataan peserta didik - PDF, JPG, PNG - Maks 2MB</p>
                            </div><span class="badge badge-required">Wajib</span>
                        </div>
                        <div class="upload-box" onclick="document.getElementById('surat_pakta_integritas').click();"><i
                                class="fa-solid fa-plus"></i><span>Upload</span><input type="file" name="surat_pakta_integritas"
                                id="surat_pakta_integritas" accept=".pdf,.jpg,.png" style="display:none;" required></div>
                    </div>
                    <div class="upload-item">
                        <div class="upload-info"><i class="fa-solid fa-file-lines doc-icon"></i>
                            <div class="text-content"><strong>KTP Orang Tua/Wali</strong>
                                <p>Scan KTP - PDF, JPG, PNG - Maks 2MB</p>
                            </div><span class="badge badge-required">Wajib</span>
                        </div>
                        <div class="upload-box" onclick="document.getElementById('ktp_orang_tua').click();"><i
                                class="fa-solid fa-plus"></i><span>Upload</span><input type="file" name="ktp_orang_tua" id="ktp_orang_tua"
                                accept=".pdf,.jpg,.png" style="display:none;" required></div>
                    </div>
                    <div class="upload-item">
                        <div class="upload-info"><i class="fa-solid fa-file-lines doc-icon"></i>
                            <div class="text-content"><strong>Akta Kelahiran Peserta Didik</strong>
                                <p>Scan akta kelahiran - PDF, JPG, PNG - Maks 2MB</p>
                            </div><span class="badge badge-required">Wajib</span>
                        </div>
                        <div class="upload-box" onclick="document.getElementById('akte_kelahiran').click();"><i
                                class="fa-solid fa-plus"></i><span>Upload</span><input type="file" name="akte_kelahiran" id="akte_kelahiran"
                                accept=".pdf,.jpg,.png" style="display:none;" required></div>
                    </div>
                    <div class="upload-item">
                        <div class="upload-info"><i class="fa-solid fa-file-lines doc-icon"></i>
                            <div class="text-content"><strong>Kartu Keluarga</strong>
                                <p>Scan Kartu Keluarga - PDF, JPG, PNG - Maks 2MB</p>
                            </div><span class="badge badge-required">Wajib</span>
                        </div>
                        <div class="upload-box" onclick="document.getElementById('kartu_keluarga').click();"><i
                                class="fa-solid fa-plus"></i><span>Upload</span><input type="file" name="kartu_keluarga" id="kartu_keluarga"
                                accept=".pdf,.jpg,.png" style="display:none;" required></div>
                    </div>
                    <div class="upload-item">
                        <div class="upload-info"><i class="fa-solid fa-file-lines doc-icon"></i>
                            <div class="text-content"><strong>Ijazah RA/TK/PAUD</strong>
                                <p>Scan ijazah - PDF, JPG, PNG - Maks 2MB</p>
                            </div><span class="badge badge-optional">Opsional</span>
                        </div>
                        <div class="upload-box" onclick="document.getElementById('ijazah_tk').click();"><i
                                class="fa-solid fa-plus"></i><span>Upload</span><input type="file" name="ijazah_tk" id="ijazah_tk"
                                accept=".pdf,.jpg,.png" style="display:none;"></div>
                    </div>
                    <div class="upload-item">
                        <div class="upload-info"><i class="fa-solid fa-file-lines doc-icon"></i>
                            <div class="text-content"><strong>Scan NISN (jika punya)</strong>
                                <p>Scan NISN - PDF, JPG, PNG - Maks 2MB</p>
                            </div><span class="badge badge-optional">Opsional</span>
                        </div>
                        <div class="upload-box" onclick="document.getElementById('nisn_file').click();"><i
                                class="fa-solid fa-plus"></i><span>Upload</span><input type="file" name="nisn_file" id="nisn_file"
                                accept=".pdf,.jpg,.png" style="display:none;"></div>
                    </div>
                `;
        }

        function attachFormListener() {
            const form = document.getElementById('formDaftarUlang');
            if (!form) return;
            form.onsubmit = async (e) => {
                e.preventDefault();

                // Ambil semua data dari tab formulir
                const formData = new FormData(form);

                // Data siswa
                formData.append('nama_lengkap', document.getElementById('nama_lengkap').value);
                formData.append('tempat_lahir', document.getElementById('tempat_lahir').value);
                formData.append('tanggal_lahir', document.getElementById('tanggal_lahir').value);
                formData.append('jenis_kelamin', document.getElementById('jenis_kelamin').value);
                formData.append('tipe_wali', document.getElementById('tipe_wali').value);
                formData.append('asal_tk', document.getElementById('asal_tk').value);

                // Data orang tua / wali (sesuai tipe)
                const tipe = document.getElementById('tipe_wali').value;
                if (tipe === 'orang_tua') {
                    formData.append('nama_ayah', document.querySelector('[name="nama_ayah"]').value);
                    formData.append('pendidikan_ayah', document.querySelector('[name="pendidikan_ayah"]').value);
                    formData.append('pekerjaan_ayah', document.querySelector('[name="pekerjaan_ayah"]').value);
                    formData.append('alamat_ktp', document.querySelector('[name="alamat_ktp"]').value);
                    formData.append('no_hp', document.querySelector('[name="no_hp"]').value);
                    formData.append('nama_ibu', document.querySelector('[name="nama_ibu"]').value);
                    formData.append('pendidikan_ibu', document.querySelector('[name="pendidikan_ibu"]').value);
                    formData.append('pekerjaan_ibu', document.querySelector('[name="pekerjaan_ibu"]').value);
                    formData.append('alamat_domisili', document.querySelector('[name="alamat_domisili"]').value);
                    formData.append('narahubung', document.querySelector('[name="narahubung"]').value);
                } else {
                    formData.append('nama_wali', document.querySelector('[name="nama_wali"]').value);
                    formData.append('pendidikan_wali', document.querySelector('[name="pendidikan_wali"]').value);
                    formData.append('pekerjaan_wali', document.querySelector('[name="pekerjaan_wali"]').value);
                    formData.append('alamat_ktp', document.querySelector('[name="alamat_ktp"]').value);
                    formData.append('no_hp', document.querySelector('[name="no_hp"]').value);
                    formData.append('alamat_domisili', document.querySelector('[name="alamat_domisili"]').value);
                    formData.append('narahubung', document.querySelector('[name="narahubung"]').value);
                }

                // Validasi file wajib (sama seperti sebelumnya)
                const requiredFiles = ['surat_pernyataan', 'surat_pakta_integritas', 'ktp_orang_tua', 'akte_kelahiran', 'kartu_keluarga'];
                let missing = false;
                requiredFiles.forEach(field => {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (input && input.files.length === 0) {
                        alert(`Harap upload ${field.replace(/_/g, ' ')}`);
                        missing = true;
                    }
                });
                if (missing) return;

                const res = await fetch('/api/daftar-ulang', {
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + token },
                    body: formData
                });
                if (res.ok) {
                    alert('Berkas dan data terkirim. Menunggu verifikasi.');
                    loadDaftarUlangSection(); // refresh tampilan
                } else {
                    const err = await res.json();
                    alert('Gagal: ' + (err.message || JSON.stringify(err)));
                }
            };
        }

        // ========== INIT ==========
        document.addEventListener('DOMContentLoaded', async function () {
            await populateFormulirDaftarUlang();

            const btnLanjut = document.getElementById('btnLanjutKeUpload');
            if (btnLanjut) {
                btnLanjut.addEventListener('click', function () {
                    document.getElementById('tabPersyaratanBtn').click();
                });
            }

            const tabFormBtn = document.getElementById('tabFormBtn');
            const tabPersyaratanBtn = document.getElementById('tabPersyaratanBtn');
            const tabFormulir = document.getElementById('tabFormulir');
            const tabPersyaratan = document.getElementById('tabPersyaratan');

            function setActiveTab(activeTab) {
                if (activeTab === 'formulir') {
                    tabFormulir.style.display = 'block';
                    tabPersyaratan.style.display = 'none';
                    tabFormBtn.classList.add('active');
                    tabPersyaratanBtn.classList.remove('active');
                } else {
                    tabFormulir.style.display = 'none';
                    tabPersyaratan.style.display = 'block';
                    tabFormBtn.classList.remove('active');
                    tabPersyaratanBtn.classList.add('active');
                    if (!sudahLoadPersyaratan) {
                        sudahLoadPersyaratan = true;
                        loadDaftarUlangSection();
                    }
                }
            }

            tabFormBtn.addEventListener('click', () => setActiveTab('formulir'));
            tabPersyaratanBtn.addEventListener('click', () => setActiveTab('persyaratan'));
            
            // Check status sebelum menentukan tab aktif default
            try {
                const cekRes = await fetch('/api/daftar-ulang/cek', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const cek = await cekRes.json();
                if (cek.sudah_mengirim && (cek.status === 'menunggu' || cek.status === 'diterima')) {
                    document.querySelector('.toggle-container').style.display = 'none';
                    setActiveTab('persyaratan');
                } else {
                    setActiveTab('formulir');
                }
            } catch (e) {
                setActiveTab('formulir');
            }
        });
    </script>
@endpush