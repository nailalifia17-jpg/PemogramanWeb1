<?php
session_start();
$page_title = "Tambah Transaksi Kas";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<section class="card shadow-sm border-0">
    <h2>Tambah Transaksi Kas Yayasan RQ</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo $flash['type']; ?>" style="padding: 10px; background: <?php echo $flash['type'] === 'success' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $flash['type'] === 'success' ? '#155724' : '#721c24'; ?>; margin-bottom: 15px; border-radius: 4px;">
            <?php echo $flash['pesan']; ?>
        </p>
    <?php endif; ?>

    <form id="form-tambah" method="post" action="../proses/proses_tambah_transaksi.php" class="row g-3">
        <p>
            <label for="tanggal">Tanggal Transaksi</label><br>
            <input type="date" id="tanggal" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>
        </p>
        <p>
            <label for="keterangan">Keterangan / Uraian</label><br>
            <input type="text" id="keterangan" name="keterangan" placeholder="Contoh: Iuran Warga Blk A No. 5" required style="width: 100%; max-width: 400px; padding: 6px;">
        </p>
        <p>
            <label for="jenis">Jenis Transaksi</label><br>
            <select id="jenis" name="jenis" required style="padding: 6px;">
                <option value="masuk">Pemasukan</option>
                <option value="keluar">Pengeluaran</option>
            </select>
        </p>
        <p>
            <label for="jumlah">Jumlah (Rp)</label><br>
            <input type="number" id="jumlah" name="jumlah" min="1" step="1" placeholder="Contoh: 50000" required style="padding: 6px;">
        </p>
        <input type="hidden" name="bulan" value="<?php echo date('n'); ?>">
        <input type="hidden" name="tahun" value="<?php echo date('Y'); ?>">
        <input type="hidden" name="metode" value="tunai">
        <p>
            <button type="submit" style="padding: 8px 15px; cursor: pointer;">Simpan Transaksi</button>
        </p>
    </form>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
