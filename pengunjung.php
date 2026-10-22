
<?php
session_start();
if (empty($_SESSION['admin_login'])) {
    header('Location: ../login-admin.php');
    exit;
}

$bookings = $_SESSION['bookings'] ?? [];
function esc($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Data Pengunjung - Gayatri</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#f4f6f3;font-family:Arial,sans-serif;color:#233b30}
header{padding:20px 5%;background:#12352b;color:white;display:flex;justify-content:space-between}
header a{color:white;text-decoration:none}nav{padding:15px 5%;background:white;display:flex;gap:20px;flex-wrap:wrap}
nav a{color:#24523c;text-decoration:none;font-size:14px}main{padding:30px;max-width:1100px;margin:auto}
.panel{background:white;border-radius:12px;padding:22px;overflow-x:auto}
table{width:100%;border-collapse:collapse;min-width:600px}th,td{text-align:left;padding:13px;border-bottom:1px solid #e5ebe6;font-size:14px}th{background:#edf3ef}
.badge{color:#1d6548;font-weight:bold}
@media(max-width:600px){main{padding:18px}}
</style>
</head>
<body>
<header><strong>⛺ GAYATRI ADMIN</strong><a href="logout.php">Logout ↗</a></header>
<nav>
<a href="dashboard.php">Dashboard</a><a href="booking.php">Data Booking</a>
<a href="pengunjung.php">Data Pengunjung</a><a href="tempat.php">Data Tempat</a>
<a href="pengaturan.php">Pengaturan</a>
</nav>
<main>
<h1>Data Pengunjung</h1>
<p>Daftar pemesan yang tercatat dalam sistem.</p>
<section class="panel">
<table>
<thead><tr><th>No.</th><th>Nama Pengunjung</th><th>Tempat</th><th>Tanggal Kunjungan</th><th>Jumlah Orang</th><th>Status</th></tr></thead>
<tbody>
<?php if (!$bookings): ?>
<tr><td colspan="6">Belum ada data pengunjung. Tambahkan booking terlebih dahulu.</td></tr>
<?php else: $no=1; foreach ($bookings as $b): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= esc($b['nama']) ?></td>
<td><?= esc($b['tempat']) ?></td>
<td><?= esc($b['tanggal']) ?></td>
<td><?= (int)$b['jumlah'] ?> orang</td>
<td class="badge"><?= esc($b['status']) ?></td>
</tr>
<?php endforeach; endif; ?>
</tbody>
</table>
</section>
</main>
</body>
</html>
