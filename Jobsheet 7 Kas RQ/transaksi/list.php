<?php
$page_title = "Daftar Transaksi";
require_once __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$filterBulan = filter_var($_GET['bulan'] ?? '', FILTER_VALIDATE_INT) ?: '';
$filterTahun = filter_var($_GET['tahun'] ?? '', FILTER_VALIDATE_INT) ?: '';
$namaBulan = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];

try {
    $db = koneksiDatabase();
    $daftarTransaksi = ambilTransaksi($db, $filterBulan !== '' ? $filterBulan : null, $filterTahun !== '' ? $filterTahun : null);
} catch (Throwable $error) {
    tampilkanKesalahanDatabase($error);
}
?>

<div class="card shadow-sm border-0">
    <h2>Daftar Transaksi Kas</h2>

    <?php if ($flash): ?>
        <p class="flash"><?php echo htmlspecialchars($flash['pesan']); ?></p>
    <?php endif; ?>

    <form method="get" class="row g-3 align-items-end mb-4">
        <div class="col-md-4">
            <label for="filter-bulan" class="form-label">Bulan</label>
            <select id="filter-bulan" name="bulan" class="form-select">
                <option value="">Semua bulan</option>
                <?php foreach ($namaBulan as $nomor => $nama): ?>
                    <option value="<?php echo $nomor; ?>"<?php echo (string) $filterBulan === (string) $nomor ? ' selected' : ''; ?>><?php echo $nama; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="filter-tahun" class="form-label">Tahun</label>
            <input type="number" id="filter-tahun" name="tahun" class="form-control" min="2000" max="2100" value="<?php echo htmlspecialchars((string) $filterTahun); ?>" placeholder="Semua tahun">
        </div>
        <div class="col-md-5 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Tampilkan</button>
            <a href="list.php" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="search-box">
        <label for="search-input">Cari Transaksi</label>
        <input type="text" id="search-input" class="form-control" placeholder="Cari keterangan transaksi...">
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle" role="table">
            <thead>
                <tr>
                    <th scope="col">Tanggal</th><th scope="col">Bulan</th><th scope="col">Tahun</th><th scope="col">Keterangan</th><th scope="col">Role Kas</th><th scope="col">Jumlah</th><th scope="col">Metode</th><th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daftarTransaksi)): ?>
                    <tr><td colspan="8" class="text-center empty-state">Belum ada transaksi. Silakan tambah lewat menu "Tambah Transaksi".</td></tr>
                <?php else: ?>
                    <?php foreach ($daftarTransaksi as $t): ?>
                        <?php
                        $tanggal = strtotime($t['tanggal']);
                        $bulanTransaksi = (int) ($t['bulan'] ?? ($tanggal ? date('n', $tanggal) : 0));
                        $tahunTransaksi = (int) ($t['tahun'] ?? ($tanggal ? date('Y', $tanggal) : 0));
                        if (($filterBulan !== '' && $bulanTransaksi !== $filterBulan) || ($filterTahun !== '' && $tahunTransaksi !== $filterTahun)) continue;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($t['tanggal']); ?></td>
                            <td><?php echo htmlspecialchars($namaBulan[$bulanTransaksi] ?? '-'); ?></td>
                            <td><?php echo $tahunTransaksi ?: '-'; ?></td>
                            <td><?php echo htmlspecialchars($t['keterangan']); ?></td>
                            <td><span class="badge text-bg-<?php echo $t['jenis'] === 'masuk' ? 'success' : 'danger'; ?>" role="status"><?php echo $t['jenis'] === 'masuk' ? 'Kas Masuk' : 'Kas Keluar'; ?></span></td>
                            <td>Rp <?php echo number_format($t['jumlah'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($t['metode'])); ?></td>
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
