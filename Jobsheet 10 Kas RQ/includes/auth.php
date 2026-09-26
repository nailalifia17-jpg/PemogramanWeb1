<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    $scriptDir = dirname($_SERVER['SCRIPT_FILENAME']);
    $rootDir = dirname(__DIR__);
    $relative = ltrim(str_replace('\\', '/', substr($scriptDir, strlen($rootDir))), '/');
    $base = $relative === '' ? '' : str_repeat('../', substr_count($relative, '/') + 1);
    header('Location: ' . $base . 'auth/login.php');
    exit;
}

function peranSaatIni(): string
{
    return (string) ($_SESSION['role'] ?? '');
}

function wajibPeran(array $peran): void
{
    if (!in_array(peranSaatIni(), $peran, true)) {
        http_response_code(403);
        echo '<!doctype html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Akses Ditolak</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="bg-light"><main class="container py-5"><div class="alert alert-warning"><h1 class="h4">Akses Ditolak</h1><p>Akun kamu tidak memiliki izin untuk melakukan tindakan ini.</p><a href="../laporan/index.php" class="btn btn-primary">Buka Laporan</a></div></main></body></html>';
        exit;
    }
}

