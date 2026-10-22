<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Contoh autentikasi sementara.
    // Ganti dengan pemeriksaan akun admin dari database.
    if ($username === 'admin' && $password === 'admin123') {
        session_regenerate_id(true);
        $_SESSION['admin_login'] = true;
        $_SESSION['admin_username'] = $username;

        header('Location: admin/dashboard.php');
        exit;
    }

    $error = 'Username atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Bumi Perkemahan Gayatri</title>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family: 'DM Sans', sans-serif;
            background:
                linear-gradient(#173d2bb0, #173d2bd9),
                url('https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=1600&q=85')
                center/cover no-repeat;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: white;
            padding: 18px 6%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }


.logo {
    display: flex;
    align-items: center;
    gap: 12px;
    color: #1d3b2d;
    text-decoration: none;
    font-family: 'Playfair Display', serif;
    font-size: 17px;
    font-weight: 700;
}

.logo img {
    width: 55px;
    height: 55px;
    object-fit: contain;
    display: block;
    flex-shrink: 0;
}

.logo-text {
    line-height: 1.2;
}

.logo-text span {
    display: block;
    font-size: 24px;
    line-height: 1.2;
}

        .home-link {
            color: #1d3b2d;
            text-decoration: none;
            font-weight: 600;
        }

        .login-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            background: white;
            border-radius: 16px;
            padding: 38px;
            box-shadow: 0 15px 45px #00000035;
        }

        .icon {
            text-align: center;
            color: #1d3b2d;
            font-size: 48px;
            margin-bottom: 15px;
        }

        h1 {
            text-align: center;
            font-family: 'Playfair Display', serif;
            font-size: 30px;
            color: #1d3b2d;
            margin-bottom: 10px;
        }

        .description {
            text-align: center;
            color: #66746b;
            font-size: 14px;
            margin-bottom: 28px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
        }

        .input-box {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid #d7ded8;
            border-radius: 8px;
            padding: 0 13px;
        }

        .input-box i {
            color: #52685a;
        }

        .input-box input {
            width: 100%;
            padding: 13px 0;
            border: none;
            outline: none;
            font-size: 14px;
            background: transparent;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: #1d3b2d;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 8px;
        }

        .btn-login:hover {
            background: #345a43;
        }

        .error {
            color: #a12626;
            background: #fff0f0;
            border-radius: 7px;
            padding: 12px;
            font-size: 14px;
            margin-bottom: 18px;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 22px;
            color: #40594a;
            text-decoration: none;
            font-size: 14px;
        }

        footer {
            padding: 18px;
            text-align: center;
            background: #142b20;
            color: white;
            font-size: 13px;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 28px 22px;
            }
        }
    </style>
</head>
<body>


<header class="navbar">
    <a href="index.php" class="logo">
        <img src="img/logo.png" alt="Logo Bumi Perkemahan Gayatri">
        <div class="logo-text">
            Bumi Perkemahan
            <span>GAYATRI</span>
        </div>
    </a>

    <a href="index.php" class="home-link">
        <i class="fa-solid fa-arrow-left"></i> Beranda
    </a>
</header>


<main class="login-wrapper">
    <div class="login-card">
        <div class="icon">
            <i class="fa-solid fa-user-shield"></i>
        </div>

        <h1>Login Admin</h1>
        <p class="description">
            Silakan masuk untuk mengakses halaman administrator.
        </p>

        <?php if ($error !== ''): ?>
            <div class="error">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-box">
                    <i class="fa-solid fa-user"></i>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Masukkan username"
                        autocomplete="username"
                        required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-box">
                    <i class="fa-solid fa-lock"></i>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        required>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fa-solid fa-right-to-bracket"></i>
                Login
            </button>
        </form>

        <a href="index.php" class="back-link">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali ke Beranda
        </a>
    </div>
</main>

<footer>
    &copy; <?= date('Y') ?> Bumi Perkemahan Gayatri.
    Semua Hak Dilindungi.
</footer>

</body>
</html>