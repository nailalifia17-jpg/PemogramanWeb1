<?php
require_once __DIR__ . '/../includes/auth.php';
$page_title = 'Iuran Anggota';
require_once __DIR__ . '/../includes/koneksi.php';

try {
    $db = koneksiDatabase();
    $daftarIuran = $db->query('SELECT i.id, i.bulan, i.tahun, i.tanggal_bayar, i.jumlah, i.metode, a.no_anggota, a.nama FROM iuran i JOIN anggota a ON a.id = i.anggota_id ORDER BY i.tahun DESC, i.bulan DESC, i.tanggal_bayar DESC, i.id DESC')->fetchAll();
    $ringkasan = $db->query('SELECT COUNT(*) AS jumlah_pembayaran, COALESCE(SUM(jumlah), 0) AS total_iuran FROM iuran')->fetch();
} catch (Throwable $error) {
    tampilkanKesalahanDatabase($error);
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$namaBulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
include __DIR__ . '/../includes/header.php';
?>
<div class="card shadow-sm border-0">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
        <div>
            <h2>Iuran Anggota</h2>
            <p class="text-muted mb-0"><?php echo (int) $ringkasan['jumlah_pembayaran']; ?> pembayaran tercatat, total Rp <?php echo number_format((int) $ringkasan['total_iuran'], 0, ',', '.'); ?>.</p>
        </div>
        <?php if (($_SESSION['role'] ?? '') === 'bendahara'): ?>
            <a href="tambah.php" class="btn btn-primary">Catat Iuran</a>
        <?php endif; ?>
    </div>
    <?php if ($flash): ?><p class="flash"><?php echo e($flash['pesan']); ?></p><?php endif; ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr><th scope="col">Anggota</th><th scope="col">Periode</th><th scope="col">Tanggal Bayar</th><th scope="col">Jumlah</th><th scope="col">Metode</th></tr>
            </thead>
            <tbody>
                <?php if (!$daftarIuran): ?>
                    <tr><td colspan="5" class="text-center empty-state">Belum ada pembayaran iuran.</td></tr>
                <?php else: ?>
                    <?php foreach ($daftarIuran as $iuran): ?>
                        <tr>
                            <td><?php echo e($iuran['nama']); ?><br><small class="text-muted"><?php echo e($iuran['no_anggota']); ?></small></td>
                            <td><?php echo e($namaBulan[(int) $iuran['bulan']] ?? '-'); ?> <?php echo (int) $iuran['tahun']; ?></td>
                            <td><?php echo e($iuran['tanggal_bayar']); ?></td>
                            <td>Rp <?php echo number_format((int) $iuran['jumlah'], 0, ',', '.'); ?></td>
                            <td><?php echo e(ucfirst($iuran['metode'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
