<?php
$page_title = "Tambah Transaksi";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>

<div class="card shadow-sm border-0">
    <h2>Tambah Transaksi Kas</h2>

    <?php if ($flash): ?>
        <p class="flash"><?php echo htmlspecialchars($flash['pesan']); ?></p>
    <?php endif; ?>

    <form id="form-tambah" method="post" action="../proses/proses_tambah_transaksi.php" class="row g-3">
        <div class="col-md-6">
            <label for="tanggal" class="form-label">Tanggal</label>
            <input type="date" id="tanggal" name="tanggal" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
        </div>
        <div class="col-md-3">
            <label for="bulan" class="form-label">Bulan</label>
            <select id="bulan" name="bulan" class="form-select" required>
                <?php for ($bulan = 1; $bulan <= 12; $bulan++): ?>
                    <option value="<?php echo $bulan; ?>"<?php echo (int) date('n') === $bulan ? ' selected' : ''; ?>><?php echo date('F', mktime(0, 0, 0, $bulan, 1)); ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="tahun" class="form-label">Tahun</label>
            <input type="number" id="tahun" name="tahun" class="form-control" min="2000" max="2100" value="<?php echo date('Y'); ?>" required>
        </div>
        <div class="col-md-6">
            <label for="keterangan" class="form-label">Keterangan</label>
            <input type="text" id="keterangan" name="keterangan" class="form-control" placeholder="Contoh: Iuran warga bulan Agustus" required>
        </div>
        <div class="col-md-6">
            <label for="jenis" class="form-label">Jenis Transaksi</label>
            <select id="jenis" name="jenis" class="form-select" required>
                <option value="masuk">Kas Masuk</option>
                <option value="keluar">Kas Keluar</option>
            </select>
        </div>
        <div class="col-md-6">
            <label for="jumlah" class="form-label">Jumlah (Rp)</label>
            <input type="number" id="jumlah" name="jumlah" class="form-control" min="1" step="1" required>
        </div>
        <div class="col-md-6">
            <label for="metode" class="form-label">Metode Pembayaran</label>
            <select id="metode" name="metode" class="form-select" required>
                <option value="tunai">Tunai</option>
                <option value="transfer">Transfer</option>
            </select>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>