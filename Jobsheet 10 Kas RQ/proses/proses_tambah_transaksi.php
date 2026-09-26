<?php
require_once __DIR__ . '/../includes/auth.php';
wajibPeran(['bendahara']);
require_once __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = trim((string) ($_POST['tanggal'] ?? ''));
    $keterangan = trim((string) ($_POST['keterangan'] ?? ''));
    $jenis = $_POST['jenis'] ?? '';
    $kategori = trim((string) ($_POST['kategori'] ?? ''));
    $jumlah = filter_var($_POST['jumlah'] ?? null, FILTER_VALIDATE_INT);
    $bulan = filter_var($_POST['bulan'] ?? null, FILTER_VALIDATE_INT);
    $tahun = filter_var($_POST['tahun'] ?? null, FILTER_VALIDATE_INT);
    $metode = $_POST['metode'] ?? 'tunai';

    $kategoriValid = $jenis === 'masuk' ? ['Sedekah Subuh', 'Infaq', 'Zakat Maal', 'Wakaf Pembebasan Tanah', 'Infaq TPQ', 'Lain-lain'] : ['Biaya Operasional', 'Bisyaroh Pengajar', 'Biaya Sewa Ruko', 'Lain-lain'];
    if ($tanggal === '' || $keterangan === '' || !in_array($jenis, ['masuk', 'keluar'], true) || !in_array($kategori, $kategoriValid, true) || $jumlah === false || $jumlah <= 0 || $bulan === false || $bulan < 1 || $bulan > 12 || $tahun === false || $tahun < 2000 || $tahun > 2100 || !in_array($metode, ['tunai', 'transfer'], true)) {
        $_SESSION['flash'] = ['pesan' => 'Data transaksi belum valid. Periksa kembali isian formulir.'];
        header('Location: ../transaksi/tambah.php');
        exit;
    }

    try {
        $db = koneksiDatabase();
        $statement = $db->prepare('INSERT INTO transaksi (tanggal, bulan, tahun, keterangan, jenis, kategori, jumlah, metode) VALUES (:tanggal, :bulan, :tahun, :keterangan, :jenis, :kategori, :jumlah, :metode)');
        $statement->execute([
            ':tanggal' => $tanggal,
            ':bulan' => $bulan,
            ':tahun' => $tahun,
            ':keterangan' => $keterangan,
            ':jenis' => $jenis,
            ':kategori' => $kategori,
            ':jumlah' => $jumlah,
            ':metode' => $metode,
        ]);
    } catch (Throwable $error) {
        tampilkanKesalahanDatabase($error);
    }
    $_SESSION['flash'] = ['pesan' => 'Transaksi berhasil disimpan.'];
}

header('Location: ../transaksi/list.php');
exit;
