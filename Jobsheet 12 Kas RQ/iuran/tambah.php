<?php
require_once __DIR__ . '/../includes/auth.php';
wajibPeran(['bendahara']);
$page_title = 'Catat Iuran Anggota';
require_once __DIR__ . '/../includes/koneksi.php';

try {
    $db = koneksiDatabase();
    $anggotaAktif = $db->query("SELECT id, no_kk, nama FROM anggota WHERE status = 'aktif' ORDER BY nama ASC")->fetchAll();
} catch (Throwable $error) {
    tampilkanKesalahanDatabase($error);
}

include __DIR__ . '/../includes/header.php';
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<div class="card shadow-sm border-0">
    <h2>Catat Iuran Anggota</h2>
    <?php if ($flash): ?><p class="flash flash-error"><?php echo e($flash['pesan']); ?></p><?php endif; ?>
    <?php if (!$anggotaAktif): ?>
        <div class="empty-state">
            <p>Belum ada anggota aktif. Tambahkan anggota terlebih dahulu.</p>
            <a href="../anggota/tambah.php" class="btn btn-primary">Tambah Anggota</a>
        </div>
    <?php else: ?>
        <form id="form-tambah" method="post" action="proses_tambah.php" class="row g-3">
            <?php echo csrf_field(); ?>
            <div class="col-md-6">
                <label for="anggota_id" class="form-label">Anggota</label>
                <select id="anggota_id" name="anggota_id" class="form-select" required>
                    <option value="">Pilih anggota</option>
                    <?php foreach ($anggotaAktif as $anggota): ?>
                        <option value="<?php echo (int) $anggota['id']; ?>"><?php echo e($anggota['nama']); ?> (<?php echo e($anggota['no_kk']); ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="bulan" class="form-label">Bulan Iuran</label>
                <select id="bulan" name="bulan" class="form-select" required>
                    <?php foreach ([1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $nomor => $namaBulan): ?>
                        <option value="<?php echo $nomor; ?>"<?php echo (int) date('n') === $nomor ? ' selected' : ''; ?>><?php echo e($namaBulan); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="tahun" class="form-label">Tahun</label>
                <input type="number" id="tahun" name="tahun" class="form-control" min="2000" max="2100" value="<?php echo date('Y'); ?>" required>
            </div>
            <div class="col-md-4">
                <label for="tanggal_bayar" class="form-label">Tanggal Pembayaran</label>
                <input type="date" id="tanggal_bayar" name="tanggal_bayar" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="col-md-4">
                <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                <input type="number" id="jumlah" name="jumlah" class="form-control" min="1" step="1" required>
            </div>
            <div class="col-md-4">
                <label for="metode" class="form-label">Metode Pembayaran</label>
                <select id="metode" name="metode" class="form-select" required>
                    <option value="tunai">Tunai</option>
                    <option value="transfer">Transfer</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Simpan Iuran</button>
                <a href="list.php" class="btn btn-outline-secondary ms-2">Batal</a>
            </div>
        </form>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
