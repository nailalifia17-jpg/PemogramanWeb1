<?php
require_once __DIR__ . '/../includes/auth.php';
$page_title = "Daftar Warga";
require_once __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$q = trim((string) ($_GET['q'] ?? ''));
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 5;
try {
    $db = koneksiDatabase();
    $totalData = hitungWarga($db, $q);
    $totalPage = max(1, (int) ceil($totalData / $perPage));
    $page = min($page, $totalPage);
    $daftarWarga = ambilWargaHalaman($db, $q, $perPage, ($page - 1) * $perPage);
} catch (Throwable $error) {
    tampilkanKesalahanDatabase($error);
}
?>

<div class="card shadow-sm border-0">
    <h2>Daftar Warga</h2>

    <?php if ($flash): ?>
        <p class="flash"><?php echo htmlspecialchars($flash['pesan']); ?></p>
    <?php endif; ?>

    <form method="get" class="search-box row g-2 align-items-end mb-3">
        <div class="col-md-9">
        <label for="search-input">Cari Nama / No. KK</label>
        <input type="text" id="search-input" name="q" class="form-control" value="<?php echo htmlspecialchars($q); ?>" placeholder="Ketik nama atau no. KK...">
        </div>
        <div class="col-md-3 d-flex gap-2"><button type="submit" class="btn btn-primary">Cari</button><a href="list.php" class="btn btn-outline-secondary">Reset</a></div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle" role="table">
            <thead>
                <tr><th scope="col">No. KK</th><th scope="col">Nama Kepala Keluarga</th><th scope="col">Alamat</th><th scope="col">No. HP</th><th scope="col">Status Kependudukan</th><th scope="col">Aksi</th></tr>
            </thead>
            <tbody>
                <?php if (empty($daftarWarga)): ?>
                    <tr><td colspan="6" class="text-center empty-state">Belum ada data warga. Silakan tambah lewat menu "Tambah Warga".</td></tr>
                <?php else: ?>
                    <?php foreach ($daftarWarga as $w): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($w['no_kk']); ?></td>
                            <td><?php echo htmlspecialchars($w['nama']); ?></td>
                            <td><?php echo htmlspecialchars($w['alamat']); ?></td>
                            <td><?php echo htmlspecialchars($w['no_hp']); ?></td>
                            <td><span class="badge text-bg-<?php echo $w['status'] === 'aktif' ? 'success' : 'secondary'; ?>" role="status" aria-label="Status kependudukan: <?php echo htmlspecialchars(ucfirst($w['status'])); ?>"><?php echo htmlspecialchars(ucfirst($w['status'])); ?></span></td>
                            <td>
                                <?php if (($_SESSION['role'] ?? '') === 'bendahara'): ?>
                                    <a href="edit.php?id=<?php echo (int) $w['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="post" action="../proses/hapus_warga.php" class="d-inline form-hapus"><input type="hidden" name="id" value="<?php echo (int) $w['id']; ?>"><button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button></form>
                                <?php else: ?>
                                    <span class="text-muted">Lihat saja</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($totalPage > 1): ?>
        <nav aria-label="Pagination warga" class="mt-3"><ul class="pagination">
            <?php for ($nomor = 1; $nomor <= $totalPage; $nomor++): ?>
                <li class="page-item<?php echo $nomor === $page ? ' active' : ''; ?>"><a class="page-link" href="?q=<?php echo urlencode($q); ?>&page=<?php echo $nomor; ?>"><?php echo $nomor; ?></a></li>
            <?php endfor; ?>
        </ul></nav>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
