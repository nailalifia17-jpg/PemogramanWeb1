<?php
$page_title = "Daftar Warga";
require_once __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
try {
    $db = koneksiDatabase();
    $daftarWarga = ambilWarga($db);
} catch (Throwable $error) {
    tampilkanKesalahanDatabase($error);
}
?>

<div class="card shadow-sm border-0">
    <h2>Daftar Warga</h2>

    <?php if ($flash): ?>
        <p class="flash"><?php echo htmlspecialchars($flash['pesan']); ?></p>
    <?php endif; ?>

    <div class="search-box">
        <label for="search-input">Cari Nama / No. KK</label>
        <input type="text" id="search-input" class="form-control" placeholder="Ketik nama atau no. KK...">
    </div>

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
                                <button type="button" class="btn btn-sm btn-outline-primary btn-edit">Edit</button>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-hapus">Hapus</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>