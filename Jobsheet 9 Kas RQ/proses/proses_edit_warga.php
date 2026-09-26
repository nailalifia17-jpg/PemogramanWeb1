<?php
session_start();
require_once __DIR__ . '/../includes/koneksi.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../warga/list.php'); exit; }
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
$noKk = trim((string) ($_POST['no_kk'] ?? ''));
$nama = trim((string) ($_POST['nama'] ?? ''));
$alamat = trim((string) ($_POST['alamat'] ?? ''));
$noHp = trim((string) ($_POST['no_hp'] ?? ''));
$status = $_POST['status'] ?? '';
if (!$id || $noKk === '' || $nama === '' || $alamat === '' || !in_array($status, ['aktif', 'pindah'], true)) { $_SESSION['flash'] = ['pesan' => 'Data warga belum valid.']; header('Location: ../warga/list.php'); exit; }
try {
    $db = koneksiDatabase();
    $statement = $db->prepare('UPDATE warga SET no_kk = :no_kk, nama = :nama, alamat = :alamat, no_hp = :no_hp, status = :status WHERE id = :id');
    $statement->execute([':no_kk' => $noKk, ':nama' => $nama, ':alamat' => $alamat, ':no_hp' => $noHp, ':status' => $status, ':id' => $id]);
    $_SESSION['flash'] = ['pesan' => 'Data warga berhasil diperbarui.'];
} catch (PDOException $error) { $_SESSION['flash'] = ['pesan' => $error->getCode() === '23505' ? 'No. KK sudah terdaftar.' : 'Data warga gagal diperbarui.']; }
header('Location: ../warga/list.php'); exit;

