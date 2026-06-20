<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Alamat Email Anda</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f8; font-family: 'Inter', Arial, sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f6f8; padding: 40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;">
                    <!-- HEADER -->
                    <tr>
                        <td align="center" style="background-color: #1a4d2e; padding: 30px 20px;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 22px; font-weight: 700; letter-spacing: 0.5px;">MI ZIYADATUL IHSAN</h1>
                            <p style="color: #a3e635; margin: 5px 0 0 0; font-size: 14px; font-weight: 500; text-transform: uppercase; letter-spacing: 1px;">Portal PPDB Online</p>
                        </td>
                    </tr>
                    <!-- BODY -->
                    <tr>
                        <td style="padding: 40px 30px; color: #334155;">
                            <h2 style="color: #1a4d2e; margin-top: 0; margin-bottom: 20px; font-size: 20px; font-weight: 600;">Halo, {{ $name }}!</h2>
                            <p style="font-size: 16px; line-height: 1.6; margin-bottom: 24px; color: #475569;">
                                Terima kasih telah mendaftar di Portal Penerimaan Peserta Didik Baru (PPDB) MI Ziyadatul Ihsan. 
                                Untuk menyelesaikan proses pembuatan akun Anda dan mengaktifkan akses ke formulir pendaftaran, silakan verifikasi alamat email Anda dengan mengeklik tombol di bawah ini:
                            </p>
                            
                            <!-- ACTION BUTTON -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 30px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $url }}" target="_blank" style="display: inline-block; background-color: #1a4d2e; color: #ffffff; text-decoration: none; padding: 14px 30px; font-size: 16px; font-weight: 600; border-radius: 8px; box-shadow: 0 4px 6px rgba(26, 77, 46, 0.2); transition: background-color 0.2s;">
                                            Verifikasi Alamat Email
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size: 14px; line-height: 1.6; color: #64748b; background-color: #f8fafc; padding: 15px; border-radius: 8px; border-left: 4px solid #1a4d2e; margin-bottom: 24px;">
                                <strong>Catatan:</strong> Tautan verifikasi ini berlaku selama 60 menit. Jika Anda tidak merasa melakukan registrasi ini, Anda dapat mengabaikan email ini dengan aman.
                            </p>

                            <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 30px 0;">

                            <p style="font-size: 12px; line-height: 1.6; color: #94a3b8; margin: 0;">
                                Jika Anda mengalami kendala saat mengeklik tombol "Verifikasi Alamat Email", salin dan tempel tautan berikut ke browser Anda:
                                <br>
                                <a href="{{ $url }}" style="color: #1a4d2e; word-break: break-all;">{{ $url }}</a>
                            </p>
                        </td>
                    </tr>
                    <!-- FOOTER -->
                    <tr>
                        <td align="center" style="background-color: #f8fafc; padding: 25px 20px; border-top: 1px solid #e2e8f0; color: #94a3b8; font-size: 12px;">
                            <p style="margin: 0 0 5px 0; font-weight: 600; color: #64748b;">MI Ziyadatul Ihsan</p>
                            <p style="margin: 0 0 15px 0;">Jl. Pesantren No.1, Kec. Bekasi, Kota Bekasi</p>
                            <p style="margin: 0; font-size: 11px;">Email ini dikirim secara otomatis oleh sistem PPDB MI Ziyadatul Ihsan.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
