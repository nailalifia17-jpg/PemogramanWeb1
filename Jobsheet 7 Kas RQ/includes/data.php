<?php

require_once __DIR__ . '/koneksi.php';

function ambilWarga(PDO $db): array
{
    return $db->query('SELECT id, no_kk, nama, alamat, no_hp, status FROM warga ORDER BY id DESC')->fetchAll();
}

function ambilTransaksi(PDO $db, ?int $bulan = null, ?int $tahun = null): array
{
    $sql = 'SELECT id, tanggal, bulan, tahun, keterangan, jenis, jumlah, metode FROM transaksi';
    $conditions = [];
    $params = [];

    if ($bulan !== null) {
        $conditions[] = 'bulan = :bulan';
        $params[':bulan'] = $bulan;
    }
    if ($tahun !== null) {
        $conditions[] = 'tahun = :tahun';
        $params[':tahun'] = $tahun;
    }
    if ($conditions) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }
    $sql .= ' ORDER BY tanggal DESC, id DESC';

    $statement = $db->prepare($sql);
    $statement->execute($params);
    return $statement->fetchAll();
}

function ringkasanTransaksi(PDO $db): array
{
    $summary = $db->query("SELECT COUNT(*) FILTER (WHERE jenis = 'masuk') AS jumlah_masuk, COUNT(*) FILTER (WHERE jenis = 'keluar') AS jumlah_keluar, COALESCE(SUM(jumlah) FILTER (WHERE jenis = 'masuk'), 0) AS total_masuk, COALESCE(SUM(jumlah) FILTER (WHERE jenis = 'keluar'), 0) AS total_keluar FROM transaksi")->fetch();

    return [
        'total_masuk' => (int) $summary['total_masuk'],
        'total_keluar' => (int) $summary['total_keluar'],
    ];
}

