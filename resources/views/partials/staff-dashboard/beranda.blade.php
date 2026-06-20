<div id="beranda-staff" class="section">
    <h2>Beranda & Ringkasan Data PPDB</h2>
    
    <!-- Cards Stats Container -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon bg-green"><i class="fa-solid fa-users"></i></div>
            <div class="stat-info">
                <h3>Total Pendaftar</h3>
                <h2 id="stats-total-pendaftar">0</h2>
                <p>Akun teregistrasi</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-yellow"><i class="fa-solid fa-file-invoice"></i></div>
            <div class="stat-info">
                <h3>Formulir Pendaftaran</h3>
                <h2 id="stats-formulir-diterima">0 <span class="stat-sub">/ 0</span></h2>
                <p><span id="stats-formulir-menunggu" class="badge-pending">0</span> menunggu verifikasi</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-blue"><i class="fa-solid fa-receipt"></i></div>
            <div class="stat-info">
                <h3>Pembayaran</h3>
                <h2 id="stats-pembayaran-diterima">0 <span class="stat-sub">/ 0</span></h2>
                <p><span id="stats-pembayaran-menunggu" class="badge-pending">0</span> menunggu verifikasi</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-purple"><i class="fa-solid fa-user-check"></i></div>
            <div class="stat-info">
                <h3>Daftar Ulang</h3>
                <h2 id="stats-daftar-ulang-diterima">0 <span class="stat-sub">/ 0</span></h2>
                <p><span id="stats-daftar-ulang-menunggu" class="badge-pending">0</span> menunggu verifikasi</p>
            </div>
        </div>
    </div>

    <!-- Active Gelombang Section -->
    <div id="gelombang-aktif-panel" class="dashboard-panel" style="display:none; margin-top:30px;">
        <div class="panel-header" style="border-bottom: 1px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-calendar-check" style="font-size: 20px; color: #1a4d2e;"></i>
            <h3 style="margin: 0; border: none; padding: 0; display: inline-block;">Gelombang Pendaftaran Aktif</h3>
        </div>
        <div class="panel-body grid-2">
            <div>
                <p><strong>Gelombang:</strong> <span id="stats-gel-nomor">-</span></p>
                <p><strong>Tahun Periode:</strong> <span id="stats-gel-tahun">-</span></p>
            </div>
            <div>
                <p><strong>Kuota Terisi:</strong> <span id="stats-gel-kuota-terisi">-</span></p>
                <p><strong>Sisa Kuota:</strong> <span id="stats-gel-sisa-kuota">-</span></p>
            </div>
        </div>
    </div>


</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}
.stat-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    display: flex;
    align-items: center;
    gap: 20px;
    border: 1px solid #e2e8f0;
}
.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
}
.bg-green { background: #1a4d2e; }
.bg-yellow { background: #e0a96d; }
.bg-blue { background: #3b82f6; }
.bg-purple { background: #8b5cf6; }

.stat-info h3 {
    margin: 0;
    font-size: 13px;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 700;
    border: none !important;
    padding: 0 !important;
}
.stat-info h2 {
    margin: 5px 0;
    font-size: 24px;
    color: #1e293b;
    font-weight: 800;
}
.stat-sub {
    font-size: 14px;
    color: #94a3b8;
    font-weight: 500;
}
.stat-info p {
    margin: 0;
    font-size: 12px;
    color: #64748b;
}
.badge-pending {
    font-weight: 700;
    color: #d97706;
}
.dashboard-panel {
    background: white;
    padding: 25px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 15px rgba(0,0,0,0.02);
}
.dashboard-panel p {
    font-size: 14px;
    margin: 8px 0;
    color: #334155;
}

/* Custom form inputs style matching general theme */
.form-label {
    display: block;
    margin-bottom: 6px;
    font-size: 14px;
    color: #334155;
}
.form-input {
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 8px 12px;
    font-size: 14px;
    transition: border-color 0.2s, box-shadow 0.2s;
    background: white;
}
.form-input:focus {
    outline: none;
    border-color: #1a4d2e;
    box-shadow: 0 0 0 3px rgba(26, 77, 46, 0.15);
}
.btn-primary:hover {
    background: #123520 !important;
}
</style>

@push('staff-scripts')
<script>
    async function loadStaffStats() {
        try {
            const res = await fetch('/api/staff/stats', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            if (!res.ok) return;
            const data = await res.json();
            
            document.getElementById('stats-total-pendaftar').innerText = data.total_pendaftar || 0;
            
            // Formulir
            const totalFormulir = (data.formulir_menunggu || 0) + (data.formulir_diterima || 0);
            document.getElementById('stats-formulir-diterima').innerHTML = `${data.formulir_diterima || 0} <span class="stat-sub">/ ${totalFormulir}</span>`;
            document.getElementById('stats-formulir-menunggu').innerText = data.formulir_menunggu || 0;

            // Pembayaran
            const totalPembayaran = (data.pembayaran_menunggu || 0) + (data.pembayaran_diterima || 0);
            document.getElementById('stats-pembayaran-diterima').innerHTML = `${data.pembayaran_diterima || 0} <span class="stat-sub">/ ${totalPembayaran}</span>`;
            document.getElementById('stats-pembayaran-menunggu').innerText = data.pembayaran_menunggu || 0;

            // Daftar Ulang
            const totalDaftarUlang = (data.daftar_ulang_menunggu || 0) + (data.daftar_ulang_diterima || 0);
            document.getElementById('stats-daftar-ulang-diterima').innerHTML = `${data.daftar_ulang_diterima || 0} <span class="stat-sub">/ ${totalDaftarUlang}</span>`;
            document.getElementById('stats-daftar-ulang-menunggu').innerText = data.daftar_ulang_menunggu || 0;

            // Gelombang Aktif
            const gelPanel = document.getElementById('gelombang-aktif-panel');
            if (data.gelombang_aktif) {
                gelPanel.style.display = 'block';
                document.getElementById('stats-gel-nomor').innerText = 'Gelombang ' + data.gelombang_aktif.nomor;
                document.getElementById('stats-gel-tahun').innerText = data.gelombang_aktif.tahun;
                const terisi = data.gelombang_aktif.kuota - data.gelombang_aktif.sisa_kuota;
                document.getElementById('stats-gel-kuota-terisi').innerText = `${terisi} dari ${data.gelombang_aktif.kuota} pendaftar`;
                document.getElementById('stats-gel-sisa-kuota').innerText = `${data.gelombang_aktif.sisa_kuota} pendaftar`;
            } else {
                gelPanel.style.display = 'none';
            }
        } catch (e) {
            console.error('Gagal mengambil data statistik', e);
        }
    }

    // Panggil saat load pertama kali
    document.addEventListener('DOMContentLoaded', () => {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        const allowed = ['panitia', 'bendahara', 'kepala_sekolah'];
        if (allowed.includes(user.role)) {
            loadStaffStats();
        }
    });
</script>
@endpush