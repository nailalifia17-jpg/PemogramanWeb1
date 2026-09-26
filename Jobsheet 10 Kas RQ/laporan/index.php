<?php
require_once __DIR__ . '/../includes/auth.php';
$page_title = "Laporan Kas";
require_once __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/header.php';

$filterBulan = filter_var($_GET['bulan'] ?? '', FILTER_VALIDATE_INT) ?: '';
$filterTahun = filter_var($_GET['tahun'] ?? '', FILTER_VALIDATE_INT) ?: '';
$namaBulan = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
try {
    $db = koneksiDatabase();
    $transaksiTerfilter = ambilTransaksi($db, $filterBulan !== '' ? $filterBulan : null, $filterTahun !== '' ? $filterTahun : null);
} catch (Throwable $error) {
    tampilkanKesalahanDatabase($error);
}

foreach ($transaksiTerfilter as &$transaksi) {
    $transaksi['bulan_tampil'] = (int) $transaksi['bulan'];
    $transaksi['tahun_tampil'] = (int) $transaksi['tahun'];
}
unset($transaksi);

$totalMasuk = 0;
$totalKeluar = 0;
foreach ($transaksiTerfilter as $t) {
    if ($t['jenis'] === 'masuk') $totalMasuk += (int) $t['jumlah'];
    if ($t['jenis'] === 'keluar') $totalKeluar += (int) $t['jumlah'];
}
$saldoAwal = 0;
$saldoAkhir = $saldoAwal + $totalMasuk - $totalKeluar;
?>

<div class="card shadow-sm border-0">
    <h2>Laporan Kas Bulanan</h2>

    <form method="get" class="row g-3 align-items-end mb-4">
        <div class="col-md-4">
            <label for="laporan-bulan" class="form-label">Bulan</label>
            <select id="laporan-bulan" name="bulan" class="form-select">
                <option value="">Semua bulan</option>
                <?php foreach ($namaBulan as $nomor => $nama): ?>
                    <option value="<?php echo $nomor; ?>"<?php echo (string) $filterBulan === (string) $nomor ? ' selected' : ''; ?>><?php echo $nama; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="laporan-tahun" class="form-label">Tahun</label>
            <input type="number" id="laporan-tahun" name="tahun" class="form-control" min="2000" max="2100" value="<?php echo htmlspecialchars((string) $filterTahun); ?>" placeholder="Semua tahun">
        </div>
        <div class="col-md-5 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Tampilkan</button>
            <a href="index.php" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="kop-laporan print-only">
        <h2>Yayasan Rumah Quran</h2>
        <p>Laporan Keuangan Pemasukan dan Pengeluaran</p>
        <p>Periode: <?php echo $filterBulan !== '' ? $namaBulan[$filterBulan] . ' ' : ''; ?><?php echo $filterTahun !== '' ? $filterTahun : 'Semua Tahun'; ?></p>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-4">
        <button type="button" id="btn-cetak" class="btn btn-primary" onclick="window.print()">Download PDF</button>
        <a class="btn btn-success" href="export.php?format=excel&amp;bulan=<?php echo urlencode((string) $filterBulan); ?>&amp;tahun=<?php echo urlencode((string) $filterTahun); ?>">Download Excel</a>
    </div>

    <h3>A. Pemasukan (Kas Masuk)</h3>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle" role="table">
            <thead><tr><th scope="col">No.</th><th scope="col">Tanggal</th><th scope="col">Bulan</th><th scope="col">Tahun</th><th scope="col">Kategori</th><th scope="col">Keterangan</th><th scope="col" class="text-end">Jumlah</th></tr></thead>
            <tbody>
                <?php $no = 1; $ada = false; ?>
                <?php foreach ($transaksiTerfilter as $t): if ($t['jenis'] === 'masuk'): $ada = true; ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($t['tanggal']); ?></td>
                        <td><?php echo htmlspecialchars($namaBulan[$t['bulan_tampil']] ?? '-'); ?></td>
                        <td><?php echo $t['tahun_tampil'] ?: '-'; ?></td>
                        <td><?php echo htmlspecialchars($t['kategori']); ?></td>
                        <td><?php echo htmlspecialchars($t['keterangan']); ?></td>
                        <td class="text-end">Rp <?php echo number_format($t['jumlah'], 0, ',', '.'); ?></td>
                    </tr>
                <?php endif; endforeach; ?>
                <?php if (!$ada): ?><tr><td colspan="7" class="text-center">Belum ada pemasukan.</td></tr><?php endif; ?>
            </tbody>
            <tfoot><tr><td colspan="6">Subtotal Pemasukan</td><td class="text-end">Rp <?php echo number_format($totalMasuk, 0, ',', '.'); ?></td></tr></tfoot>
        </table>
    </div>

    <h3>B. Pengeluaran (Kas Keluar)</h3>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle" role="table">
            <thead><tr><th scope="col">No.</th><th scope="col">Tanggal</th><th scope="col">Bulan</th><th scope="col">Tahun</th><th scope="col">Kategori</th><th scope="col">Keterangan</th><th scope="col" class="text-end">Jumlah</th></tr></thead>
            <tbody>
                <?php $no = 1; $ada = false; ?>
                <?php foreach ($transaksiTerfilter as $t): if ($t['jenis'] === 'keluar'): $ada = true; ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($t['tanggal']); ?></td>
                        <td><?php echo htmlspecialchars($namaBulan[$t['bulan_tampil']] ?? '-'); ?></td>
                        <td><?php echo $t['tahun_tampil'] ?: '-'; ?></td>
                        <td><?php echo htmlspecialchars($t['kategori']); ?></td>
                        <td><?php echo htmlspecialchars($t['keterangan']); ?></td>
                        <td class="text-end">Rp <?php echo number_format($t['jumlah'], 0, ',', '.'); ?></td>
                    </tr>
                <?php endif; endforeach; ?>
                <?php if (!$ada): ?><tr><td colspan="7" class="text-center">Belum ada pengeluaran.</td></tr><?php endif; ?>
            </tbody>
            <tfoot><tr><td colspan="6">Subtotal Pengeluaran</td><td class="text-end">Rp <?php echo number_format($totalKeluar, 0, ',', '.'); ?></td></tr></tfoot>
        </table>
    </div>

    <h3>C. Ringkasan Saldo</h3>
    <div class="ringkasan-laporan">
        <div class="kas-card saldo-awal"><h4>Saldo Awal</h4><p>Rp <?php echo number_format($saldoAwal, 0, ',', '.'); ?></p></div>
        <div class="kas-card pemasukan"><h4>Total Pemasukan</h4><p>Rp <?php echo number_format($totalMasuk, 0, ',', '.'); ?></p></div>
        <div class="kas-card pengeluaran"><h4>Total Pengeluaran</h4><p>Rp <?php echo number_format($totalKeluar, 0, ',', '.'); ?></p></div>
        <div class="kas-card saldo-akhir"><h4>Saldo Akhir</h4><p>Rp <?php echo number_format($saldoAkhir, 0, ',', '.'); ?></p></div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
