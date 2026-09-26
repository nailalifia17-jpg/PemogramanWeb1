<?php
$page_title = 'Login';
require_once __DIR__ . '/../includes/koneksi.php';
include __DIR__ . '/../includes/header.php';
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<div class="card shadow-sm border-0 auth-card">
    <h2>Login Petugas</h2>
    <?php if ($flash): ?><p class="flash"><?php echo htmlspecialchars($flash['pesan']); ?></p><?php endif; ?>
    <form method="post" action="proses_login.php" class="row g-3">
        <div class="col-12"><label for="username" class="form-label">Username</label><input type="text" id="username" name="username" class="form-control" required autofocus></div>
        <div class="col-12"><label for="password" class="form-label">Password</label><input type="password" id="password" name="password" class="form-control" required></div>
        <div class="col-12"><button type="submit" class="btn btn-primary">Login</button><a href="register.php" class="btn btn-outline-secondary ms-2">Daftar</a></div>
    </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>

