<?php
session_start();
require_once __DIR__ . '/../includes/koneksi.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../warga/list.php'); exit; }
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) { $_SESSION['flash'] = ['pesan' => 'ID warga tidak valid.']; header('Location: ../warga/list.php'); exit; }
try { $db = koneksiDatabase(); $statement = $db->prepare('DELETE FROM warga WHERE id = :id'); $statement->execute([':id' => $id]); $_SESSION['flash'] = ['pesan' => 'Data warga berhasil dihapus.']; } catch (Throwable $error) { $_SESSION['flash'] = ['pesan' => 'Data warga gagal dihapus.']; }
header('Location: ../warga/list.php'); exit;

