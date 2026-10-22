
<?php
session_start();

if (empty($_SESSION['admin_login'])) {
    header('Location: ../login-admin.php');
    exit;
}

if (!isset($_SESSION['admin_display_name'])) {
    $_SESSION['admin_display_name'] = $_SESSION['admin_username'] ?? 'Admin';
}

$pesan = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');

    if ($nama === '' || strlen($nama) > 60) {
        $error = 'Nama wajib diisi dan maksimal 60 karakter.';
    } else {
        $_SESSION['admin_display_name'] = $nama;
        $pesan = 'Nama tampilan berhasil diperbarui.';
    }
}

function esc($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pengaturan Admin - Gayatri</title>
<style>
*{box-sizing:border-box}
body{margin:0;background:#f4f6f3;font-family:Arial,sans-serif;color:#233b30}
header{padding:20px 5%;background:#12352b;color:white;display:flex;justify-content:space-between}
header a{color:white;text-decoration:none}
nav{padding:15px 5%;background:white;display:flex;gap:20px;flex-wrap:wrap}
nav a{color:#24523c;text-decoration:none;font-size:14px}
main{max-width:850px;margin:auto;padding:30px 20px}
.panel{background:white;padding:25px;border-radius:12px;margin-bottom:20px}
label{display:block;font-weight:bold;font-size:14px;margin-bottom:8px}
input{width:100%;max-width:500px;padding:12px;border:1px solid #d6dfd8;border-radius:7px;margin-bottom:18px}
button{padding:12px 18px;border:0;border-radius:7px;background:#1d6548;color:white;cursor:pointer}
.success,.error{padding:12px;border-radius:7px;margin-bottom:15px}
.success{background:#e0f5e8;color:#216344}
.error{background:#fff0f0;color:#a12626}
.note{background:#f1f5f1;padding:14px;border-radius:8px;font-size:13px;line-height:1.7;color:#53635b}
</style>
</head>
<body>
<header>
<strong>⛺ GAYATRI ADMIN</strong>
<a href="logout.php">Logout ↗</a>
</header>
<nav>
<a href="dashboard.php">Dashboard</a>
<a href="booking.php">Data Booking</a>
<a href="pengunjung.php">Data Pengunjung</a>
<a href="tempat.php">Data Tempat</a>
<a href="pengaturan.php">Pengaturan</a>
</nav>
<main>
<h1>Pengaturan Admin</h1>
<p>Atur informasi profil administrator.</p>

<section class="panel">
<h3>Profil Administrator</h3>

<?php if ($pesan): ?>
<div class="success"><?= esc($pesan) ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="error"><?= esc($error) ?></div>
<?php endif; ?>

<form method="post">
<label for="nama">Nama Tampilan</label>
<input id="nama" name="nama" maxlength="60"
value="<?= esc($_SESSION['admin_display_name']) ?>" required>
<br>
<button type="submit">Simpan Pengaturan</button>
</form>
</section>

<section class="panel">
<h3>Informasi Akun</h3>
<p><strong>Username login:</strong>
<?= esc($_SESSION['admin_username'] ?? 'Admin') ?></p>
<p class="note">
Pengaturan nama di halaman ini hanya tersimpan selama sesi login.
Penggantian username login dan password belum tersedia karena
akun saat ini masih menggunakan autentikasi contoh. Untuk penggunaan
sebenarnya, simpan akun admin dalam database MySQL dan gunakan
password yang telah di-hash.
</p>
</section>
</main>
</body>
</html>
