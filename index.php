<?php
// index.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bumi Perkemahan Gayatri</title>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #f8f7f2;
            color: #263b31;
        }

        .navbar {
            min-height: 85px;
            padding: 15px 5%;
            background: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px #0000000d;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #1d3b2d;
            text-decoration: none;
        }
        


.logo {
    display: flex;
    align-items: center;
    gap: 12px;
    color: #1d3b2d;
    text-decoration: none;
}

.logo img {
    width: 65px;
    height: 55px;
    object-fit: contain;
    display: block;
    flex-shrink: 0;
}

.logo-text {
    font-family: 'Playfair Display', serif;
    font-weight: 700;
    font-size: 17px;
    line-height: 1.2;
}

.logo-text span {
    display: block;
    font-size: 24px;
    line-height: 1.2;
}

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 28px;
        }

        .nav-menu > a {
            text-decoration: none;
            color: #35443d;
            font-size: 14px;
        }

        .nav-menu > a:hover {
            color: #60835e;
        }

        .nav-menu .btn-login-admin {
            background: #1d3b2d;
            color: white;
            padding: 14px 24px;
            border-radius: 35px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 9px;
            transition: 0.3s;
        }

        .nav-menu .btn-login-admin:hover {
            background: #345a43;
            color: white;
        }

        .hero {
            min-height: 450px;
            padding: 65px 6%;
            display: flex;
            align-items: center;
            color: white;
            background:
                linear-gradient(90deg, #000000b8, #00000020),
                url('https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?auto=format&fit=crop&w=1800&q=85')
                center/cover no-repeat;
        }

        .hero-content {
            max-width: 570px;
        }

        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(38px, 5vw, 53px);
            line-height: 1.15;
            margin: 12px 0 20px;
        }

        .hero p {
            line-height: 1.8;
            margin-bottom: 20px;
            font-size: 25px;
        }

        .location {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .catalog {
            padding: 55px 5% 65px;
            text-align: center;
        }

        .section-icon {
            margin-bottom: 12px;
            color: #244634;
            font-size: 25px;
        }

        .catalog h2,
        .booking-section h2 {
            font-family: 'Playfair Display', serif;
        }

        .catalog h2 {
            font-size: 40px;
            margin-bottom: 12px;
        }

        .catalog-description {
            max-width: 600px;
            margin: 0 auto 32px;
            color: #53635b;
            line-height: 1.8;
        }

        .catalog-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 25px;
            text-align: left;
        }

        .card {
            background: white;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 5px 20px #00000010;
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card img {
            display: block;
            width: 100%;
            height: 220px;
            object-fit: cover;
        }

        .card-content {
            padding: 20px;
        }

        .card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            margin-bottom: 12px;
        }

        .card p {
            color: #53635b;
            font-size: 14px;
            line-height: 1.8;
            min-height: 76px;
        }

        .card-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 18px;
            font-size: 13px;
        }

        .card-info i {
            color: #244634;
            font-size: 25px;
        }

        .booking-section {
            padding: 60px 20px;
            text-align: center;
            color: white;
            background:
                linear-gradient(#183527eb, #183527f0),
                url('https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=1600&q=85')
                center/cover no-repeat;
        }

        .booking-section > i {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .booking-section h2 {
            font-size: 34px;
            margin-bottom: 14px;
        }

        .booking-section p {
            line-height: 1.8;
            margin-bottom: 25px;
        }

        .btn-booking {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 16px 28px;
            background: #527d4d;
            color: white;
            border-radius: 35px;
            text-decoration: none;
            font-weight: 700;
            transition: 0.3s;
        }

        .btn-booking:hover {
            background: #6b9665;
            transform: translateY(-2px);
        }

        footer {
            padding: 22px 15px;
            text-align: center;
            color: #e4eae5;
            background: #152b20;
            font-size: 13px;
        }

        @media (max-width: 850px) {
            .navbar {
                flex-wrap: wrap;
            }

            .nav-menu {
                gap: 16px;
                flex-wrap: wrap;
            }

            .catalog-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 600px) {
            .navbar {
                padding: 15px 4%;
            }

            .nav-menu {
                width: 100%;
                justify-content: space-between;
                gap: 12px;
            }

            .nav-menu > a {
                font-size: 12px;
            }

            .nav-menu .btn-login-admin {
                padding: 10px 12px;
            }

            .hero {
                min-height: 400px;
                padding: 45px 6%;
            }

            .hero h1 {
                font-size: 38px;
            }

            .catalog {
                padding: 40px 5%;
            }

            .catalog h2 {
                font-size: 32px;
            }

            .catalog-grid {
                grid-template-columns: 1fr;
            }

            .booking-section h2 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <header class="navbar">
        
<a href="#beranda" class="logo">
    <img src="img/logo.png" alt="Logo Bumi Perkemahan Gayatri">
    <div class="logo-text">
        Bumi Perkemahan
        <span>GAYATRI</span>
    </div>
</a>


        <nav class="nav-menu">
            <a href="#beranda">Beranda</a>
            <a href="#tentang">Tentang Kami</a>
            <a href="#katalog">Katalog Tempat</a>

            <!-- Tombol Login Admin -->
           <a href="login-admin.php" class="btn-login-admin">
    <i class="fa-solid fa-user"></i>
    Login Admin
</a>
        </nav>
    </header>

    <!-- HERO -->
    <section class="hero" id="beranda">
        <div class="hero-content">
            <p>Selamat Datang di</p>
            <h1>Bumi Perkemahan Gayatri</h1>
            <p>
                Tempat terbaik untuk berkemah, belajar, dan
                membangun kebersamaan di alam terbuka.
            </p>
            <div class="location">
                <i class="fa-solid fa-location-dot"></i>
                Lebakwengi,Kec.Walantaka,Kota Serang - Banten
            </div>
        </div>
    </section>

    <!-- KATALOG TEMPAT -->
    <section class="catalog" id="katalog">
        <div class="section-icon">
            <i class="fa-solid fa-tree"></i>
        </div>

        <h2>Katalog Tempat</h2>
        <p class="catalog-description">
            Nikmati berbagai fasilitas yang kami sediakan untuk
            kegiatan perkemahan, gathering, maupun acara lainnya.
        </p>

        <div class="catalog-grid">

            <article class="card">
                <img src="img/kemah.jpeg" alt="Lapangan Api Unggun">

                <div class="card-content">
                    <h3>Lapangan Api Unggun</h3>
                    <p>
                        Cocok untuk kegiatan api unggun, keakraban,
                        dan berbagai acara kebersamaan lainnya.
                    </p>
                    <div class="card-info">
                        <i class="fa-solid fa-fire"></i>
                        <span>Area kegiatan api unggun</span>
                    </div>
                </div>
            </article>

            <article class="card">
                <img src="img/aula.jpeg"
                    alt="Aula">

                <div class="card-content">
                    <h3>Aula</h3>
                    <p>
                        Aula serbaguna untuk acara internal, seminar,
                        pertemuan, maupun kegiatan kelompok.
                    </p>
                    <div class="card-info">
                        <i class="fa-solid fa-users"></i>
                        <span>Untuk kegiatan kelompok</span>
                    </div>
                </div>
            </article>

            <article class="card">
                <img
                    src="https://images.unsplash.com/photo-1504851149312-7a075b496cc7?auto=format&fit=crop&w=800&q=80"
                    alt="Tempat Camping">

                <div class="card-content">
                    <h3>Tempat Camping</h3>
                    <p>
                        Area camping yang luas dan nyaman dengan
                        pemandangan alam yang asri dan sejuk.
                    </p>
                    <div class="card-info">
                        <i class="fa-solid fa-tent"></i>
                        <span>Tersedia area untuk tenda</span>
                    </div>
                </div>
            </article>

        </div>
        <!-- GALERI KEGIATAN -->
        <h2>Galeri Kegiatan</h2>
        <p class="catalog-description">
            Lihat keseruan kegiatan berkemah, api unggun, dan kebersamaan di alam Bumi Perkemahan Gayatri.
        </p>
         <div class="catalog-grid">

            <article class="card">
                <img src="img/dokumen.jpeg" alt="Lapangan Api Unggun">

                <div class="card-content">
                    <h3>Apel Peramuka</h3>
                    <p>
                        Kegiatan apel pramuka untuk melatih kedisiplinan, kekompakan, dan semangat kebersamaan peserta.
                    </p>
                    <div class="card-info">
                        
                    </div>
                </div>
            </article>

            <article class="card">
                <img src="img/pmi.jpeg"
                    alt="Aula">

                <div class="card-content">
                    <h3>Reuni PMI Provinsi Banten</h3>
                    <p>
                        Kegiatan reuni PMI untuk mempererat silaturahmi, mengenang kebersamaan, dan memperkuat persaudaraan antaranggota.
                    </p>
                    <div class="card-info">
                        
                        
                    </div>
                </div>
            </article>

            <article class="card">
                <img src="img/gantung.png"
                    alt="Tempat Camping">

                <div class="card-content">
                    <h3>Kegiatan Magang</h3>
                    <p>
                        Kegiatan magang untuk menambah pengalaman, melatih keterampilan, dan meningkatkan kerja sama di lingkungan alam terbuka.
                    </p>
                    <div class="card-info">
                    </div>
                </div>
            </article>
            <article class="card">
                <img src="img/magang.jpeg"
                    alt="Tempat Camping">

                <div class="card-content">
                    <h3>Kegiatan Magang</h3>
                    <p>
                        Kegiatan magang untuk menambah pengalaman, melatih keterampilan, dan meningkatkan kerja sama di lingkungan alam terbuka.
                    </p>
                    <div class="card-info">
                    </div>
                </div>
            </article>

        </div>
    </section>

    <!-- BOOKING -->
    <section class="booking-section" id="tentang">
        <i class="fa-solid fa-mountain"></i>
        <h2>Siap untuk Berpetualang?</h2>
        <p>
            Segera lakukan pemesanan tempat sesuai kebutuhan Anda
            <br>
            untuk pengalaman berkemah yang tak terlupakan.
        </p>

        <a href="booking.php" class="btn-booking">
            <i class="fa-regular fa-calendar-days"></i>
            Booking Sekarang
            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </section>
    

    <!-- FOOTER -->
    <footer id="kontak">
        <p>
            &copy; <?php echo date('Y'); ?>
            Bumi Perkemahan Gayatri. Semua Hak Dilindungi.
        </p>
    </footer>

</body>
</html>