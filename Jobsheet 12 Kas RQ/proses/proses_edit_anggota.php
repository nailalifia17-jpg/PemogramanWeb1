<?php
require_once __DIR__ . '/../includes/auth.php';
wajibPeran(['bendahara']);
require_once __DIR__ . '/../includes/koneksi.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../anggota/list.php'); exit; }
csrf_verify();
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
$noAnggota = trim((string) ($_POST['no_anggota'] ?? ''));
$nama = trim((string) ($_POST['nama'] ?? ''));
$alamat = trim((string) ($_POST['alamat'] ?? ''));
$noHp = trim((string) ($_POST['no_hp'] ?? ''));
$status = $_POST['status'] ?? '';
if (!$id || $noAnggota === '' || $nama === '' || $alamat === '' || !in_array($status, ['aktif', 'nonaktif'], true)) { $_SESSION['flash'] = ['pesan' => 'Data anggota belum valid.']; header('Location: ../anggota/list.php'); exit; }
try {
    $db = koneksiDatabase();
    $statement = $db->prepare('UPDATE anggota SET no_anggota = :no_anggota, nama = :nama, alamat = :alamat, no_hp = :no_hp, status = :status WHERE id = :id');
    $statement->execute([':no_anggota' => $noAnggota, ':nama' => $nama, ':alamat' => $alamat, ':no_hp' => $noHp, ':status' => $status, ':id' => $id]);
    $_SESSION['flash'] = ['pesan' => 'Data anggota berhasil diperbarui.'];
} catch (PDOException $error) { $_SESSION['flash'] = ['pesan' => $error->getCode() === '23505' ? 'Nomor anggota sudah terdaftar.' : 'Data anggota gagal diperbarui.']; }
header('Location: ../anggota/list.php'); exit;
