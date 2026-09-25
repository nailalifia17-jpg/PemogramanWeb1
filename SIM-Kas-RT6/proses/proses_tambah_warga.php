<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $noKk = trim((string) ($_POST['no_kk'] ?? ''));
    $nama = trim((string) ($_POST['nama'] ?? ''));
    $alamat = trim((string) ($_POST['alamat'] ?? ''));
    $noHp = trim((string) ($_POST['no_hp'] ?? ''));
    $status = $_POST['status'] ?? '';

    if ($noKk === '' || $nama === '' || $alamat === '' || !in_array($status, ['aktif', 'pindah'], true)) {
        $_SESSION['flash'] = ['pesan' => 'Data warga belum valid. Periksa kembali isian formulir.'];
        header('Location: ../warga/tambah.php');
        exit;
    }

    $_SESSION['warga'] ??= [];
    $_SESSION['warga'][] = [
        'no_kk' => $noKk,
        'nama' => $nama,
        'alamat' => $alamat,
        'no_hp' => $noHp,
        'status' => $status,
    ];

    $_SESSION['flash'] = ['pesan' => 'Data warga berhasil disimpan.'];
}

header('Location: ../warga/list.php');
exit;