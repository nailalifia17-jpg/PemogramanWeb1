<?php

require_once __DIR__ . '/koneksi.php';

function ambilWarga(PDO $db): array
{
    return $db->query('SELECT id, no_kk, nama, alamat, no_hp, status FROM warga ORDER BY id DESC')->fetchAll();
}

function hitungWarga(PDO $db, string $q = ''): int
{
    $statement = $db->prepare('SELECT COUNT(*) FROM warga WHERE no_kk ILIKE :q OR nama ILIKE :q OR alamat ILIKE :q OR no_hp ILIKE :q');
    $statement->execute([':q' => '%' . $q . '%']);
    return (int) $statement->fetchColumn();
}

function ambilWargaHalaman(PDO $db, string $q, int $limit, int $offset): array
{
    $statement = $db->prepare('SELECT id, no_kk, nama, alamat, no_hp, status FROM warga WHERE no_kk ILIKE :q OR nama ILIKE :q OR alamat ILIKE :q OR no_hp ILIKE :q ORDER BY id DESC LIMIT :limit OFFSET :offset');
    $statement->bindValue(':q', '%' . $q . '%', PDO::PARAM_STR);
    $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
    $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchAll();
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

function hitungTransaksi(PDO $db, string $q = '', ?int $bulan = null, ?int $tahun = null): int
{
    $conditions = ['(keterangan ILIKE :q OR metode ILIKE :q OR jenis ILIKE :q)'];
    $params = [':q' => '%' . $q . '%'];
    if ($bulan !== null) {
        $conditions[] = 'bulan = :bulan';
        $params[':bulan'] = $bulan;
    }
    if ($tahun !== null) {
        $conditions[] = 'tahun = :tahun';
        $params[':tahun'] = $tahun;
    }
    $statement = $db->prepare('SELECT COUNT(*) FROM transaksi WHERE ' . implode(' AND ', $conditions));
    $statement->execute($params);
    return (int) $statement->fetchColumn();
}

function ambilTransaksiHalaman(PDO $db, string $q, ?int $bulan, ?int $tahun, int $limit, int $offset): array
{
    $conditions = ['(keterangan ILIKE :q OR metode ILIKE :q OR jenis ILIKE :q)'];
    $params = [':q' => '%' . $q . '%'];
    if ($bulan !== null) {
        $conditions[] = 'bulan = :bulan';
        $params[':bulan'] = $bulan;
    }
    if ($tahun !== null) {
        $conditions[] = 'tahun = :tahun';
        $params[':tahun'] = $tahun;
    }
    $statement = $db->prepare('SELECT id, tanggal, bulan, tahun, keterangan, jenis, jumlah, metode FROM transaksi WHERE ' . implode(' AND ', $conditions) . ' ORDER BY tanggal DESC, id DESC LIMIT :limit OFFSET :offset');
    foreach ($params as $name => $value) {
        $statement->bindValue($name, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
    $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchAll();
}

function ambilWargaDenganId(PDO $db, int $id): ?array
{
    $statement = $db->prepare('SELECT id, no_kk, nama, alamat, no_hp, status FROM warga WHERE id = :id');
    $statement->execute([':id' => $id]);
    return $statement->fetch() ?: null;
}

function ambilTransaksiDenganId(PDO $db, int $id): ?array
{
    $statement = $db->prepare('SELECT id, tanggal, bulan, tahun, keterangan, jenis, jumlah, metode FROM transaksi WHERE id = :id');
    $statement->execute([':id' => $id]);
    return $statement->fetch() ?: null;
}

function ringkasanTransaksi(PDO $db): array
{
    $summary = $db->query("SELECT COUNT(*) FILTER (WHERE jenis = 'masuk') AS jumlah_masuk, COUNT(*) FILTER (WHERE jenis = 'keluar') AS jumlah_keluar, COALESCE(SUM(jumlah) FILTER (WHERE jenis = 'masuk'), 0) AS total_masuk, COALESCE(SUM(jumlah) FILTER (WHERE jenis = 'keluar'), 0) AS total_keluar FROM transaksi")->fetch();

    return [
        'total_masuk' => (int) $summary['total_masuk'],
        'total_keluar' => (int) $summary['total_keluar'],
    ];
}
