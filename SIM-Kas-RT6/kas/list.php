<?php
session_start();
$page_title = "Daftar Transaksi Kas";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$daftarTransaksi = $_SESSION['transaksi'] ?? [];
?>
<section>
    <h2>Daftar Riwayat Kas RT 6</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo $flash['type']; ?>" style="padding: 10px; background: <?php echo $flash['type'] === 'success' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $flash['type'] === 'success' ? '#155724' : '#721c24'; ?>; margin-bottom: 15px; border-radius: 4px;">
            <?php echo $flash['pesan']; ?>
        </p>
    <?php endif; ?>

    <div class="table-responsive">
        <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%; margin-top: 10px;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Jenis</th>
                    <th>Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daftarTransaksi)): ?>
                <tr>
                    <td colspan="5" style="text-align: center;">Belum ada data transaksi kas. Silakan tambah lewat menu "Tambah Transaksi".</td>
                </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($daftarTransaksi as $t): ?>
                    <tr>
                        <td style="text-align: center;"><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($t['tanggal']); ?></td>
                        <td><?php echo htmlspecialchars($t['keterangan']); ?></td>
                        <td style="text-align: center; color: <?php echo $t['jenis'] === 'masuk' ? 'green' : 'red'; ?>; font-weight: bold;">
                            <?php echo $t['jenis'] === 'masuk' ? 'Pemasukan' : 'Pengeluaran'; ?>
                        </td>
                        <td style="text-align: right;">Rp <?php echo number_format($t['jumlah'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>