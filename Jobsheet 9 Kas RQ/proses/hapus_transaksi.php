<?php
session_start();
require_once __DIR__ . '/../includes/koneksi.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../transaksi/list.php'); exit; }
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) { $_SESSION['flash'] = ['pesan' => 'ID transaksi tidak valid.']; header('Location: ../transaksi/list.php'); exit; }
try { $db = koneksiDatabase(); $statement = $db->prepare('DELETE FROM transaksi WHERE id = :id'); $statement->execute([':id' => $id]); $_SESSION['flash'] = ['pesan' => 'Transaksi berhasil dihapus.']; } catch (Throwable $error) { $_SESSION['flash'] = ['pesan' => 'Transaksi gagal dihapus.']; }
header('Location: ../transaksi/list.php'); exit;

