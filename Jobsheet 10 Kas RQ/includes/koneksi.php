<?php

function koneksiDatabase(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    if (!extension_loaded('pdo_pgsql')) {
        throw new RuntimeException('Ekstensi pdo_pgsql belum aktif. Aktifkan pdo_pgsql di php.ini Laragon lalu restart server.');
    }

    $host = getenv('SIMKAS_DB_HOST') ?: '127.0.0.1';
    $port = getenv('SIMKAS_DB_PORT') ?: '5432';
    $database = getenv('SIMKAS_DB_NAME') ?: 'sim_kas_yayasan_rq';
    $user = getenv('SIMKAS_DB_USER') ?: 'postgres';
    $password = getenv('SIMKAS_DB_PASS') ?: 'postgres';
    $dsn = "pgsql:host={$host};port={$port};dbname={$database}";

    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}

function tampilkanKesalahanDatabase(Throwable $error): never
{
    http_response_code(500);
    $pesan = htmlspecialchars($error->getMessage(), ENT_QUOTES, 'UTF-8');
    echo '<!doctype html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>SIM Kas Yayasan RQ - Database</title><style>body{font-family:Arial,sans-serif;background:#f5f7fb;color:#263238;margin:0;padding:32px}.box{max-width:760px;margin:auto;background:#fff;padding:28px;border-radius:12px;box-shadow:0 4px 12px #0001}h1{color:#0b6b57}code{background:#eef0f8;padding:3px 6px;border-radius:4px}</style></head><body><div class="box"><h1>Database belum siap</h1><p>SIM Kas Yayasan RQ membutuhkan PostgreSQL untuk menyimpan data secara permanen.</p><p><strong>Detail:</strong> ' . $pesan . '</p><p>Aktifkan <code>pdo_pgsql</code>, buat database <code>sim_kas_yayasan_rq</code>, lalu jalankan file <code>sql/01_schema.sql</code>.</p></div></body></html>';
    exit;
}


