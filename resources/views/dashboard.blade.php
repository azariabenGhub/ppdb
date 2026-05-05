<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PPDB MI Ziyadatul Ihsan</title>
    <link rel="stylesheet" href="{{ asset('storage/css/style.css') }}">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/fi-regular-rounded.css'>
    <style>
        /* ===== DASHBOARD SECTION STYLES ===== */
        .section-wrapper {
            background-color: white;
            border-radius: 15px;
            padding: 35px;
            border: 1px solid #eee;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        }

        .section-wrapper h2 {
            font-size: 1.3rem;
            color: #1a4d2e;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
        }

        /* ===== SIDEBAR ACTIVE STATE ===== */
        .sidebar-menu li.active {
            background-color: #f0f7f2;
            color: #1a4d2e;
        }

        .sidebar-menu li.active a {
            color: #1a4d2e;
            font-weight: 600;
        }

        /* ===== BERANDA - TIMELINE ===== */
        .timeline-card {
            background: white;
            padding: 35px;
            border-radius: 15px;
            border: 1px solid #eee;
        }

        .timeline-card h3 {
            font-size: 1.1rem;
            color: #1a4d2e;
            margin-bottom: 5px;
        }

        /* Grid 3 kolom: konten-kiri | center-axis | konten-kanan */
        .timeline-wrapper {
            display: grid;
            grid-template-columns: 1fr 36px 1fr;
            grid-template-rows: auto;
            column-gap: 20px;
            margin-top: 30px;
            position: relative;
        }

        /* Garis vertikal di kolom tengah, full height */
        .vertical-line {
            grid-column: 2;
            grid-row: 1 / span 999;
            width: 2px;
            background-color: #1a4d2e;
            justify-self: center;
            border-radius: 2px;
        }

        /* Setiap step row: 3 sel — kiri, circle, kanan */
        .step-row {
            display: contents;
        }

        .step-left {
            grid-column: 1;
            display: flex;
            justify-content: flex-end;
            align-items: flex-start;
            padding-bottom: 36px;
        }

        .step-center {
            grid-column: 2;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 0;
            z-index: 2;
        }

        .step-right {
            grid-column: 3;
            display: flex;
            justify-content: flex-start;
            align-items: flex-start;
            padding-bottom: 36px;
        }

        .step-circle {
            width: 36px;
            height: 36px;
            background-color: #1a4d2e;
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            flex-shrink: 0;
            font-size: 1rem;
            position: relative;
            z-index: 3;
        }

        .step-content {
            background-color: #f4f7f6;
            border-radius: 10px;
            padding: 14px 18px;
            max-width: 100%;
        }

        /* konten kiri rata kanan */
        .step-left .step-content {
            text-align: right;
        }

        .step-left .step-date {
            justify-content: flex-end;
        }

        .step-content h4 {
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 6px;
            color: #222;
        }

        .step-content p {
            font-size: 0.82rem;
            color: #555;
            line-height: 1.5;
        }

        .step-date {
            font-size: 0.75rem;
            color: #1a4d2e;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 6px;
        }

        /* ===== FOOTER CARD ===== */
        .footer-card {
            background-color: #1a4d2e;
            color: white;
            border-radius: 15px;
            padding: 35px;
            display: flex;
            gap: 60px;
            flex-wrap: wrap;
        }

        .footer-card .footer-col h5 {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.7;
            margin-bottom: 8px;
        }

        .footer-card .footer-col h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .footer-card .footer-col p {
            font-size: 0.85rem;
            opacity: 0.85;
            line-height: 1.8;
        }

        /* ===== FORM SECTION STYLES ===== */
        .form-step fieldset {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
        }

        .form-step legend {
            font-weight: 600;
            color: #1a4d2e;
            padding: 0 10px;
            font-size: 0.95rem;
        }

        .form-step label {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
            color: #444;
            margin-bottom: 5px;
        }

        .form-step input[type="text"],
        .form-step input[type="date"],
        .form-step input[type="number"],
        .form-step input[type="email"],
        .form-step textarea,
        .form-step select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.9rem;
            font-family: "Inter", sans-serif;
            background-color: #f9f9f9;
            margin-bottom: 16px;
            color: #333;
        }

        .form-step input:focus,
        .form-step textarea:focus,
        .form-step select:focus {
            outline: none;
            border-color: #1a4d2e;
            background-color: white;
        }

        .form-step h3 {
            font-size: 1rem;
            color: #1a4d2e;
            margin: 15px 0 10px;
        }

        .form-step hr {
            border: none;
            border-top: 1px solid #eee;
            margin: 20px 0;
        }

        .step-nav {
            display: flex;
            gap: 12px;
            margin-top: 10px;
        }

        .btn-secondary {
            background-color: white;
            color: #1a4d2e;
            border: 1px solid #1a4d2e;
            border-radius: 6px;
            padding: 10px 24px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background-color: #f0f7f2;
        }

        .btn-submit {
            background-color: #1a4d2e;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 24px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-submit:hover {
            background-color: #153d24;
        }

        /* ===== PEMBAYARAN STYLES ===== */
        .payment-methods-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 25px;
        }

        .payment-method-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 18px;
            background: #f9f9f9;
        }

        .payment-method-card strong {
            display: block;
            font-size: 0.95rem;
            color: #1a4d2e;
            margin-bottom: 6px;
        }

        .payment-method-card small {
            font-size: 0.8rem;
            color: #888;
        }

        .upload-section {
            background: #f4f7f6;
            border-radius: 10px;
            padding: 25px;
            margin-top: 20px;
        }

        .upload-section h3 {
            font-size: 1rem;
            color: #1a4d2e;
            margin-bottom: 15px;
        }

        .upload-section select,
        .upload-section input[type="file"] {
            display: block;
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: white;
            font-family: "Inter", sans-serif;
            font-size: 0.9rem;
            margin-bottom: 14px;
        }

        .riwayat-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
            margin-top: 20px;
        }

        .riwayat-table th {
            background-color: #1a4d2e;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
        }

        .riwayat-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            color: #444;
        }

        .riwayat-table tr:hover td {
            background-color: #f4f7f6;
        }

        /* ===== JADWAL TES STYLES ===== */
        .jadwal-card {
            border: 1px solid #d4e8db;
            background: #f0f7f2;
            border-radius: 10px;
            padding: 25px;
        }

        .jadwal-card p {
            font-size: 1rem;
            color: #333;
        }

        .jadwal-card strong {
            color: #1a4d2e;
        }

        /* ===== PENGUMUMAN STYLES ===== */
        .pengumuman-lulus {
            background: #e8f5e9;
            border: 1px solid #a5d6a7;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .pengumuman-tidak-lulus {
            background: #fde8e8;
            border: 1px solid #f5c6c6;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .nilai-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-top: 15px;
        }

        .nilai-item {
            background: white;
            border-radius: 8px;
            padding: 14px;
            border: 1px solid #eee;
        }

        .nilai-item label {
            font-size: 0.75rem;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .nilai-item span {
            display: block;
            font-size: 1.4rem;
            font-weight: 700;
            color: #1a4d2e;
            margin-top: 4px;
        }

        /* ===== STATUS PENDAFTARAN ===== */
        .status-steps {
            display: flex;
            flex-direction: column;
            gap: 0;
            margin-top: 20px;
        }

        .status-step {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            padding-bottom: 25px;
            position: relative;
        }

        .status-step:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 14px;
            top: 30px;
            bottom: 0;
            width: 2px;
            background: #e0e0e0;
        }

        .status-dot {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .status-dot.done {
            background: #1a4d2e;
            color: white;
        }

        .status-dot.current {
            background: #f59e0b;
            color: white;
        }

        .status-dot.pending {
            background: #e0e0e0;
            color: #888;
        }

        .status-step-info h4 {
            font-size: 0.95rem;
            margin-bottom: 4px;
        }

        .status-step-info p {
            font-size: 0.82rem;
            color: #888;
        }

        /* ===== DAFTAR ULANG ===== */
        .daftar-ulang-info {
            background: #f4f7f6;
            border-radius: 10px;
            padding: 25px;
            border: 1px dashed #1a4d2e;
        }

        .daftar-ulang-info p {
            font-size: 0.9rem;
            color: #555;
            line-height: 1.7;
        }

        /* ===== LOGOUT BUTTON ===== */
        .btn-logout {
            background: transparent;
            border: 1px solid #dc2626;
            color: #dc2626;
            border-radius: 6px;
            padding: 8px 18px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-logout:hover {
            background: #dc2626;
            color: white;
        }

        .jadwal-tes-header {
            margin-bottom: 20px;
        }

        .jadwal-tes-header h3 {
            font-size: 1.1rem;
            color: #1a4d2e;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .jadwal-tes-header p {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 20px;
        }

        .jadwal-tes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .jadwal-tes-card {
            border: 1px solid #d4e8db;
            border-radius: 12px;
            overflow: hidden;
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .jadwal-tes-card-header {
            background-color: #1a4d2e;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .jadwal-tes-card-header .tes-icon {
            width: 42px;
            height: 42px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .jadwal-tes-card-header .tes-icon i {
            color: white;
            font-size: 1.2rem;
        }

        .jadwal-tes-card-header .tes-title h4 {
            color: white;
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .jadwal-tes-card-header .tes-title p {
            color: rgba(255, 255, 255, 0.75);
            font-size: 0.8rem;
            margin: 0;
        }

        .jadwal-tes-card-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .jadwal-tes-detail-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .jadwal-tes-detail-item i {
            color: #1a4d2e;
            font-size: 1rem;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .jadwal-tes-detail-item .detail-text label {
            display: block;
            font-size: 0.75rem;
            color: #888;
            margin-bottom: 2px;
        }

        .jadwal-tes-detail-item .detail-text strong {
            display: block;
            font-size: 0.9rem;
            color: #222;
            font-weight: 600;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {

            /* Timeline mobile: semua jadi 1 kolom kiri */
            .timeline-wrapper {
                grid-template-columns: 36px 1fr;
                column-gap: 14px;
            }

            .vertical-line {
                grid-column: 1;
                grid-row: 1 / span 999;
                justify-self: center;
            }

            .step-left,
            .step-right {
                grid-column: 2;
                justify-content: flex-start;
                text-align: left;
                padding-bottom: 28px;
            }

            .step-left:empty,
            .step-right:empty {
                display: none;
            }

            .step-center {
                grid-column: 1;
            }

            .step-left .step-content {
                text-align: left;
            }

            .step-left .step-date {
                justify-content: flex-start;
            }

            .step-content {
                max-width: 100%;
            }

            .footer-card {
                flex-direction: column;
                gap: 25px;
            }

            .nilai-grid {
                grid-template-columns: 1fr;
            }

            .section-wrapper {
                padding: 20px;
            }

            .timeline-card {
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            .step-circle {
                width: 30px;
                height: 30px;
                font-size: 0.85rem;
            }

            .timeline-wrapper {
                grid-template-columns: 30px 1fr;
                column-gap: 12px;
            }
        }
    </style>
</head>

<body>

    <!-- ===== HEADER ===== -->
    <header class="main-header">
        <div class="header-brand">
            <img src="{{ asset('storage/assets/logo-mizi.png') }}" alt="Logo" class="logo-mizi">
            <div>
                <strong style="font-size:1rem; color:#1a4d2e; display:block;">MI Ziyadatul Ihsan</strong>
                <span style="font-size:0.75rem; color:#888;">PPDB 2025/2026</span>
            </div>
        </div>
        <div class="header-profile">
            <span id="headerUserName">Nama Calon Siswa</span>
            <div
                style="width:36px;height:36px;border-radius:50%;background:#1a4d2e;display:flex;align-items:center;justify-content:center;">
                <i class="fi fi-rr-user" style="color:white;font-size:1rem;"></i>
            </div>
            <button class="btn-logout" id="logoutButton">
                <i class="fi fi-rr-sign-out"></i> Keluar
            </button>
        </div>
    </header>

    <!-- ===== MAIN WRAPPER ===== -->
    <div class="dashboard-wrapper">

        <!-- ===== SIDEBAR ===== -->
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li class="active" data-section="beranda" href="#" onclick="navigate(event,'beranda')">
                    <a>
                        <i class="fi fi-rr-home"></i> Beranda
                    </a>
                </li>
                <li data-section="formulir" href="#" onclick="navigate(event,'formulir')">
                    <a>
                        <i class="fi fi-rr-document"></i> Formulir Pendaftaran
                    </a>
                </li>
                <li data-section="seleksi" href="#" onclick="navigate(event,'seleksi')">
                    <a>
                        <i class="fi fi-rr-calendar"></i> Jadwal Tes
                    </a>
                </li>
                <li data-section="pengumuman" href="#" onclick="navigate(event,'pengumuman')">
                    <a>
                        <i class="fi fi-rr-megaphone"></i> Pengumuman
                    </a>
                </li>
                <li data-section="daftar-ulang" href="#" onclick="navigate(event,'daftar-ulang')">
                    <a>
                        <i class="fi fi-rr-refresh"></i> Daftar Ulang
                    </a>
                </li>
                <li data-section="pembayaran" href="#" onclick="navigate(event,'pembayaran')">
                    <a>
                        <i class="fi fi-rr-credit-card"></i> Pembayaran
                    </a>
                </li>
                <li data-section="status" href="#" onclick="navigate(event,'status')">
                    <a>
                        <i class="fi fi-rr-info"></i> Status Pendaftaran
                    </a>
                </li>
            </ul>
        </aside>

        <!-- ===== CONTENT AREA ===== -->
        <main class="content-area">

            <!-- ===== BERANDA ===== -->
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
                        <h3 style="text-align:center; font-size:1.1rem; color:#1a4d2e; font-weight:700;">Alur
                            Pendaftaran
                        </h3>

                        <div class="timeline-wrapper">
                            <!-- Garis vertikal di kolom tengah -->
                            <div class="vertical-line"></div>

                            <!-- Step 1: konten kiri, circle tengah, kanan kosong -->
                            <div class="step-left">
                                <div class="step-content">
                                    <h4>Cek berkas persyaratan</h4>
                                    <p>Calon peserta didik mempersiapkan berkas-berkas yang dibutuhkan.</p>
                                </div>
                            </div>
                            <div class="step-center">
                                <div class="step-circle">1</div>
                            </div>
                            <div class="step-right"></div>

                            <!-- Step 2: kiri kosong, circle tengah, konten kanan -->
                            <div class="step-left"></div>
                            <div class="step-center">
                                <div class="step-circle">2</div>
                            </div>
                            <div class="step-right">
                                <div class="step-content">
                                    <div class="step-date"><i class="fi fi-rr-calendar"></i> 5 Mei – 15 Mei</div>
                                    <h4>Pembayaran Formulir</h4>
                                    <p>Melakukan pembayaran formulir ke:<br>DKI Syariah: norek<br>A.N Ziyadatul Ihsan
                                    </p>
                                </div>
                            </div>

                            <!-- Step 3: konten kiri, circle tengah, kanan kosong -->
                            <div class="step-left">
                                <div class="step-content">
                                    <div class="step-date"><i class="fi fi-rr-calendar"></i> 5 Mei – 15 Mei</div>
                                    <h4>Daftar Online</h4>
                                    <p>Calon peserta didik mengisi biodata pada formulir pendaftaran dan tunggu
                                        verifikasi
                                        dari panitia.</p>
                                </div>
                            </div>
                            <div class="step-center">
                                <div class="step-circle">3</div>
                            </div>
                            <div class="step-right"></div>

                            <!-- Step 4: kiri kosong, circle tengah, konten kanan -->
                            <div class="step-left"></div>
                            <div class="step-center">
                                <div class="step-circle">4</div>
                            </div>
                            <div class="step-right">
                                <div class="step-content">
                                    <div class="step-date"><i class="fi fi-rr-calendar"></i> 5 Mei – 15 Mei</div>
                                    <h4>Cek Jadwal Tes</h4>
                                    <p>Setelah formulir diverifikasi, periksa jadwal tes akademik dan quran. Tes
                                        dilakukan
                                        secara offline di sekolah.</p>
                                </div>
                            </div>

                            <!-- Step 5: konten kiri, circle tengah, kanan kosong -->
                            <div class="step-left">
                                <div class="step-content">
                                    <div class="step-date"><i class="fi fi-rr-calendar"></i> 5 Mei – 15 Mei</div>
                                    <h4>Pengumuman Tes</h4>
                                    <p>Calon peserta didik memeriksa detail nilai dan hasil tes pada laman pengumuman.
                                    </p>
                                </div>
                            </div>
                            <div class="step-center">
                                <div class="step-circle">5</div>
                            </div>
                            <div class="step-right"></div>

                            <!-- Step 6: kiri kosong, circle tengah, konten kanan -->
                            <div class="step-left"></div>
                            <div class="step-center">
                                <div class="step-circle">6</div>
                            </div>
                            <div class="step-right">
                                <div class="step-content">
                                    <div class="step-date"><i class="fi fi-rr-calendar"></i> 5 Mei – 15 Mei</div>
                                    <h4>Daftar Ulang</h4>
                                    <p>Setelah dinyatakan lulus, unggah berkas-berkas persyaratan pada laman daftar
                                        ulang.
                                    </p>
                                </div>
                            </div>

                            <!-- Step 7: konten kiri, circle tengah, kanan kosong -->
                            <div class="step-left" style="padding-bottom:0;">
                                <div class="step-content">
                                    <div class="step-date"><i class="fi fi-rr-calendar"></i> 5 Mei – 15 Mei</div>
                                    <h4>Pembayaran Daftar Ulang</h4>
                                    <p>Lihat detail biaya daftar ulang dan lakukan pembayaran pada nomor rekening yang
                                        tertera.</p>
                                </div>
                            </div>
                            <div class="step-center" style="padding-bottom:0;">
                                <div class="step-circle">7</div>
                            </div>
                            <div class="step-right" style="padding-bottom:0;"></div>

                        </div>
                    </div>

                    <!-- Footer Info -->
                    <div class="footer-card">
                        <div class="footer-col">
                            <h5>Kontak Kami</h5>
                            <h3>MI Ziyadatul Ihsan</h3>
                            <p>
                                Ririn Asmarwati, S.Pd.I (0878 8751 8892)<br>
                                Hayatun Nufus, S.Pd. I (0878 7707 0284)<br>
                                Mamluatul Mukarromah (0822 1073 3866)
                            </p>
                        </div>
                        <div class="footer-col">
                            <h5>Alamat Kami</h5>
                            <h3>MI Ziyadatul Ihsan</h3>
                            <p>
                                Jl. Sadar No. 33 Rt.001/014 Jatinegara, Cipinang<br>
                                Muara, Kota Jakarta Timur, D.K.I. Jakarta
                            </p>
                        </div>
                    </div>
                </div>
            </div><!-- /beranda -->


            <!-- ===== FORMULIR PENDAFTARAN ===== -->
            <div id="formulir" class="section" style="display:none;">
                <div class="section-wrapper">
                    <h2>Formulir Pendaftaran</h2>
                    <div id="konten-formulir">
                        <p style="color:#888;">Memuat...</p>
                    </div>
                </div>
            </div>


            <!-- ===== JADWAL TES ===== -->
            <div id="seleksi" class="section" style="display:none;">
                <div class="section-wrapper">
                    <div class="jadwal-tes-header">
                        <h3>Jadwal Tes</h3>
                        <p>Informasi jadwal seleksi calon siswa baru</p>
                    </div>
                    <div id="info-seleksi">
                        <!-- konten dinamis akan diisi JavaScript -->
                        <div class="jadwal-card">
                            <p style="color:#888; font-size:0.9rem;">Memuat data jadwal tes...</p>
                        </div>
                    </div>
                </div>
            </div>


            <!-- ===== PENGUMUMAN ===== -->
            <div id="pengumuman" class="section" style="display:none;">
                <div class="section-wrapper">
                    <h2>Pengumuman Hasil Seleksi</h2>
                    <div id="info-pengumuman">
                        <p style="color:#888; font-size:0.9rem;">Memuat data pengumuman...</p>
                    </div>
                </div>
            </div>


            <!-- ===== DAFTAR ULANG ===== -->
            <div id="daftar-ulang" class="section" style="display:none;">
                <div class="section-wrapper">
                    <h2>Daftar Ulang</h2>
                    <div class="daftar-ulang-info">
                        <p>Setelah dinyatakan lulus seleksi, Anda dapat melakukan proses daftar ulang pada halaman ini.
                            Pastikan semua berkas persyaratan telah disiapkan sebelum mengunggah.</p>
                    </div>
                </div>
            </div>


            <!-- ===== PEMBAYARAN ===== -->
            <div id="pembayaran" class="section" style="display:none;">
                <div class="section-wrapper">
                    <h2>Pembayaran</h2>

                    <h3 style="font-size:1rem; color:#333; margin-bottom:15px; font-weight:600;">Metode Pembayaran</h3>
                    <div id="metode-pembayaran-list" class="payment-methods-grid">
                        <p style="color:#888; font-size:0.9rem;">Memuat...</p>
                    </div>

                    <div class="upload-section">
                        <h3>Upload Bukti Pembayaran</h3>
                        <form id="form-bukti" enctype="multipart/form-data">
                            <label
                                style="font-size:0.85rem; font-weight:500; color:#444; margin-bottom:6px; display:block;">Jenis
                                Pembayaran</label>
                            <select name="jenis_pembayaran" id="jenis_pembayaran">
                                <option value="formulir">Pembayaran Formulir</option>
                                <option value="masuk" disabled>Pembayaran Masuk (menunggu formulir diterima)</option>
                            </select>

                            <label
                                style="font-size:0.85rem; font-weight:500; color:#444; margin-bottom:6px; display:block;">Bukti
                                Pembayaran</label>
                            <input type="file" name="bukti_pembayaran" accept="image/*" required>

                            <button type="submit" class="btn-submit" style="margin-top:10px;">Kirim Bukti</button>
                        </form>
                    </div>

                    <div style="margin-top:30px;">
                        <h3 style="font-size:1rem; color:#333; margin-bottom:10px; font-weight:600;">Riwayat Pembayaran
                            & Status</h3>
                        <div id="riwayat-bukti" style="overflow-x:auto;">
                            <p style="color:#888; font-size:0.9rem;">Memuat...</p>
                        </div>
                    </div>
                </div>
            </div>


            <!-- ===== STATUS PENDAFTARAN ===== -->
            <div id="status" class="section" style="display:none;">
                <div class="section-wrapper">
                    <h2>Status Pendaftaran</h2>
                    <p style="color:#888; font-size:0.85rem; margin-bottom:20px;">Berikut adalah status pendaftaran Anda
                        saat ini.</p>
                    <div id="status-content">
                        <p style="color:#888; font-size:0.9rem;">Memuat data status...</p>
                    </div>
                </div>
            </div>


        </main><!-- /content-area -->
    </div><!-- /dashboard-wrapper -->

    <div id="message"
        style="position:fixed;bottom:20px;right:20px;background:#1a4d2e;color:white;padding:10px 18px;border-radius:8px;font-size:0.85rem;display:none;z-index:999;">
    </div>

    <script>
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        const token = localStorage.getItem('access_token');

        if (!token || user.role !== 'pendaftar') {
            window.location.href = '/login';
        }

        // ========== 1. Nama User ==========
        document.getElementById('headerUserName').innerText = user.name || 'Pengguna';

        // ========== 2. Navigasi Halaman Utama ==========
        function navigate(event, sectionName) {
            event.preventDefault();

            // Sembunyikan semua section
            const sections = document.querySelectorAll('.section');
            sections.forEach(section => section.style.display = 'none');

            // Tampilkan section yang dituju
            const target = document.getElementById(sectionName);
            if (target) {
                target.style.display = 'block';
            }

            // Perbarui active state sidebar
            const sidebarItems = document.querySelectorAll('.sidebar-menu li');
            sidebarItems.forEach(item => item.classList.remove('active'));
            const activeItem = document.querySelector(`.sidebar-menu li[data-section="${sectionName}"]`);
            if (activeItem) {
                activeItem.classList.add('active');
            }

            // Panggil fungsi load sesuai section
            if (sectionName === 'formulir') {
                loadFormulirSection();
            } else if (sectionName === 'pembayaran') {
                loadMetodeUntukPendaftar();
                loadRiwayatBukti();
                cekStatusPendaftaran();
            } else if (sectionName === 'seleksi') {
                loadSeleksiSaya();
            } else if (sectionName === 'pengumuman') {
                loadPengumuman();
            } else if (sectionName === 'status') {
                loadStatusPendaftaran();
            }
        }

        // Load metode pembayaran
        async function loadMetodeUntukPendaftar() {
            const res = await fetch('/api/metode-pembayaran', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const data = await res.json();
            let html = '';
            data.forEach(m => {
                let imgHtml = '';
                if (m.gambar_qris) {
                    imgHtml = `<img src="/api/file/metode/${m.id}?token=${token}" width="500" style="display:block; margin-top:6px;">`;
                }
                html += `<div class="payment-method-card">
                    <strong>${m.nama_bank || 'QRIS'}</strong>
                    <small>No Rek: ${m.nomor_rekening || '-'} (${m.atas_nama || '-'})</small>
                    ${imgHtml}
                    <small>${m.keterangan || ''}</small>
                </div>`;
            });
            document.getElementById('metode-pembayaran-list').innerHTML = html || '<p>Belum ada metode</p>';
        }

        // Upload bukti
        document.getElementById('form-bukti').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const res = await fetch('/api/bukti-pembayaran', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token },
                body: formData
            });
            if (res.ok) {
                alert('Bukti dikirim');
                loadRiwayatBukti();
            }
        });

        // Tampilkan riwayat bukti
        async function loadRiwayatBukti() {
            const res = await fetch('/api/bukti-pembayaran', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const data = await res.json();
            let html = '<table class="riwayat-table"><thead><tr><th>Jenis</th><th>Status</th><th>Bukti</th><th>Kwitansi</th><th>Catatan</th></tr></thead><tbody>';
            data.forEach(b => {
                const buktiLink = `<a href="/api/file/bukti/${b.id_bukti_pembayaran}?token=${token}" target="_blank">Lihat Bukti</a>`;
                let kwitansiLink = '-';
                if (b.verifikasi?.kwitansi) {
                    kwitansiLink = `<a href="/api/file/kwitansi/${b.verifikasi.kwitansi.id_kwitansi}?token=${token}" target="_blank">Unduh Kwitansi</a>`;
                }
                html += `<tr>
                    <td>${b.jenis_pembayaran}</td>
                    <td>${b.status}</td>
                    <td>${buktiLink}</td>
                    <td>${kwitansiLink}</td>
                    <td>${b.verifikasi?.catatan || ''}</td>
                </tr>`;
            });
            html += '</tbody></table>';
            document.getElementById('riwayat-bukti').innerHTML = html;
        }

        // Cek status pendaftaran untuk mengaktifkan jenis "masuk"
        async function cekStatusPendaftaran() {
            const res = await fetch('/api/formulir-saya', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const result = await res.json();
            if (result.data && result.data.status === 'diterima') {
                document.querySelector('#jenis_pembayaran option[value="masuk"]').disabled = false;
            }
        }

        // ========== 3. Multistep dalam Formulir ==========
        function showStep(stepNumber) {
            // Sembunyikan semua step
            const steps = ['step-1', 'step-2', 'step-3', 'step-selesai'];
            steps.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.display = 'none';
            });

            // Tampilkan step yang diminta
            const stepMap = {
                1: 'step-1',
                2: 'step-2',
                3: 'step-3',
                'selesai': 'step-selesai'
            };
            const el = document.getElementById(stepMap[stepNumber]);
            if (el) el.style.display = 'block';
        }

        function nextStep(step) {
            showStep(step);
        }

        function prevStep(step) {
            showStep(step);
        }

        // Toggle Orang Tua / Wali
        function toggleOrangTuaWali() {
            const radio = document.querySelector('input[name="tipe_wali"]:checked');
            const formOrtu = document.getElementById('form-orang-tua');
            const formWali = document.getElementById('form-wali');
            if (radio && radio.value === 'orang_tua') {
                formOrtu.style.display = 'block';
                formWali.style.display = 'none';
            } else {
                formOrtu.style.display = 'none';
                formWali.style.display = 'block';
            }
        }

        function toggleInputAkademik() {
            const checkbox = document.getElementById('bukan-pindahan');
            const formAkademik = document.getElementById('form-akademik');
            if (formAkademik) {
                formAkademik.style.display = checkbox.checked ? 'none' : 'block';
            }
        }

        // Fungsi untuk mengambil semua data formulir
        function getFormData() {
            const tipe_wali = document.querySelector('input[name="tipe_wali"]:checked')?.value || 'orang_tua';
            const isBukanPindahan = document.getElementById('bukan-pindahan')?.checked || false;

            const data = {
                // Biodata siswa
                nama_lengkap: document.getElementById('nama_lengkap')?.value || '',
                tempat_lahir: document.getElementById('tempat_lahir')?.value || '',
                tanggal_lahir: document.getElementById('tanggal_lahir')?.value || '',
                nik: document.getElementById('nik')?.value || '',
                agama: document.getElementById('agama')?.value || '',
                warga_negara: document.getElementById('warga_negara')?.value || '',
                anak_ke: document.getElementById('anak_ke')?.value || '',
                jumlah_saudara: document.getElementById('jumlah_saudara')?.value || '',
                alamat_lengkap: document.getElementById('alamat_lengkap')?.value || '',
                tipe_wali: tipe_wali,

                // Data orang tua / wali (hanya isi sesuai tipe)
                ...(tipe_wali === 'orang_tua' ? {
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
                } : {
                    nama_wali: document.getElementById('nama_wali')?.value || '',
                    pekerjaan_wali: document.getElementById('pekerjaan_wali')?.value || '',
                    agama_wali: document.getElementById('agama_wali')?.value || '',
                    pendidikan_wali: document.getElementById('pendidikan_wali')?.value || '',
                    no_ktp_wali: document.getElementById('no_ktp_wali')?.value || '',
                    penghasilan_wali: document.getElementById('penghasilan_wali')?.value || '',
                    no_telp_wali: document.getElementById('no_telp_wali')?.value || '',
                    alamat_wali: document.getElementById('alamat_wali')?.value || '',
                }),

                // Data akademik (hanya jika bukan pindahan tidak dikirim, bisa null)
                is_bukan_pindahan: isBukanPindahan,
                ...(isBukanPindahan ? {} : {
                    asal_sekolah: document.getElementById('asal_sekolah')?.value || '',
                    no_ijazah: document.getElementById('no_ijazah')?.value || '',
                    tahun_ijazah: document.getElementById('tahun_ijazah')?.value || '',
                    diterima_kelas: document.getElementById('diterima_kelas')?.value || '',
                    pindah_dari: document.getElementById('pindah_dari')?.value || '',
                    no_pindah: document.getElementById('no_pindah')?.value || '',
                    tanggal_pindah: document.getElementById('tanggal_pindah')?.value || '',
                })
            };

            return data;
        }

        // Fungsi submit ke server
        async function submitForm() {
            const token = localStorage.getItem('access_token');
            if (!token) {
                alert('Anda belum login.');
                return;
            }

            const payload = getFormData();
            try {
                const response = await fetch('/api/formulir', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();
                if (response.ok) {
                    alert('Pendaftaran berhasil!');
                    loadFormulirSection(); // reload status
                } else {
                    alert('Gagal: ' + (result.message || JSON.stringify(result.errors)));
                }
            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan jaringan.');
            }
        }

        // ========== FUNGSI FORMULIR DINAMIS ==========
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

        async function loadFormulirSection() {
            const token = localStorage.getItem('access_token');
            // 1. Ambil data formulir yang sudah diisi (jika ada)
            const response = await fetch('/api/formulir-saya', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const result = await response.json();
            const data = result.data;
            const konten = document.getElementById('konten-formulir');

            // PRIORITAS: Jika formulir sudah diterima, tampilkan pesan terima kasih
            if (data && data.status === 'diterima') {
                konten.innerHTML = `<p>Formulir Anda sudah diterima. Terima kasih.</p>`;
                return;
            }

            // 2. Jika belum diterima, cek gelombang aktif
            const gelombangAktif = await getGelombangAktif();
            if (!gelombangAktif) {
                konten.innerHTML = '<p>Sedang tidak ada gelombang pendaftaran aktif. Silakan cek kembali nanti.</p>';
                return;
            }

            // 3. Tampilkan form sesuai status (belum ada, menunggu, ditolak)
            if (data === null) {
                konten.innerHTML = formHtmlKosong(gelombangAktif);
            } else if (data.status === 'menunggu') {
                konten.innerHTML = `<p>Formulir sedang menunggu verifikasi.</p>`;
            } else if (data.status === 'ditolak') {
                const catatan = data.verifikasi?.catatan ?? '-';
                konten.innerHTML = `
                    <p>Formulir Anda ditolak dengan catatan:</p>
                    <blockquote style="background:#fde8e8; padding:12px; border-left:3px solid #dc2626; margin:10px 0;">${catatan}</blockquote>
                    <p>Silakan perbaiki formulir di bawah ini:</p>
                    ${formHtmlEdit(data, gelombangAktif)}
                `;
            }
        }

        // Fungsi untuk menampilkan informasi biaya gelombang
        function infoBiayaGelombang(gel) {
            return `<div style="background:#f0f7f2; border-radius:8px; padding:15px; margin-bottom:20px;">
                <p style="margin:0;"><strong>Gelombang ${gel.nomor_gelombang} (${gel.tahun})</strong></p>
                <p style="margin:4px 0;">Biaya Formulir: Rp ${parseInt(gel.biaya_formulir).toLocaleString()}</p>
                <p style="margin:4px 0;">Biaya Daftar Ulang (jika lulus): Rp ${parseInt(gel.biaya_daftar_ulang).toLocaleString()}</p>
                <p style="margin:4px 0;">Kuota tersisa: ${gel.sisa_kuota} dari ${gel.kuota}</p>
                <p style="margin:4px 0;">Periode: ${new Date(gel.periode_mulai).toLocaleString()} s/d ${new Date(gel.periode_selesai).toLocaleString()}</p>
            </div>`;
        }

        function formHtmlKosong(gel) {
            return `
                ${infoBiayaGelombang(gel)}
                <div id="step-1" class="form-step">
                    <fieldset>
                        <legend>Langkah 1: Biodata Siswa</legend>
                        <label>Nama Lengkap:</label><input type="text" id="nama_lengkap">
                        <label>Tempat Lahir:</label><input type="text" id="tempat_lahir">
                        <label>Tanggal Lahir:</label><input type="date" id="tanggal_lahir">
                        <label>NIK:</label><input type="text" id="nik">
                        <label>Agama:</label>
                        <select id="agama">
                            <option>Islam</option><option>Kristen</option><option>Katolik</option>
                            <option>Hindu</option><option>Buddha</option><option>Konghucu</option>
                        </select>
                        <label>Warga Negara:</label><input type="text" id="warga_negara">
                        <label>Anak ke-:</label><input type="number" id="anak_ke" min="1">
                        <label>Jumlah Saudara:</label><input type="number" id="jumlah_saudara" min="0">
                        <label>Alamat Lengkap:</label><textarea id="alamat_lengkap" rows="3"></textarea>
                    </fieldset>
                    <div class="step-nav">
                        <button type="button" class="btn-submit" onclick="nextStep(2)">Selanjutnya</button>
                    </div>
                </div>

                <!-- STEP 2: ORANG TUA / WALI -->
                <div id="step-2" class="form-step" style="display:none;">
                    <fieldset>
                        <legend>Langkah 2: Data Orang Tua / Wali</legend>
                        <label style="display:inline; margin-right:15px;"><input type="radio" name="tipe_wali" value="orang_tua" checked onchange="toggleOrangTuaWali()"> Orang Tua</label>
                        <label style="display:inline;"><input type="radio" name="tipe_wali" value="wali" onchange="toggleOrangTuaWali()"> Wali</label>
                        <hr>
                        
                        <!-- Form Orang Tua -->
                        <div id="form-orang-tua">
                            <h3>Data Ayah</h3>
                            <input type="text" id="nama_ayah" placeholder="Nama Lengkap Ayah">
                            <input type="text" id="pekerjaan_ayah" placeholder="Pekerjaan Ayah">
                            <select id="agama_ayah"><option value="">-- Pilih --</option><option>Islam</option><option>Kristen</option><option>Katolik</option><option>Hindu</option><option>Buddha</option><option>Konghucu</option></select>
                            <input type="text" id="pendidikan_ayah" placeholder="Pendidikan Terakhir Ayah">
                            <input type="text" id="no_ktp_ayah" placeholder="No KTP Ayah">
                            <input type="text" id="penghasilan_ayah" placeholder="Penghasilan per Bulan Ayah">
                            <input type="text" id="no_telp_ayah" placeholder="No Telp Ayah">
                            <textarea id="alamat_ayah" rows="2" placeholder="Alamat Ayah"></textarea>
                            <hr>
                            <h3>Data Ibu</h3>
                            <input type="text" id="nama_ibu" placeholder="Nama Lengkap Ibu">
                            <input type="text" id="pekerjaan_ibu" placeholder="Pekerjaan Ibu">
                            <select id="agama_ibu"><option value="">-- Pilih --</option><option>Islam</option><option>Kristen</option><option>Katolik</option><option>Hindu</option><option>Buddha</option><option>Konghucu</option></select>
                            <input type="text" id="pendidikan_ibu" placeholder="Pendidikan Terakhir Ibu">
                            <input type="text" id="no_ktp_ibu" placeholder="No KTP Ibu">
                            <input type="text" id="penghasilan_ibu" placeholder="Penghasilan per Bulan Ibu">
                            <input type="text" id="no_telp_ibu" placeholder="No Telp Ibu">
                            <textarea id="alamat_ibu" rows="2" placeholder="Alamat Ibu"></textarea>
                        </div>
                        <div id="form-wali" style="display:none;">
                            <h3>Data Wali</h3>
                            <input type="text" id="nama_wali" placeholder="Nama Lengkap Wali">
                            <input type="text" id="pekerjaan_wali" placeholder="Pekerjaan Wali">
                            <select id="agama_wali"><option value="">-- Pilih --</option><option>Islam</option><option>Kristen</option><option>Katolik</option><option>Hindu</option><option>Buddha</option><option>Konghucu</option></select>
                            <input type="text" id="pendidikan_wali" placeholder="Pendidikan Terakhir Wali">
                            <input type="text" id="no_ktp_wali" placeholder="No KTP Wali">
                            <input type="text" id="penghasilan_wali" placeholder="Penghasilan per Bulan Wali">
                            <input type="text" id="no_telp_wali" placeholder="No Telp Wali">
                            <textarea id="alamat_wali" rows="2" placeholder="Alamat Wali"></textarea>
                        </div>
                    </fieldset>
                    <div class="step-nav">
                        <button type="button" class="btn-secondary" onclick="prevStep(1)">Sebelumnya</button>
                        <button type="button" class="btn-submit" onclick="nextStep(3)">Selanjutnya</button>
                    </div>
                </div>

                <!-- STEP 3: DATA AKADEMIK (MURID PINDAHAN - DENGAN CHECKBOX DI SINI) -->
                <div id="step-3" class="form-step" style="display:none;">
                    <fieldset>
                        <legend>Langkah 3: Data Akademik</legend>
                        <label><input type="checkbox" id="bukan-pindahan" onchange="toggleInputAkademik()"> Bukan murid pindahan</label>
                        <hr>
                        <div id="form-akademik">
                            <input type="text" id="asal_sekolah" placeholder="Asal Sekolah/Madrasah">
                            <input type="text" id="no_ijazah" placeholder="No Ijazah/STTB">
                            <input type="text" id="tahun_ijazah" placeholder="Tahun">
                            <input type="text" id="diterima_kelas" placeholder="Diterima di Kelas">
                            <input type="text" id="pindah_dari" placeholder="Pindah dari">
                            <input type="text" id="no_pindah" placeholder="No Pindah">
                            <input type="date" id="tanggal_pindah">
                        </div>
                    </fieldset>
                    <div class="step-nav">
                        <button type="button" class="btn-secondary" onclick="prevStep(2)">Sebelumnya</button>
                        <button type="button" class="btn-submit" onclick="submitForm()">Kirim Formulir</button>
                    </div>
                </div>
            `;
        }

        function formHtmlEdit(data, gel) {
            return `
                ${infoBiayaGelombang(gel)}
                <div id="step-1" class="form-step">
                    <fieldset>
                        <legend>Langkah 1: Biodata Siswa</legend>
                        <label>Nama Lengkap:</label><input type="text" id="nama_lengkap" value="${data.nama_lengkap || ''}">
                        <label>Tempat Lahir:</label><input type="text" id="tempat_lahir" value="${data.tempat_lahir || ''}">
                        <label>Tanggal Lahir:</label><input type="date" id="tanggal_lahir" value="${data.tanggal_lahir || ''}">
                        <label>NIK:</label><input type="text" id="nik" value="${data.nik || ''}">
                        <label>Agama:</label>
                        <select id="agama">
                            <option ${data.agama === 'Islam' ? 'selected' : ''}>Islam</option>
                            <option ${data.agama === 'Kristen' ? 'selected' : ''}>Kristen</option>
                            <option ${data.agama === 'Katolik' ? 'selected' : ''}>Katolik</option>
                            <option ${data.agama === 'Hindu' ? 'selected' : ''}>Hindu</option>
                            <option ${data.agama === 'Buddha' ? 'selected' : ''}>Buddha</option>
                            <option ${data.agama === 'Konghucu' ? 'selected' : ''}>Konghucu</option>
                        </select>
                        <label>Warga Negara:</label><input type="text" id="warga_negara" value="${data.warga_negara || ''}">
                        <label>Anak ke-:</label><input type="number" id="anak_ke" value="${data.anak_ke || ''}">
                        <label>Jumlah Saudara:</label><input type="number" id="jumlah_saudara" value="${data.jumlah_saudara || ''}">
                        <label>Alamat Lengkap:</label><textarea id="alamat_lengkap" rows="3">${data.alamat_lengkap || ''}</textarea>
                    </fieldset>
                    <div class="step-nav">
                        <button type="button" class="btn-submit" onclick="nextStep(2)">Selanjutnya</button>
                    </div>
                </div>

                <div id="step-2" class="form-step" style="display:none;">
                    <fieldset>
                        <legend>Langkah 2: Data Orang Tua / Wali</legend>
                        <label style="display:inline; margin-right:15px;">
                            <input type="radio" name="tipe_wali" value="orang_tua" ${data.tipe_wali === 'orang_tua' ? 'checked' : ''} onchange="toggleOrangTuaWali()"> Orang Tua
                        </label>
                        <label style="display:inline;">
                            <input type="radio" name="tipe_wali" value="wali" ${data.tipe_wali === 'wali' ? 'checked' : ''} onchange="toggleOrangTuaWali()"> Wali
                        </label>
                        <hr>

                        <div id="form-orang-tua" style="display:${data.tipe_wali === 'orang_tua' ? 'block' : 'none'};">
                            <h3>Data Ayah</h3>
                            <input type="text" id="nama_ayah" value="${data.nama_ayah || ''}" placeholder="Nama Lengkap Ayah">
                            <input type="text" id="pekerjaan_ayah" value="${data.pekerjaan_ayah || ''}" placeholder="Pekerjaan Ayah">
                            <select id="agama_ayah"><option value="">-- Pilih --</option><option ${data.agama_ayah === 'Islam' ? 'selected' : ''}>Islam</option><option ${data.agama_ayah === 'Kristen' ? 'selected' : ''}>Kristen</option><option ${data.agama_ayah === 'Katolik' ? 'selected' : ''}>Katolik</option><option ${data.agama_ayah === 'Hindu' ? 'selected' : ''}>Hindu</option><option ${data.agama_ayah === 'Buddha' ? 'selected' : ''}>Buddha</option><option ${data.agama_ayah === 'Konghucu' ? 'selected' : ''}>Konghucu</option></select>
                            <input type="text" id="pendidikan_ayah" value="${data.pendidikan_ayah || ''}" placeholder="Pendidikan Terakhir Ayah">
                            <input type="text" id="no_ktp_ayah" value="${data.no_ktp_ayah || ''}" placeholder="No KTP Ayah">
                            <input type="text" id="penghasilan_ayah" value="${data.penghasilan_ayah || ''}" placeholder="Penghasilan per Bulan Ayah">
                            <input type="text" id="no_telp_ayah" value="${data.no_telp_ayah || ''}" placeholder="No Telp Ayah">
                            <textarea id="alamat_ayah" rows="2" placeholder="Alamat Ayah">${data.alamat_ayah || ''}</textarea>
                            <hr>
                            <h3>Data Ibu</h3>
                            <input type="text" id="nama_ibu" value="${data.nama_ibu || ''}" placeholder="Nama Lengkap Ibu">
                            <input type="text" id="pekerjaan_ibu" value="${data.pekerjaan_ibu || ''}" placeholder="Pekerjaan Ibu">
                            <select id="agama_ibu"><option value="">-- Pilih --</option><option ${data.agama_ibu === 'Islam' ? 'selected' : ''}>Islam</option><option ${data.agama_ibu === 'Kristen' ? 'selected' : ''}>Kristen</option><option ${data.agama_ibu === 'Katolik' ? 'selected' : ''}>Katolik</option><option ${data.agama_ibu === 'Hindu' ? 'selected' : ''}>Hindu</option><option ${data.agama_ibu === 'Buddha' ? 'selected' : ''}>Buddha</option><option ${data.agama_ibu === 'Konghucu' ? 'selected' : ''}>Konghucu</option></select>
                            <input type="text" id="pendidikan_ibu" value="${data.pendidikan_ibu || ''}" placeholder="Pendidikan Terakhir Ibu">
                            <input type="text" id="no_ktp_ibu" value="${data.no_ktp_ibu || ''}" placeholder="No KTP Ibu">
                            <input type="text" id="penghasilan_ibu" value="${data.penghasilan_ibu || ''}" placeholder="Penghasilan per Bulan Ibu">
                            <input type="text" id="no_telp_ibu" value="${data.no_telp_ibu || ''}" placeholder="No Telp Ibu">
                            <textarea id="alamat_ibu" rows="2" placeholder="Alamat Ibu">${data.alamat_ibu || ''}</textarea>
                        </div>

                        <div id="form-wali" style="display:${data.tipe_wali === 'wali' ? 'block' : 'none'};">
                            <h3>Data Wali</h3>
                            <input type="text" id="nama_wali" value="${data.nama_wali || ''}" placeholder="Nama Lengkap Wali">
                            <input type="text" id="pekerjaan_wali" value="${data.pekerjaan_wali || ''}" placeholder="Pekerjaan Wali">
                            <select id="agama_wali"><option value="">-- Pilih --</option><option ${data.agama_wali === 'Islam' ? 'selected' : ''}>Islam</option><option ${data.agama_wali === 'Kristen' ? 'selected' : ''}>Kristen</option><option ${data.agama_wali === 'Katolik' ? 'selected' : ''}>Katolik</option><option ${data.agama_wali === 'Hindu' ? 'selected' : ''}>Hindu</option><option ${data.agama_wali === 'Buddha' ? 'selected' : ''}>Buddha</option><option ${data.agama_wali === 'Konghucu' ? 'selected' : ''}>Konghucu</option></select>
                            <input type="text" id="pendidikan_wali" value="${data.pendidikan_wali || ''}" placeholder="Pendidikan Terakhir Wali">
                            <input type="text" id="no_ktp_wali" value="${data.no_ktp_wali || ''}" placeholder="No KTP Wali">
                            <input type="text" id="penghasilan_wali" value="${data.penghasilan_wali || ''}" placeholder="Penghasilan per Bulan Wali">
                            <input type="text" id="no_telp_wali" value="${data.no_telp_wali || ''}" placeholder="No Telp Wali">
                            <textarea id="alamat_wali" rows="2" placeholder="Alamat Wali">${data.alamat_wali || ''}</textarea>
                        </div>
                    </fieldset>
                    <div class="step-nav">
                        <button type="button" class="btn-secondary" onclick="prevStep(1)">Sebelumnya</button>
                        <button type="button" class="btn-submit" onclick="nextStep(3)">Selanjutnya</button>
                    </div>
                </div>

                <div id="step-3" class="form-step" style="display:none;">
                    <fieldset>
                        <legend>Langkah 3: Data Akademik</legend>
                        <label>
                            <input type="checkbox" id="bukan-pindahan" ${data.is_bukan_pindahan ? 'checked' : ''} onchange="toggleInputAkademik()"> Bukan murid pindahan
                        </label>
                        <hr>
                        <div id="form-akademik" style="display:${data.is_bukan_pindahan ? 'none' : 'block'};">
                            <input type="text" id="asal_sekolah" value="${data.asal_sekolah || ''}" placeholder="Asal Sekolah/Madrasah">
                            <input type="text" id="no_ijazah" value="${data.no_ijazah || ''}" placeholder="No Ijazah/STTB">
                            <input type="text" id="tahun_ijazah" value="${data.tahun_ijazah || ''}" placeholder="Tahun">
                            <input type="text" id="diterima_kelas" value="${data.diterima_kelas || ''}" placeholder="Diterima di Kelas">
                            <input type="text" id="pindah_dari" value="${data.pindah_dari || ''}" placeholder="Pindah dari">
                            <input type="text" id="no_pindah" value="${data.no_pindah || ''}" placeholder="No Pindah">
                            <input type="date" id="tanggal_pindah" value="${data.tanggal_pindah || ''}">
                        </div>
                    </fieldset>
                    <div class="step-nav">
                        <button type="button" class="btn-secondary" onclick="prevStep(2)">Sebelumnya</button>
                        <button type="button" class="btn-submit" onclick="submitForm()">Kirim Formulir</button>
                    </div>
                </div>
            `;
        }

        // jadwal tes
        async function loadSeleksiSaya() {
            const container = document.getElementById('info-seleksi');
            try {
                const res = await fetch('/api/seleksi-saya', { headers: { 'Authorization': 'Bearer ' + token } });
                const data = await res.json();

                // Nilai default jika data kosong
                let jadwalText = data?.jadwal_tes || 'Belum ada jadwal tes. Silakan cek secara berkala.';

                // Anda bisa parsing tanggal, waktu, lokasi dari string jadwalText
                // Untuk contoh, kita gunakan data statis yang informatif, namun tetap menampilkan informasi dari API.
                // Jika API mengembalikan field yang lebih terstruktur (tanggal, waktu, lokasi), bisa digunakan di sini.
                // Di sini kita asumsikan jadwalText berisi informasi seperti "Sabtu, 20 Mei 2025, 08:00 - 12:00 di Ruang Aula"

                // Contoh parsing sederhana (opsional, sesuaikan dengan format sebenarnya)
                let dateTime = 'Belum ditentukan';
                let location = 'MI Ziyadatul Ihsan';
                if (jadwalText !== 'Belum ada jadwal tes. Silakan cek secara berkala.') {
                    // Contoh: ekstrak tanggal dan lokasi (sangat sederhana)
                    dateTime = jadwalText;
                    location = 'MI Ziyadatul Ihsan, Jl. Sadar No.33';
                }

                const html = `
                    <div class="jadwal-tes-grid">
                        <!-- Card Tes Akademik -->
                        <div class="jadwal-tes-card">
                            <div class="jadwal-tes-card-header">
                                <div class="tes-icon">
                                    <i class="fi fi-rr-book"></i>
                                </div>
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
                                        <strong>90 Menit</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Tes Baca Al-Qur'an -->
                        <div class="jadwal-tes-card">
                            <div class="jadwal-tes-card-header">
                                <div class="tes-icon">
                                    <i class="fi fi-rr-star"></i>
                                </div>
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

        // Fungsi kecil untuk menghindari XSS
        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function (m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        // pengumuman
        async function loadPengumuman() {
            const res = await fetch('/api/seleksi-saya', { headers: { 'Authorization': 'Bearer ' + token } });
            const data = await res.json();
            const container = document.getElementById('info-pengumuman');
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
        }

        // status pendaftaran
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
                    { label: 'Formulir Pendaftaran', status: data.status === 'diterima' ? 'done' : (data.status === 'menunggu' ? 'current' : 'pending') },
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
                    } catch (e) { }
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
                } catch (e) { }

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

        // ========== 4. Logout ==========
        document.getElementById('logoutButton').addEventListener('click', async function () {
            try {
                const response = await fetch('/api/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    localStorage.removeItem('access_token');
                    localStorage.removeItem('user');
                    window.location.href = '/login';
                } else {
                    document.getElementById('message').innerText = 'Gagal logout.';
                    document.getElementById('message').style.display = 'block';
                    setTimeout(() => {
                        document.getElementById('message').style.display = 'none';
                    }, 3000);
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('message').innerText = 'Terjadi kesalahan jaringan.';
                document.getElementById('message').style.display = 'block';
                setTimeout(() => {
                    document.getElementById('message').style.display = 'none';
                }, 3000);
            }
        });
    </script>
</body>

</html>