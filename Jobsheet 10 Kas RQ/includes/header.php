<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$__jobsheetRoot = dirname(__DIR__);
$__scriptDir = dirname($_SERVER['SCRIPT_FILENAME']);
$__rel = ltrim(str_replace('\\', '/', substr($__scriptDir, strlen($__jobsheetRoot))), '/');
$base = $__rel === '' ? '' : str_repeat('../', substr_count($__rel, '/') + 1);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIM Kas Yayasan RQ<?php echo isset($page_title) ? ' | ' . $page_title : ''; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="header-inner">
        <a class="brand" href="<?php echo $base; ?>index.php"><img src="<?php echo $base; ?>assets/images/logo-rq.svg" alt="Logo Yayasan Rumah Quran"><span>Yayasan Rumah Quran</span></a>
        <button type="button" id="nav-toggle-btn" class="navbar-toggler" aria-controls="navbarNav" aria-expanded="false" aria-label="Buka menu">
            <span class="hamburger-icon" aria-hidden="true">&#9776;</span>
        </button>
        <nav id="navbarNav" aria-label="Navigasi utama" role="navigation">
            <ul>
                <li><a class="nav-link" href="<?php echo $base; ?>index.php">Beranda</a></li>
                <li><a href="<?php echo $base; ?>transaksi/list.php">Daftar Transaksi</a></li>
                <li><a href="<?php echo $base; ?>laporan/index.php">Laporan</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (($_SESSION['role'] ?? '') === 'bendahara'): ?>
                        <li><a href="<?php echo $base; ?>transaksi/tambah.php">Tambah Transaksi</a></li>
                    <?php endif; ?>
                    <li class="nav-user"><span><?php echo htmlspecialchars((string) ($_SESSION['nama'] ?? $_SESSION['username'])); ?> (<?php echo htmlspecialchars((string) ($_SESSION['role'] ?? 'bendahara')); ?>)</span></li>
                    <li><a href="<?php echo $base; ?>auth/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?php echo $base; ?>auth/login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
<main>
