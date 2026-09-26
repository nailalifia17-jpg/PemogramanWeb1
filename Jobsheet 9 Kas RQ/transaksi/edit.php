<?php
$page_title = 'Edit Transaksi';
require_once __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/header.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
try {
    $db = koneksiDatabase();
    $transaksi = $id ? ambilTransaksiDenganId($db, $id) : null;
} catch (Throwable $error) {
    tampilkanKesalahanDatabase($error);
}
if (!$transaksi) {
    $_SESSION['flash'] = ['pesan' => 'Transaksi tidak ditemukan.'];
    header('Location: list.php');
    exit;
}
?>
<div class="card shadow-sm border-0">
    <h2>Edit Transaksi Kas</h2>
    <form id="form-tambah" method="post" action="../proses/proses_edit_transaksi.php" class="row g-3">
        <input type="hidden" name="id" value="<?php echo (int) $transaksi['id']; ?>">
        <div class="col-md-6"><label for="tanggal" class="form-label">Tanggal</label><input type="date" id="tanggal" name="tanggal" class="form-control" value="<?php echo htmlspecialchars($transaksi['tanggal']); ?>" required></div>
        <div class="col-md-3"><label for="bulan" class="form-label">Bulan</label><select id="bulan" name="bulan" class="form-select" required><?php for ($bulan = 1; $bulan <= 12; $bulan++): ?><option value="<?php echo $bulan; ?>"<?php echo (int) $transaksi['bulan'] === $bulan ? ' selected' : ''; ?>><?php echo date('F', mktime(0, 0, 0, $bulan, 1)); ?></option><?php endfor; ?></select></div>
        <div class="col-md-3"><label for="tahun" class="form-label">Tahun</label><input type="number" id="tahun" name="tahun" class="form-control" min="2000" max="2100" value="<?php echo (int) $transaksi['tahun']; ?>" required></div>
        <div class="col-md-6"><label for="keterangan" class="form-label">Keterangan</label><input type="text" id="keterangan" name="keterangan" class="form-control" value="<?php echo htmlspecialchars($transaksi['keterangan']); ?>" required></div>
        <div class="col-md-6"><label for="jenis" class="form-label">Jenis Transaksi</label><select id="jenis" name="jenis" class="form-select" required><option value="masuk"<?php echo $transaksi['jenis'] === 'masuk' ? ' selected' : ''; ?>>Kas Masuk</option><option value="keluar"<?php echo $transaksi['jenis'] === 'keluar' ? ' selected' : ''; ?>>Kas Keluar</option></select></div>
        <div class="col-md-6"><label for="jumlah" class="form-label">Jumlah (Rp)</label><input type="number" id="jumlah" name="jumlah" class="form-control" min="1" value="<?php echo (int) $transaksi['jumlah']; ?>" required></div>
        <div class="col-md-6"><label for="metode" class="form-label">Metode Pembayaran</label><select id="metode" name="metode" class="form-select" required><option value="tunai"<?php echo $transaksi['metode'] === 'tunai' ? ' selected' : ''; ?>>Tunai</option><option value="transfer"<?php echo $transaksi['metode'] === 'transfer' ? ' selected' : ''; ?>>Transfer</option></select></div>
        <div class="col-12"><button type="submit" class="btn btn-primary">Simpan Perubahan</button><a href="list.php" class="btn btn-outline-secondary ms-2">Batal</a></div>
    </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>

