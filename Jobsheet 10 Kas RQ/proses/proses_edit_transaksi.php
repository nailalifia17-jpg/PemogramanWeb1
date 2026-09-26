<?php
require_once __DIR__ . '/../includes/auth.php';
wajibPeran(['bendahara']);
require_once __DIR__ . '/../includes/koneksi.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../transaksi/list.php'); exit; }
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
$tanggal = trim((string) ($_POST['tanggal'] ?? ''));
$keterangan = trim((string) ($_POST['keterangan'] ?? ''));
$jenis = $_POST['jenis'] ?? '';
$kategori = trim((string) ($_POST['kategori'] ?? ''));
$jumlah = filter_var($_POST['jumlah'] ?? null, FILTER_VALIDATE_INT);
$bulan = filter_var($_POST['bulan'] ?? null, FILTER_VALIDATE_INT);
$tahun = filter_var($_POST['tahun'] ?? null, FILTER_VALIDATE_INT);
$metode = $_POST['metode'] ?? '';
if (!$id || $tanggal === '' || $keterangan === '' || !in_array($jenis, ['masuk', 'keluar'], true) || $kategori === '' || $jumlah === false || $jumlah <= 0 || $bulan === false || $bulan < 1 || $bulan > 12 || $tahun === false || $tahun < 2000 || $tahun > 2100 || !in_array($metode, ['tunai', 'transfer'], true)) { $_SESSION['flash'] = ['pesan' => 'Data transaksi belum valid.']; header('Location: ../transaksi/list.php'); exit; }
try {
    $db = koneksiDatabase();
    $statement = $db->prepare('UPDATE transaksi SET tanggal = :tanggal, bulan = :bulan, tahun = :tahun, keterangan = :keterangan, jenis = :jenis, kategori = :kategori, jumlah = :jumlah, metode = :metode WHERE id = :id');
    $statement->execute([':tanggal' => $tanggal, ':bulan' => $bulan, ':tahun' => $tahun, ':keterangan' => $keterangan, ':jenis' => $jenis, ':kategori' => $kategori, ':jumlah' => $jumlah, ':metode' => $metode, ':id' => $id]);
    $_SESSION['flash'] = ['pesan' => 'Transaksi berhasil diperbarui.'];
} catch (Throwable $error) { $_SESSION['flash'] = ['pesan' => 'Transaksi gagal diperbarui.']; }
header('Location: ../transaksi/list.php'); exit;

