<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/data.php';

$format = $_GET['format'] ?? 'excel';
$filterBulan = filter_var($_GET['bulan'] ?? '', FILTER_VALIDATE_INT) ?: null;
$filterTahun = filter_var($_GET['tahun'] ?? '', FILTER_VALIDATE_INT) ?: null;

try {
    $db = koneksiDatabase();
    $transaksi = ambilTransaksi($db, $filterBulan, $filterTahun);
} catch (Throwable $error) {
    tampilkanKesalahanDatabase($error);
}

if ($format !== 'excel') {
    header('Location: index.php?bulan=' . urlencode((string) ($filterBulan ?? '')) . '&tahun=' . urlencode((string) ($filterTahun ?? '')));
    exit;
}

$namaBulan = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
$filename = 'laporan-kas-rt6-' . date('Y-m-d') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
$output = fopen('php://output', 'w');
fwrite($output, "\xEF\xBB\xBF");
fputcsv($output, ['Laporan Kas Yayasan RQ']);
fputcsv($output, ['Tanggal', 'Bulan', 'Tahun', 'Kategori', 'Keterangan', 'Jenis', 'Jumlah', 'Metode']);
foreach ($transaksi as $item) {
    fputcsv($output, [$item['tanggal'], $namaBulan[(int) $item['bulan']] ?? '-', $item['tahun'], $item['kategori'], $item['keterangan'], $item['jenis'] === 'masuk' ? 'Pemasukan' : 'Pengeluaran', $item['jumlah'], ucfirst($item['metode'])]);
}
fclose($output);
exit;

