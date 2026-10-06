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

<section class="welcome">
    <div>
        <h1>Selamat Datang di Sistem Informasi Kas Yayasan Rumah Quran Mumtazah</h1>
        <p>Pencatatan pemasukan dan pengeluaran yayasan yang rapi, transparan, dan siap dilaporkan secara berkala.</p>
    </div>
</section>

<section class="summary">
    <h2 class="section-title">Ringkasan Keuangan</h2>
    <div class="row g-4">
        <div class="col-12 col-md-4">
            <div class="summary-card masuk">
                <div class="summary-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 7v10M8 13l4 4 4-4"/></svg></div>
                <h3>Total Pemasukan</h3>
                <p>Rp <?php echo number_format($totalMasuk, 0, ',', '.'); ?></p>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="summary-card keluar">
                <div class="summary-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 17V7M8 11l4-4 4 4"/></svg></div>
                <h3>Total Pengeluaran</h3>
                <p>Rp <?php echo number_format($totalKeluar, 0, ',', '.'); ?></p>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="summary-card saldo">
                <div class="summary-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="3"/><circle cx="12" cy="12" r="3"/></svg></div>
                <h3>Saldo Kas Akhir</h3>
                <p>Rp <?php echo number_format($saldo, 0, ',', '.'); ?></p>
            </div>
        </div>
    </div>
</section>

<section class="card">
    <h2 class="section-title">Aktivitas Terbaru</h2>
    <ul class="activity-list">
        <?php if (empty($daftarTransaksi)): ?>
            <li class="activity-empty">Belum ada transaksi. Transaksi yang dicatat akan muncul di sini.</li>
        <?php else: ?>
            <?php foreach (array_slice(array_reverse($daftarTransaksi), 0, 3) as $t): ?>
                <li>
                    <span class="badge badge-<?php echo $t['jenis']; ?>"><?php echo $t['jenis'] === 'masuk' ? 'Masuk' : 'Keluar'; ?></span>
                    <span class="activity-text">
                        <?php echo e($t['keterangan']); ?> &mdash;
                        <strong>Rp <?php echo number_format($t['jumlah'], 0, ',', '.'); ?></strong>
                    </span>
                    <time><?php echo e($t['tanggal']); ?></time>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>