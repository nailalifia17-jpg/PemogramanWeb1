<?php
require_once __DIR__ . '/../includes/auth.php';
wajibPeran(['bendahara']);
require_once __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $noAnggota = trim((string) ($_POST['no_anggota'] ?? ''));
    $nama = trim((string) ($_POST['nama'] ?? ''));
    $alamat = trim((string) ($_POST['alamat'] ?? ''));
    $noHp = trim((string) ($_POST['no_hp'] ?? ''));
    $status = $_POST['status'] ?? '';

    if ($noAnggota === '' || $nama === '' || $alamat === '' || !in_array($status, ['aktif', 'nonaktif'], true)) {
        $_SESSION['flash'] = ['pesan' => 'Data anggota belum valid. Periksa kembali isian formulir.'];
        header('Location: ../anggota/tambah.php');
        exit;
    }

    try {
        $db = koneksiDatabase();
        $statement = $db->prepare('INSERT INTO anggota (no_anggota, nama, alamat, no_hp, status) VALUES (:no_anggota, :nama, :alamat, :no_hp, :status)');
        $statement->execute([
            ':no_anggota' => $noAnggota,
            ':nama' => $nama,
            ':alamat' => $alamat,
            ':no_hp' => $noHp,
            ':status' => $status,
        ]);
    } catch (PDOException $error) {
        $_SESSION['flash'] = ['pesan' => $error->getCode() === '23505' ? 'Nomor anggota sudah terdaftar. Gunakan nomor lain.' : 'Data anggota gagal disimpan ke database.'];
        header('Location: ../anggota/tambah.php');
        exit;
    } catch (Throwable $error) {
        tampilkanKesalahanDatabase($error);
    }

    $_SESSION['flash'] = ['pesan' => 'Data anggota berhasil disimpan.'];
}

header('Location: ../anggota/list.php');
exit;
