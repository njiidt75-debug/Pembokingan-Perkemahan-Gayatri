
<?php
session_start();
if (empty($_SESSION['admin_login'])) {
    header('Location: ../login-admin.php');
    exit;
}

if (!isset($_SESSION['tempat'])) {
    $_SESSION['tempat'] = [
        ['id'=>1,'nama'=>'Lapangan Api Unggun','deskripsi'=>'Area kegiatan api unggun dan kebersamaan','kapasitas'=>100],
        ['id'=>2,'nama'=>'Aula','deskripsi'=>'Tempat pertemuan dan acara kelompok','kapasitas'=>50],
        ['id'=>3,'nama'=>'Tempat Camping','deskripsi'=>'Area berkemah di alam terbuka','kapasitas'=>200]
    ];
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'tambah') {
        $nama = trim($_POST['nama'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $kapasitas = filter_input(INPUT_POST, 'kapasitas', FILTER_VALIDATE_INT);

        if ($nama === '' || $deskripsi === '' || !$kapasitas || $kapasitas < 1) {
            $error = 'Lengkapi semua kolom dengan data yang benar.';
        } else {
            $id = 1;
            foreach ($_SESSION['tempat'] as $t) {
                $id = max($id, $t['id'] + 1);
            }
            $_SESSION['tempat'][] = compact('id','nama','deskripsi','kapasitas');
            header('Location: tempat.php');
            exit;
        }
    }

    if ($aksi === 'hapus') {
        $id = (int)($_POST['id'] ?? 0);
        $_SESSION['tempat'] = array_values(array_filter(
            $_SESSION['tempat'], fn($t) => $t['id'] !== $id
        ));
        header('Location: tempat.php');
        exit;
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
<title>Data Tempat - Gayatri</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#f4f6f3;font-family:Arial,sans-serif;color:#233b30}
header{padding:20px 5%;background:#12352b;color:white;display:flex;justify-content:space-between}
header a{color:white;text-decoration:none}nav{padding:15px 5%;background:white;display:flex;gap:20px;flex-wrap:wrap}
nav a{color:#24523c;text-decoration:none;font-size:14px}main{padding:30px;max-width:1100px;margin:auto}
.panel{background:white;border-radius:12px;padding:22px;margin-bottom:20px}
form.add{display:grid;grid-template-columns:1fr 1.5fr 130px auto;gap:12px;align-items:end}
label{display:block;font-size:13px;font-weight:bold;margin-bottom:7px}
input{width:100%;padding:11px;border:1px solid #d6dfd8;border-radius:7px}
button{padding:11px 14px;background:#1d6548;color:white;border:0;border-radius:7px;cursor:pointer}
.delete{background:#a63838}table{width:100%;border-collapse:collapse;min-width:600px}
th,td{text-align:left;padding:13px;border-bottom:1px solid #e5ebe6;font-size:13px}th{background:#edf3ef}
.table-wrap{overflow-x:auto}.error{background:#fff0f0;color:#a12626;padding:12px}
@media(max-width:750px){form.add{grid-template-columns:1fr 1fr}}@media(max-width:500px){main{padding:18px}form.add{grid-template-columns:1fr}}
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
<h1>Data Tempat</h1>
<p>Kelola tempat dan fasilitas Bumi Perkemahan Gayatri.</p>
<section class="panel">
<h3>Tambah Tempat</h3>
<?php if ($error): ?><p class="error"><?= esc($error) ?></p><?php endif; ?>
<form class="add" method="post">
<input type="hidden" name="aksi" value="tambah">
<div><label>Nama Tempat</label><input name="nama" required></div>
<div><label>Deskripsi</label><input name="deskripsi" required></div>
<div><label>Kapasitas Orang</label><input type="number" name="kapasitas" min="1" required></div>
<button type="submit">+ Tambah</button>
</form>
</section>
<section class="panel">
<h3>Daftar Tempat</h3>
<div class="table-wrap"><table>
<thead><tr><th>No.</th><th>Nama Tempat</th><th>Deskripsi</th><th>Kapasitas</th><th>Aksi</th></tr></thead>
<tbody>
<?php if (!$_SESSION['tempat']): ?>
<tr><td colspan="5">Belum ada tempat.</td></tr>
<?php else: foreach ($_SESSION['tempat'] as $i=>$t): ?>
<tr>
<td><?= $i+1 ?></td><td><?= esc($t['nama']) ?></td>
<td><?= esc($t['deskripsi']) ?></td><td><?= (int)$t['kapasitas'] ?> orang</td>
<td><form method="post" onsubmit="return confirm('Hapus tempat ini?')">
<input type="hidden" name="aksi" value="hapus">
<input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
<button class="delete">Hapus</button></form></td>
</tr>
<?php endforeach; endif; ?>
</tbody></table></div>
</section>
</main>
</body>
</html>
