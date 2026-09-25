<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = trim((string) ($_POST['tanggal'] ?? ''));
    $keterangan = trim((string) ($_POST['keterangan'] ?? ''));
    $jenis = $_POST['jenis'] ?? '';
    $jumlah = filter_var($_POST['jumlah'] ?? null, FILTER_VALIDATE_INT);
    $bulan = filter_var($_POST['bulan'] ?? null, FILTER_VALIDATE_INT);
    $tahun = filter_var($_POST['tahun'] ?? null, FILTER_VALIDATE_INT);
    $metode = $_POST['metode'] ?? 'tunai';

    if ($tanggal === '' || $keterangan === '' || !in_array($jenis, ['masuk', 'keluar'], true) || $jumlah === false || $jumlah <= 0 || $bulan === false || $bulan < 1 || $bulan > 12 || $tahun === false || $tahun < 2000 || $tahun > 2100 || !in_array($metode, ['tunai', 'transfer'], true)) {
        $_SESSION['flash'] = ['pesan' => 'Data transaksi belum valid. Periksa kembali isian formulir.'];
        header('Location: ../transaksi/tambah.php');
        exit;
    }

    $_SESSION['transaksi'] ??= [];
    $_SESSION['transaksi'][] = [
        'tanggal' => $tanggal,
        'keterangan' => $keterangan,
        'jenis' => $jenis,
        'jumlah' => $jumlah,
        'bulan' => $bulan,
        'tahun' => $tahun,
        'metode' => $metode,
    ];
    $_SESSION['flash'] = ['pesan' => 'Transaksi berhasil disimpan.'];
}

header('Location: ../transaksi/list.php');
exit;