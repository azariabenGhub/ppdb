<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sanctum Demo</title>
</head>

<body>
    <h1>Register</h1>

    <form id="registerForm">
        <div>
            <label>Nama:</label>
            <input type="text" name="name" id="name" required>
        </div>
        <div>
            <label>Email:</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div>
            <label>Password:</label>
            <input type="password" name="password" id="password" required minlength="8">
        </div>
        <div>
            <label>Konfirmasi Password:</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required>
        </div>
        <button type="submit">Daftar</button>
    </form>

    <p>Sudah punya akun? <a href="/login">Login di sini</a></p>

    <div id="message"></div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                password_confirmation: document.getElementById('password_confirmation').value
            };

            try {
                const response = await fetch('/api/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (response.ok) {
                    // Simpan token ke localStorage
                    localStorage.setItem('access_token', data.access_token);
                    localStorage.setItem('user', JSON.stringify(data.user));

                    // Redirect ke dashboard
                    window.location.href = '/dashboard';
                } else {
                    // Tampilkan pesan error (misal validasi)
                    document.getElementById('message').innerText = JSON.stringify(data);
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('message').innerText = 'Terjadi kesalahan jaringan.';
            }
        });
    </script>
</body>

</html>