
<?php
session_start();

if (empty($_SESSION['admin_login'])) {
    header('Location: ../login-admin.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$error = '';

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// ==========================================
// PROSES TAMBAH, UBAH STATUS, DAN HAPUS
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';

    // TAMBAH BOOKING
    if ($aksi === 'tambah') {
        $nama = trim($_POST['nama'] ?? '');
        $hp = trim($_POST['hp'] ?? '');
        $tempat = trim($_POST['tempat'] ?? '');
        $acara = trim($_POST['acara'] ?? '');
        $tanggal = trim($_POST['tanggal'] ?? '');
        $jumlah = filter_var(
            $_POST['jumlah'] ?? null,
            FILTER_VALIDATE_INT
        );
        $alamat = trim($_POST['alamat'] ?? '');

        $date = DateTime::createFromFormat('!Y-m-d', $tanggal);
        $validDate = $date && $date->format('Y-m-d') === $tanggal;

        if (
            $nama === '' || strlen($nama) > 100 ||
            $hp === '' || strlen($hp) > 25 ||
            $tempat === '' ||
            $acara === '' || strlen($acara) > 80 ||
            !$validDate || $tanggal < date('Y-m-d') ||
            $jumlah === false || $jumlah < 1 ||
            $alamat === '' || strlen($alamat) > 500
        ) {
            $error = 'Periksa kembali semua data booking.';
        } else {
            try {
                mysqli_begin_transaction($conn);

                // Cari ID tempat berdasarkan nama.
                $stmt = mysqli_prepare(
                    $conn,
                    "SELECT id FROM tempat
                     WHERE nama = ? AND aktif = 1
                     LIMIT 1"
                );

                mysqli_stmt_bind_param($stmt, "s", $tempat);
                mysqli_stmt_execute($stmt);
                $hasil = mysqli_stmt_get_result($stmt);
                $dataTempat = mysqli_fetch_assoc($hasil);
                mysqli_stmt_close($stmt);

                if (!$dataTempat) {
                    throw new Exception('Tempat tidak ditemukan.');
                }

                $tempatId = (int)$dataTempat['id'];

                // Buat kode booking unik.
                $kode = 'GYT-' . date('Ymd') . '-'
                    . strtoupper(bin2hex(random_bytes(4)));

                // Simpan booking ke MySQL.
                $stmt = mysqli_prepare(
                    $conn,
                    "INSERT INTO bookings
                    (kode_booking, nama, hp, tempat_id, acara,
                     tanggal, jumlah, alamat, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Menunggu')"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "sssissis",
                    $kode,
                    $nama,
                    $hp,
                    $tempatId,
                    $acara,
                    $tanggal,
                    $jumlah,
                    $alamat
                );

                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception('Gagal menyimpan booking.');
                }

                $bookingId = mysqli_insert_id($conn);
                mysqli_stmt_close($stmt);

                // Simpan data pemesan.
                $stmt = mysqli_prepare(
                    $conn,
                    "INSERT INTO pengunjung
                    (booking_id, nama, hp, alamat)
                    VALUES (?, ?, ?, ?)"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "isss",
                    $bookingId,
                    $nama,
                    $hp,
                    $alamat
                );

                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception('Gagal menyimpan data pengunjung.');
                }

                mysqli_stmt_close($stmt);
                mysqli_commit($conn);

                header('Location: booking.php?pesan=tambah');
                exit;

            } catch (Throwable $ex) {
                mysqli_rollback($conn);
                error_log('Tambah booking: ' . $ex->getMessage());
                $error = 'Booking gagal ditambahkan. Periksa kembali database.';
            }
        }
    }

    // UBAH STATUS BOOKING
    if ($aksi === 'status') {
        $id = filter_var(
            $_POST['id'] ?? null,
            FILTER_VALIDATE_INT
        );
        $status = $_POST['status'] ?? '';

        $statusValid = [
            'Menunggu',
            'Dikonfirmasi',
            'Dibatalkan'
        ];

        if (!$id || $id < 1 ||
            !in_array($status, $statusValid, true)) {
            $error = 'Data status booking tidak valid.';
        } else {
            $stmt = mysqli_prepare(
                $conn,
                "UPDATE bookings SET status = ? WHERE id = ?"
            );

            mysqli_stmt_bind_param($stmt, "si", $status, $id);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header('Location: booking.php?pesan=status');
                exit;
            }

            mysqli_stmt_close($stmt);
            $error = 'Status booking gagal diperbarui.';
        }
    }

    // HAPUS BOOKING
    if ($aksi === 'hapus') {
        $id = filter_var(
            $_POST['id'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (!$id || $id < 1) {
            $error = 'ID booking tidak valid.';
        } else {
            $stmt = mysqli_prepare(
                $conn,
                "DELETE FROM bookings WHERE id = ?"
            );

            mysqli_stmt_bind_param($stmt, "i", $id);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header('Location: booking.php?pesan=hapus');
                exit;
            }

            mysqli_stmt_close($stmt);
            $error = 'Booking gagal dihapus.';
        }
    }
}

// ==========================================
// AMBIL DAFTAR TEMPAT DARI DATABASE
// ==========================================

$tempatResult = mysqli_query(
    $conn,
    "SELECT nama FROM tempat
     WHERE aktif = 1
     ORDER BY nama ASC"
);

$tempatList = [];

if ($tempatResult) {
    while ($row = mysqli_fetch_assoc($tempatResult)) {
        $tempatList[] = $row['nama'];
    }
}

// ==========================================
// AMBIL DAFTAR BOOKING DARI DATABASE
// ==========================================

$sql = "SELECT
            b.id,
            b.kode_booking,
            b.nama,
            b.hp,
            b.acara,
            t.nama AS tempat,
            b.tanggal,
            b.jumlah,
            b.alamat,
            b.status
        FROM bookings b
        INNER JOIN tempat t ON b.tempat_id = t.id
        ORDER BY b.dibuat_pada DESC";

$result = mysqli_query($conn, $sql);
$bookings = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $bookings[] = $row;
    }
} else {
    $error = 'Data booking gagal dimuat dari database.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Data Booking - Gayatri Admin</title>

<style>
*{box-sizing:border-box}
body{
    margin:0;
    background:#f4f6f3;
    color:#233b30;
    font-family:Arial,sans-serif
}
header{
    background:#12352b;
    color:white;
    padding:20px 5%;
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px
}
header a{color:white;text-decoration:none}
nav{
    background:white;
    padding:14px 5%;
    display:flex;
    gap:20px;
    flex-wrap:wrap
}
nav a{
    color:#24523c;
    text-decoration:none;
    font-size:14px
}
main{max-width:1200px;margin:auto;padding:30px 20px}
h1{margin-top:0}
.panel{
    background:white;
    padding:22px;
    border-radius:12px;
    margin-bottom:22px;
    box-shadow:0 3px 12px #00000008
}
form.add{
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:14px
}
label{
    display:block;
    font-size:13px;
    font-weight:bold;
    margin-bottom:7px
}
input,select,textarea{
    width:100%;
    padding:11px;
    border:1px solid #d6dfd8;
    border-radius:7px;
    font-family:inherit
}
button,.btn{
    border:0;
    border-radius:7px;
    padding:10px 14px;
    background:#1d6548;
    color:white;
    cursor:pointer;
    text-decoration:none
}
table{
    width:100%;
    border-collapse:collapse;
    min-width:850px
}
th,td{
    text-align:left;
    padding:13px 10px;
    border-bottom:1px solid #e8eee9;
    font-size:13px
}
th{background:#edf3ef}
.table-wrap{overflow-x:auto}
.actions{
    display:flex;
    gap:6px;
    align-items:center;
    flex-wrap:wrap
}
.actions form{display:flex;gap:5px;align-items:center}
.actions select{width:auto;padding:8px}
.delete{background:#a63838}
.status{font-weight:bold;color:#286447}
.error,.notice{
    padding:12px;
    margin-bottom:15px;
    border-radius:7px
}
.error{background:#fff0f0;color:#a12626}
.notice{background:#e3f5e9;color:#17623e}
@media(max-width:750px){
    form.add{grid-template-columns:1fr 1fr}
}
@media(max-width:480px){
    form.add{grid-template-columns:1fr}
    header{align-items:flex-start;flex-direction:column}
}
</style>
</head>

<body>
<header>
    <strong>⛺ GAYATRI ADMIN</strong>
    <a href="logout.php">Keluar ↗</a>
</header>

<nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="booking.php">Data Booking</a>
    <a href="pengunjung.php">Data Pengunjung</a>
    <a href="tempat.php">Data Tempat</a>
    <a href="pengaturan.php">Pengaturan</a>
</nav>

<main>
    <h1>Data Booking</h1>
    <p>Kelola pemesanan tempat di Bumi Perkemahan Gayatri.</p>

    <?php if ($error !== ''): ?>
        <div class="error"><?= e($error) ?></div>
    <?php endif; ?>

    <?php
    $pesan = $_GET['pesan'] ?? '';
    $notifikasi = [
        'tambah' => 'Booking berhasil ditambahkan.',
        'status' => 'Status booking berhasil diperbarui.',
        'hapus' => 'Booking berhasil dihapus.'
    ];
    ?>

    <?php if (isset($notifikasi[$pesan])): ?>
        <div class="notice">
            <?= e($notifikasi[$pesan]) ?>
        </div>
    <?php endif; ?>

    <section class="panel">
        <h3>Tambah Booking</h3>

        <form class="add" method="post">
            <input type="hidden" name="aksi" value="tambah">

            <div>
                <label>Nama Pemesan</label>
                <input name="nama" maxlength="100" required>
            </div>

            <div>
                <label>Nomor HP</label>
                <input name="hp" maxlength="25" required>
            </div>

            <div>
                <label>Tempat</label>
                <select name="tempat" required>
                    <option value="">Pilih tempat</option>
                    <?php foreach ($tempatList as $namaTempat): ?>
                        <option value="<?= e($namaTempat) ?>">
                            <?= e($namaTempat) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label>Jenis Acara</label>
                <select name="acara" required>
                    <option value="">Pilih acara</option>
                    <option value="Camping">Camping</option>
                    <option value="Gathering">Gathering</option>
                    <option value="Family Gathering">Family Gathering</option>
                    <option value="Pelatihan">Pelatihan</option>
                    <option value="Acara Lainnya">Acara Lainnya</option>
                </select>
            </div>

            <div>
                <label>Tanggal Booking</label>
                <input type="date" name="tanggal"
                       min="<?= date('Y-m-d') ?>" required>
            </div>

            <div>
                <label>Jumlah Orang</label>
                <input type="number" name="jumlah" min="1" required>
            </div>

            <div>
                <label>Alamat Pemesan</label>
                <textarea name="alamat" maxlength="500"
                          rows="2" required></textarea>
            </div>

            <div style="align-self:end">
                <button type="submit">+ Tambah Booking</button>
            </div>
        </form>
    </section>

    <section class="panel">
        <h3>Daftar Pemesanan</h3>

        <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Tempat</th>
                    <th>Tanggal</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
            <?php if (empty($bookings)): ?>
                <tr>
                    <td colspan="7">Belum ada data booking.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($bookings as $b): ?>
                <tr>
                    <td><?= e($b['kode_booking']) ?></td>
                    <td><?= e($b['nama']) ?></td>
                    <td><?= e($b['tempat']) ?></td>
                    <td><?= e($b['tanggal']) ?></td>
                    <td><?= (int)$b['jumlah'] ?> orang</td>
                    <td class="status"><?= e($b['status']) ?></td>
                    <td>
                        <div class="actions">
                            <form method="post">
                                <input type="hidden" name="aksi"
                                       value="status">
                                <input type="hidden" name="id"
                                       value="<?= (int)$b['id'] ?>">

                                <select name="status"
                                        aria-label="Status booking">
                                    <?php foreach (
                                        ['Menunggu','Dikonfirmasi','Dibatalkan']
                                        as $s
                                    ): ?>
                                        <option value="<?= e($s) ?>"
                                            <?= $b['status'] === $s
                                                ? 'selected' : '' ?>>
                                            <?= e($s) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <button type="submit">Simpan</button>
                            </form>

                            <form method="post"
                                  onsubmit="return confirm('Hapus booking ini?')">
                                <input type="hidden" name="aksi"
                                       value="hapus">
                                <input type="hidden" name="id"
                                       value="<?= (int)$b['id'] ?>">

                                <button class="delete" type="submit">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </section>
</main>
</body>
</html>
