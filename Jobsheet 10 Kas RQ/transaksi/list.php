<?php
require_once __DIR__ . '/../includes/auth.php';
$page_title = "Daftar Transaksi";
require_once __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$filterBulan = filter_var($_GET['bulan'] ?? '', FILTER_VALIDATE_INT) ?: '';
$filterTahun = filter_var($_GET['tahun'] ?? '', FILTER_VALIDATE_INT) ?: '';
$q = trim((string) ($_GET['q'] ?? ''));
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 5;
$namaBulan = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];

try {
    $db = koneksiDatabase();
    $totalData = hitungTransaksi($db, $q, $filterBulan !== '' ? $filterBulan : null, $filterTahun !== '' ? $filterTahun : null);
    $totalPage = max(1, (int) ceil($totalData / $perPage));
    $page = min($page, $totalPage);
    $daftarTransaksi = ambilTransaksiHalaman($db, $q, $filterBulan !== '' ? $filterBulan : null, $filterTahun !== '' ? $filterTahun : null, $perPage, ($page - 1) * $perPage);
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

    <form method="get" class="search-box row g-2 align-items-end mb-3">
        <input type="hidden" name="bulan" value="<?php echo htmlspecialchars((string) $filterBulan); ?>"><input type="hidden" name="tahun" value="<?php echo htmlspecialchars((string) $filterTahun); ?>">
        <div class="col-md-9">
        <label for="search-input">Cari Transaksi</label>
        <input type="text" id="search-input" name="q" class="form-control" value="<?php echo htmlspecialchars($q); ?>" placeholder="Cari keterangan transaksi..."></div>
        <div class="col-md-3 d-flex gap-2"><button type="submit" class="btn btn-primary">Cari</button><a href="list.php" class="btn btn-outline-secondary">Reset</a></div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle" role="table">
            <thead>
                <tr>
                    <th scope="col">Tanggal</th><th scope="col">Bulan</th><th scope="col">Tahun</th><th scope="col">Kategori</th><th scope="col">Keterangan</th><th scope="col">Jenis</th><th scope="col">Jumlah</th><th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daftarTransaksi)): ?>
                    <tr><td colspan="8" class="text-center empty-state">Belum ada transaksi pemasukan atau pengeluaran.</td></tr>
                <?php else: ?>
                    <?php foreach ($daftarTransaksi as $t): ?>
                        <?php $bulanTransaksi = (int) $t['bulan']; $tahunTransaksi = (int) $t['tahun']; ?>
                        <tr>
                            <td><?php echo htmlspecialchars($t['tanggal']); ?></td>
                            <td><?php echo htmlspecialchars($namaBulan[$bulanTransaksi] ?? '-'); ?></td>
                            <td><?php echo $tahunTransaksi ?: '-'; ?></td>
                            <td><?php echo htmlspecialchars($t['kategori']); ?></td>
                            <td><?php echo htmlspecialchars($t['keterangan']); ?></td>
                            <td><span class="badge text-bg-<?php echo $t['jenis'] === 'masuk' ? 'success' : 'danger'; ?>" role="status"><?php echo $t['jenis'] === 'masuk' ? 'Pemasukan' : 'Pengeluaran'; ?></span></td>
                            <td>Rp <?php echo number_format($t['jumlah'], 0, ',', '.'); ?></td>
                            <td>
                                <?php if (($_SESSION['role'] ?? '') === 'bendahara'): ?>
                                    <a href="edit.php?id=<?php echo (int) $t['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="post" action="../proses/hapus_transaksi.php" class="d-inline form-hapus"><input type="hidden" name="id" value="<?php echo (int) $t['id']; ?>"><button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button></form>
                                <?php else: ?>
                                    <span class="text-muted">Lihat saja</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($totalPage > 1): ?>
        <nav aria-label="Pagination transaksi" class="mt-3"><ul class="pagination">
            <?php for ($nomor = 1; $nomor <= $totalPage; $nomor++): ?>
                <li class="page-item<?php echo $nomor === $page ? ' active' : ''; ?>"><a class="page-link" href="?q=<?php echo urlencode($q); ?>&bulan=<?php echo urlencode((string) $filterBulan); ?>&tahun=<?php echo urlencode((string) $filterTahun); ?>&page=<?php echo $nomor; ?>"><?php echo $nomor; ?></a></li>
            <?php endfor; ?>
        </ul></nav>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
