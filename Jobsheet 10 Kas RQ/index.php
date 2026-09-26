<?php
require_once __DIR__ . '/includes/auth.php';
$page_title = "Beranda";
require_once __DIR__ . '/includes/data.php';
include __DIR__ . '/includes/header.php';

try {
    $db = koneksiDatabase();
    $daftarTransaksi = ambilTransaksi($db);
    $ringkasan = ringkasanTransaksi($db);
} catch (Throwable $error) {
    tampilkanKesalahanDatabase($error);
}

$totalMasuk = $ringkasan['total_masuk'];
$totalKeluar = $ringkasan['total_keluar'];
$saldo = $totalMasuk - $totalKeluar;
?>

<div class="welcome">
    <h2>Selamat Datang di Sistem Informasi Kas Yayasan Rumah Quran</h2>
    <p>Pencatatan pemasukan dan pengeluaran yayasan yang rapi, transparan, dan siap dilaporkan secara berkala.</p>
</div>

<div class="summary">
    <h2>Ringkasan Keuangan</h2>
    <div class="summary-grid">
        <div class="summary-card masuk">
            <h3>Total Pemasukan</h3>
            <p>Rp <?php echo number_format($totalMasuk, 0, ',', '.'); ?></p>
        </div>
        <div class="summary-card keluar">
            <h3>Total Pengeluaran</h3>
            <p>Rp <?php echo number_format($totalKeluar, 0, ',', '.'); ?></p>
        </div>
        <div class="summary-card saldo">
            <h3>Saldo Kas RT Akhir</h3>
            <p>Rp <?php echo number_format($saldo, 0, ',', '.'); ?></p>
        </div>
    </div>
</div>

<div class="card">
    <h2>Aktivitas Terbaru</h2>
    <ul class="activity-list">
        <?php if (empty($daftarTransaksi)): ?>
            <li>Belum ada transaksi.</li>
        <?php else: ?>
            <?php foreach (array_slice(array_reverse($daftarTransaksi), 0, 3) as $t): ?>
                <li>
                    <span class="badge badge-<?php echo $t['jenis']; ?>"><?php echo $t['jenis'] === 'masuk' ? 'Masuk' : 'Keluar'; ?></span>
                    <?php echo htmlspecialchars($t['keterangan']); ?> â€”
                    Rp <?php echo number_format($t['jumlah'], 0, ',', '.'); ?>
                    <time><?php echo htmlspecialchars($t['tanggal']); ?></time>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
