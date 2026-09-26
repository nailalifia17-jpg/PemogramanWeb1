<?php
$page_title = 'Daftar Akun';
require_once __DIR__ . '/../includes/koneksi.php';
include __DIR__ . '/../includes/header.php';
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<div class="card shadow-sm border-0 auth-card">
    <h2>Daftar Akun Pengurus</h2>
    <?php if ($flash): ?><p class="flash"><?php echo htmlspecialchars($flash['pesan']); ?></p><?php endif; ?>
    <form method="post" action="proses_register.php" class="row g-3">
        <div class="col-12"><label for="nama" class="form-label">Nama</label><input type="text" id="nama" name="nama" class="form-control" required></div>
        <div class="col-12"><label for="username" class="form-label">Username</label><input type="text" id="username" name="username" class="form-control" required></div>
        <div class="col-12"><label for="role" class="form-label">Peran</label><select id="role" name="role" class="form-select" required><option value="bendahara">Bendahara</option><option value="ketua">Ketua RT</option></select></div>
        <div class="col-md-6"><label for="password" class="form-label">Password</label><input type="password" id="password" name="password" class="form-control" minlength="6" required></div>
        <div class="col-md-6"><label for="konfirmasi_password" class="form-label">Konfirmasi Password</label><input type="password" id="konfirmasi_password" name="konfirmasi_password" class="form-control" minlength="6" required></div>
        <div class="col-12"><button type="submit" class="btn btn-primary">Daftar</button><a href="login.php" class="btn btn-outline-secondary ms-2">Sudah punya akun?</a></div>
    </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>

