<?php
session_start();
require_once __DIR__ . '/../includes/koneksi.php';

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

    try {
        $db = koneksiDatabase();
        $statement = $db->prepare('INSERT INTO warga (no_kk, nama, alamat, no_hp, status) VALUES (:no_kk, :nama, :alamat, :no_hp, :status)');
        $statement->execute([
            ':no_kk' => $noKk,
            ':nama' => $nama,
            ':alamat' => $alamat,
            ':no_hp' => $noHp,
            ':status' => $status,
        ]);
    } catch (PDOException $error) {
        $_SESSION['flash'] = ['pesan' => $error->getCode() === '23505' ? 'No. KK sudah terdaftar. Gunakan nomor lain.' : 'Data warga gagal disimpan ke database.'];
        header('Location: ../warga/tambah.php');
        exit;
    } catch (Throwable $error) {
        tampilkanKesalahanDatabase($error);
    }

    $_SESSION['flash'] = ['pesan' => 'Data warga berhasil disimpan.'];
}

header('Location: ../warga/list.php');
exit;