<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/csrf.php';

$__jobsheetRoot = dirname(__DIR__);
$__scriptDir = dirname($_SERVER['SCRIPT_FILENAME']);
$__rel = ltrim(str_replace('\\', '/', substr($__scriptDir, strlen($__jobsheetRoot))), '/');
$base = $__rel === '' ? '' : str_repeat('../', substr_count($__rel, '/') + 1);

// Penanda menu aktif berdasarkan URL saat ini
$__uri = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$__aktif = [
    'beranda' => $__rel === '' ? ' active' : '',
    'list'    => (strpos($__uri, '/transaksi/') !== false && strpos($__uri, '/transaksi/tambah') === false) ? ' active' : '',
    'laporan' => strpos($__uri, '/laporan/') !== false ? ' active' : '',
    'tambah'  => strpos($__uri, '/transaksi/tambah') !== false ? ' active' : '',
    'iuran'   => strpos($__uri, '/iuran/') !== false ? ' active' : '',
    'anggota' => strpos($__uri, '/anggota/') !== false ? ' active' : '',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIM Kas Yayasan Rumah Quran Mumtazah<?php echo isset($page_title) ? ' | ' . $page_title : ''; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css">
</head>
<body>
<header class="site-header">
    <nav class="navbar navbar-expand-xl navbar-dark" aria-label="Navigasi utama">
        <div class="container-fluid px-3 px-xl-4">
            <a class="navbar-brand brand" href="<?php echo $base; ?>index.php">
                <img src="<?php echo $base; ?>assets/images/logo-rq.svg" alt="Logo Yayasan Rumah Quran Mumtazah">
                <span>Yayasan Rumah Quran Mumtazah</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Buka menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-xl-center gap-xl-1">
                    <li class="nav-item"><a class="nav-link<?php echo $__aktif['beranda']; ?>" href="<?php echo $base; ?>index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link<?php echo $__aktif['list']; ?>" href="<?php echo $base; ?>transaksi/list.php">Daftar Transaksi</a></li>
                    <li class="nav-item"><a class="nav-link<?php echo $__aktif['laporan']; ?>" href="<?php echo $base; ?>laporan/index.php">Laporan</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link<?php echo $__aktif['anggota']; ?>" href="<?php echo $base; ?>anggota/list.php">Anggota</a></li>
                        <li class="nav-item"><a class="nav-link<?php echo $__aktif['iuran']; ?>" href="<?php echo $base; ?>iuran/list.php">Iuran Anggota</a></li>
                        <?php if (($_SESSION['role'] ?? '') === 'bendahara'): ?>
                            <li class="nav-item"><a class="nav-link<?php echo $__aktif['tambah']; ?>" href="<?php echo $base; ?>transaksi/tambah.php">Tambah Transaksi</a></li>
                        <?php endif; ?>
                        <li class="nav-item nav-user"><span><?php echo e((string) ($_SESSION['nama'] ?? $_SESSION['username'])); ?> (<?php echo e((string) ($_SESSION['role'] ?? 'bendahara')); ?>)</span></li>
                        <li class="nav-item"><a class="nav-link nav-logout" href="<?php echo $base; ?>auth/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link nav-logout" href="<?php echo $base; ?>auth/login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
<main class="main-wrap">