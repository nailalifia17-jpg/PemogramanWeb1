<?php
session_start();
require_once __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = trim((string) ($_POST['username'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

try {
    $db = koneksiDatabase();
    $statement = $db->prepare('SELECT id, nama, username, password, role FROM users WHERE username = :username');
    $statement->execute([':username' => $username]);
    $user = $statement->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['flash'] = ['pesan' => 'Username atau password salah.'];
        header('Location: login.php');
        exit;
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    header('Location: ../index.php');
    exit;
} catch (Throwable $error) {
    tampilkanKesalahanDatabase($error);
}

