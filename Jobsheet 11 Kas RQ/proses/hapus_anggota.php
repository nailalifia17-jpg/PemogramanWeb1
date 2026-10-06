<?php
require_once __DIR__ . '/../includes/auth.php';
wajibPeran(['bendahara']);
require_once __DIR__ . '/../includes/koneksi.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../anggota/list.php'); exit; }
csrf_verify();
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) { $_SESSION['flash'] = ['pesan' => 'ID anggota tidak valid.']; header('Location: ../anggota/list.php'); exit; }
try { $db = koneksiDatabase(); $statement = $db->prepare('DELETE FROM anggota WHERE id = :id'); $statement->execute([':id' => $id]); $_SESSION['flash'] = ['pesan' => 'Data anggota berhasil dihapus.']; } catch (Throwable $error) { $_SESSION['flash'] = ['pesan' => 'Data anggota gagal dihapus.']; }
header('Location: ../anggota/list.php'); exit;

