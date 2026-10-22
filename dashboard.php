
<?php
session_start();

if (empty($_SESSION['admin_login'])) {
    header('Location: ../login-admin.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

// Fungsi untuk mengamankan output HTML.
if (!function_exists('gayatri_h')) {
    function gayatri_h($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

// Memastikan koneksi database tersedia.
if (!isset($conn) || !($conn instanceof mysqli)) {
    die('Koneksi database gagal. Periksa config/database.php.');
}

// Mengambil angka statistik.
function gayatri_total($conn, $sql) {
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        return 0;
    }

    $row = mysqli_fetch_assoc($result);
    return (int)($row['total'] ?? 0);
}

$totalBooking = gayatri_total(
    $conn,
    "SELECT COUNT(*) AS total FROM bookings"
);

$totalPengunjung = gayatri_total(
    $conn,
    "SELECT COUNT(*) AS total FROM pengunjung"
);

$tempatTersedia = gayatri_total(
    $conn,
    "SELECT COUNT(*) AS total FROM tempat WHERE aktif = 1"
);

$bookingHariIni = gayatri_total(
    $conn,
    "SELECT COUNT(*) AS total FROM bookings WHERE tanggal = CURDATE()"
);

// Mengambil lima booking terbaru.
$bookingTerbaru = [];

$sql = "
    SELECT
        b.id,
        b.kode_booking,
        b.nama,
        b.acara,
        b.tanggal,
        b.jumlah,
        b.status,
        t.nama AS nama_tempat
    FROM bookings b
    LEFT JOIN tempat t ON t.id = b.tempat_id
    ORDER BY b.dibuat_pada DESC, b.id DESC
    LIMIT 5
";

$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $bookingTerbaru[] = $row;
    }
}

// Menyiapkan grafik booking tujuh hari terakhir.
$labelsGrafik = [];
$dataHarian = [];

for ($i = 6; $i >= 0; $i--) {
    $hari = date('Y-m-d', strtotime("-$i days"));

    $labelsGrafik[] = date('d M', strtotime($hari));
    $dataHarian[$hari] = 0;
}

$sqlGrafik = "
    SELECT DATE(dibuat_pada) AS hari, COUNT(*) AS total
    FROM bookings
    WHERE dibuat_pada >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
      AND dibuat_pada < DATE_ADD(CURDATE(), INTERVAL 1 DAY)
    GROUP BY DATE(dibuat_pada)
";

$resultGrafik = mysqli_query($conn, $sqlGrafik);

if ($resultGrafik) {
    while ($row = mysqli_fetch_assoc($resultGrafik)) {
        if (isset($dataHarian[$row['hari']])) {
            $dataHarian[$row['hari']] = (int)$row['total'];
        }
    }
}

$dataGrafik = array_values($dataHarian);
$namaAdmin = $_SESSION['admin_username'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard Admin - Gayatri</title>

<link rel="stylesheet"
 href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f3f6f3;
    color: #183b30;
}

a {
    text-decoration: none;
}

.sidebar {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    width: 220px;
    padding: 22px 14px;
    background: #103b2e;
    color: white;
    display: flex;
    flex-direction: column;
    z-index: 10;
}

.brand {
    margin: 0 5px 35px;
    font-family: Georgia, serif;
    font-size: 19px;
    font-weight: bold;
    line-height: 1.5;
}

.brand i {
    margin-right: 7px;
    font-size: 23px;
}

.menu-label {
    margin: 0 8px 12px;
    font-size: 10px;
    color: #b5d5c5;
    text-transform: uppercase;
}

.sidebar a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    margin-bottom: 5px;
    border-radius: 8px;
    color: white;
    font-size: 13px;
}

.sidebar a:hover,
.sidebar a.active {
    background: #2c6a50;
}

.sidebar .logout {
    margin-top: auto;
    border-top: 1px solid #426455;
    border-radius: 0;
    padding-top: 20px;
}

.main {
    margin-left: 220px;
    min-height: 100vh;
}

.topbar {
    min-height: 62px;
    background: white;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 10px;
    padding: 12px 25px;
    border-bottom: 1px solid #e7ece8;
}

.avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: #1d6a4e;
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
}

.topbar small {
    color: #849087;
}

.welcome {
    padding: 25px;
    background:
        linear-gradient(90deg, rgba(242,248,240,.96),
        rgba(235,244,233,.80)),
        url('../img/kemah.jpeg') center/cover;
}

.welcome h1 {
    font-family: Georgia, serif;
    font-size: 26px;
    margin: 0 0 8px;
}

.welcome p {
    margin: 0;
    color: #61766c;
    font-size: 13px;
}

.content {
    padding: 20px;
}

.stats {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 15px;
    margin-bottom: 18px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 3px 12px #00000006;
}

.stat-icon {
    width: 42px;
    height: 42px;
    flex-shrink: 0;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
}

.green { background: #258b68; }
.blue { background: #287bd0; }
.orange { background: #f18a1d; }
.purple { background: #7960cf; }

.stat-label {
    color: #74877d;
    font-size: 11px;
    margin-bottom: 7px;
}

.stat-number {
    font-size: 25px;
    font-weight: bold;
    margin-bottom: 5px;
}

.stat-note {
    color: #829087;
    font-size: 10px;
}

.columns {
    display: grid;
    grid-template-columns: minmax(0, 2fr) minmax(250px, 1fr);
    gap: 15px;
    margin-bottom: 15px;
}

.panel {
    background: white;
    border-radius: 12px;
    padding: 20px;
    min-width: 0;
    box-shadow: 0 3px 12px #00000006;
}

.panel h3 {
    font-family: Georgia, serif;
    font-size: 16px;
    margin: 0 0 18px;
}

.chart-wrap {
    height: 250px;
    position: relative;
}

.quick-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    margin-bottom: 9px;
    border-radius: 7px;
    color: white;
    font-size: 12px;
    font-weight: bold;
}

.quick-link:last-child {
    margin-bottom: 0;
}

.quick-link:hover {
    filter: brightness(.95);
}

.quick-green { background: #218865; }
.quick-blue { background: #287bd0; }
.quick-orange { background: #ed8b1c; }
.quick-purple { background: #7960cf; }

.table-wrap {
    overflow-x: auto;
}

table {
    width: 100%;
    min-width: 650px;
    border-collapse: collapse;
}

th, td {
    text-align: left;
    padding: 12px 10px;
    border-bottom: 1px solid #e9efeb;
    font-size: 11px;
}

th {
    background: #edf4ef;
}

.status {
    display: inline-block;
    padding: 5px 8px;
    border-radius: 15px;
    font-size: 10px;
    font-weight: bold;
    white-space: nowrap;
}

.menunggu {
    background: #fff0d9;
    color: #9b651c;
}

.dikonfirmasi {
    background: #d9f3e7;
    color: #217354;
}

.dibatalkan {
    background: #ffe0e0;
    color: #a53b3b;
}

.action-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #edf4ef;
    color: #245b43;
}

.see-all {
    float: right;
    color: #217557;
    font-size: 11px;
}

.activity {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 17px;
}

.activity:last-child {
    margin-bottom: 0;
}

.activity-icon {
    width: 32px;
    height: 32px;
    flex-shrink: 0;
    border-radius: 50%;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
}

.activity strong {
    display: block;
    font-size: 11px;
    margin-bottom: 5px;
}

.activity small {
    font-size: 10px;
    color: #819087;
}

footer {
    padding: 25px 10px;
    text-align: center;
    font-size: 10px;
    color: #829087;
}

@media(max-width: 1100px) {
    .stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .columns {
        grid-template-columns: 1fr;
    }
}

@media(max-width: 650px) {
    .sidebar {
        width: 65px;
        padding: 15px 8px;
    }

    .brand {
        font-size: 0;
        text-align: center;
        margin: 10px 0 30px;
    }

    .brand i {
        font-size: 25px;
        margin: 0;
    }

    .menu-label {
        display: none;
    }

    .sidebar a {
        justify-content: center;
        padding: 13px 5px;
        font-size: 0;
    }

    .sidebar a i {
        font-size: 17px;
    }

    .main {
        margin-left: 65px;
    }

    .content {
        padding: 12px;
    }

    .stats {
        gap: 10px;
    }

    .stat-card {
        padding: 10px;
        gap: 7px;
    }

    .stat-icon {
        width: 30px;
        height: 30px;
        font-size: 12px;
    }

    .stat-number {
        font-size: 20px;
    }

    .welcome {
        padding: 20px 15px;
    }

    .welcome h1 {
        font-size: 21px;
    }

    .panel {
        padding: 14px;
    }

    .chart-wrap {
        height: 210px;
    }
}
</style>
</head>

<body>

<aside class="sidebar">

    <div class="brand">
        <i class="fa-solid fa-mountain-sun"></i>
        BUMI PERKEMAHAN<br>GAYATRI
    </div>

    <div class="menu-label">Menu Utama</div>

    <a href="dashboard.php" class="active">
        <i class="fa-solid fa-house"></i> Dashboard
    </a>

    <a href="booking.php">
        <i class="fa-solid fa-calendar-days"></i> Data Booking
    </a>

    <a href="pengunjung.php">
        <i class="fa-solid fa-users"></i> Data Pengunjung
    </a>

    <a href="tempat.php">
        <i class="fa-solid fa-location-dot"></i> Data Tempat
    </a>

    <a href="pengaturan.php">
        <i class="fa-solid fa-gear"></i> Pengaturan
    </a>

    <a href="logout.php" class="logout">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
    </a>

</aside>

<div class="main">

    <header class="topbar">
        <div class="avatar">
            <i class="fa-solid fa-user"></i>
        </div>
        <div>
            <strong><?= gayatri_h($namaAdmin) ?></strong><br>
            <small>Administrator</small>
        </div>
    </header>

    <section class="welcome">
        <h1>Selamat Datang, <?= gayatri_h($namaAdmin) ?>!</h1>
        <p>
            Kelola data pemesanan dan informasi Bumi Perkemahan Gayatri
            dengan mudah.
        </p>
    </section>

    <main class="content">

        <section class="stats">

            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fa-regular fa-calendar-check"></i>
                </div>
                <div>
                    <div class="stat-label">Total Booking</div>
                    <div class="stat-number"><?= $totalBooking ?></div>
                    <div class="stat-note">Data pemesanan</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <div class="stat-label">Total Pengunjung</div>
                    <div class="stat-number"><?= $totalPengunjung ?></div>
                    <div class="stat-note">Data pengunjung terdaftar</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <div>
                    <div class="stat-label">Tempat Tersedia</div>
                    <div class="stat-number"><?= $tempatTersedia ?></div>
                    <div class="stat-note">Jenis tempat aktif</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fa-solid fa-calendar-day"></i>
                </div>
                <div>
                    <div class="stat-label">Booking Hari Ini</div>
                    <div class="stat-number"><?= $bookingHariIni ?></div>
                    <div class="stat-note">Pemesanan untuk hari ini</div>
                </div>
            </div>

        </section>

        <section class="columns">

            <div class="panel">
                <h3>Grafik Booking 7 Hari Terakhir</h3>
                <div class="chart-wrap">
                    <canvas id="bookingChart"></canvas>
                </div>
            </div>

            <div class="panel">
                <h3>Menu Cepat</h3>

                <a href="booking.php" class="quick-link quick-green">
                    <i class="fa-solid fa-calendar-plus"></i>
                    Tambah Booking
                </a>

                <a href="tempat.php" class="quick-link quick-blue">
                    <i class="fa-solid fa-location-dot"></i>
                    Kelola Tempat
                </a>

                <a href="pengunjung.php" class="quick-link quick-orange">
                    <i class="fa-solid fa-users"></i>
                    Lihat Pengunjung
                </a>

                <a href="pengaturan.php" class="quick-link quick-purple">
                    <i class="fa-solid fa-gear"></i>
                    Pengaturan Akun
                </a>
            </div>

        </section>

        <section class="columns">

            <div class="panel">
                <h3>
                    Booking Terbaru
                    <a href="booking.php" class="see-all">Lihat Semua →</a>
                </h3>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Jenis Acara</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php if (empty($bookingTerbaru)): ?>
                            <tr>
                                <td colspan="6">
                                    Belum ada data booking.
                                </td>
                            </tr>
                        <?php else: ?>

                            <?php foreach ($bookingTerbaru as $index => $b): ?>

                                <?php
                                $statusClass = 'menunggu';

                                if ($b['status'] === 'Dikonfirmasi') {
                                    $statusClass = 'dikonfirmasi';
                                } elseif ($b['status'] === 'Dibatalkan') {
                                    $statusClass = 'dibatalkan';
                                }
                                ?>

                                <tr>
                                    <td><?= $index + 1 ?></td>

                                    <td><?= gayatri_h($b['nama']) ?></td>

                                    <td><?= gayatri_h($b['acara']) ?></td>

                                    <td><?= gayatri_h($b['tanggal']) ?></td>

                                    <td>
                                        <span class="status <?= $statusClass ?>">
                                            <?= gayatri_h($b['status']) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <a href="booking.php"
                                           class="action-link"
                                           title="Kelola booking">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel">
                <h3>Aktivitas Terakhir</h3>

                <div class="activity">
                    <div class="activity-icon green">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div>
                        <strong>Dashboard berhasil dibuka</strong>
                        <small>Aktivitas administrator</small>
                    </div>
                </div>

                <div class="activity">
                    <div class="activity-icon blue">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div>
                        <strong>Login sebagai <?= gayatri_h($namaAdmin) ?></strong>
                        <small>Sesi administrator aktif</small>
                    </div>
                </div>

                <div class="activity">
                    <div class="activity-icon orange">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div>
                        <strong>Periksa booking terbaru</strong>
                        <small><?= $totalBooking ?> pemesanan tercatat</small>
                    </div>
                </div>
            </div>

        </section>

        <footer>
            © <?= date('Y') ?> Bumi Perkemahan Gayatri.
            Semua Hak Dilindungi.
        </footer>

    </main>
</div>

<script>
const chartElement = document.getElementById('bookingChart');

new Chart(chartElement, {
    type: 'line',

    data: {
        labels: <?= json_encode($labelsGrafik) ?>,

        datasets: [{
            label: 'Jumlah Booking',
            data: <?= json_encode($dataGrafik) ?>,
            borderColor: '#198260',
            backgroundColor: 'rgba(25,130,96,0.12)',
            fill: true,
            tension: 0.35,
            borderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: '#198260'
        }]
    },

    options: {
        responsive: true,
        maintainAspectRatio: false,

        plugins: {
            legend: {
                display: false
            }
        },

        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});
</script>

</body>
</html>
