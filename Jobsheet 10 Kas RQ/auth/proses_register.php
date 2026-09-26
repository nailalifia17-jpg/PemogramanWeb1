<?php
session_start();
require_once __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

$nama = trim((string) ($_POST['nama'] ?? ''));
$username = trim((string) ($_POST['username'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$konfirmasi = (string) ($_POST['konfirmasi_password'] ?? '');
$role = $_POST['role'] ?? '';

if ($nama === '' || $username === '' || strlen($password) < 6 || $password !== $konfirmasi || !in_array($role, ['bendahara', 'ketua'], true)) {
    $_SESSION['flash'] = ['pesan' => 'Data registrasi belum valid. Password minimal 6 karakter dan harus sama.'];
    header('Location: register.php');
    exit;
}

try {
    $db = koneksiDatabase();
    $statement = $db->prepare('INSERT INTO users (nama, username, password, role) VALUES (:nama, :username, :password, :role)');
    $statement->execute([
        ':nama' => $nama,
        ':username' => $username,
        ':password' => password_hash($password, PASSWORD_DEFAULT),
        ':role' => $role,
    ]);
    $_SESSION['flash'] = ['pesan' => 'Registrasi berhasil. Silakan login.'];
    header('Location: login.php');
    exit;
} catch (PDOException $error) {
    $_SESSION['flash'] = ['pesan' => $error->getCode() === '23505' ? 'Username sudah digunakan.' : 'Registrasi gagal disimpan.'];
    header('Location: register.php');
    exit;
} catch (Throwable $error) {
    tampilkanKesalahanDatabase($error);
}

