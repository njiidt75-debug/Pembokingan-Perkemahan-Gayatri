
<?php
session_start();

// Hapus semua data sesi login
$_SESSION = [];

// Hapus cookie sesi jika digunakan
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Hancurkan sesi
session_destroy();

// Arahkan ke halaman awal website
header("Location: ../index.php");
exit;
?>
