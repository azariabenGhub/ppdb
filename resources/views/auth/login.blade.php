<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sanctum Demo</title>
</head>

<body>
    <h1>Login</h1>

    <form id="loginForm">
        <div>
            <label>Email:</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div>
            <label>Password:</label>
            <input type="password" name="password" id="password" required>
        </div>
        <button type="submit">Masuk</button>
    </form>

    <p>Belum punya akun? <a href="/register">Daftar di sini</a></p>

    <div id="message"></div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value
            };

            try {
                const response = await fetch('/api/login', {
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
                    document.getElementById('message').innerText = data.message || 'Login gagal.';
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('message').innerText = 'Terjadi kesalahan jaringan.';
            }
        });
    </script>
</body>

</html>