<?php
require_once __DIR__ . '/../includes/auth.php';
wajibPeran(['bendahara']);
require_once __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah.php');
    exit;
}

csrf_verify();
$anggotaId = filter_var($_POST['anggota_id'] ?? null, FILTER_VALIDATE_INT);
$bulan = filter_var($_POST['bulan'] ?? null, FILTER_VALIDATE_INT);
$tahun = filter_var($_POST['tahun'] ?? null, FILTER_VALIDATE_INT);
$tanggalBayar = trim((string) ($_POST['tanggal_bayar'] ?? ''));
$jumlah = filter_var($_POST['jumlah'] ?? null, FILTER_VALIDATE_INT);
$metode = $_POST['metode'] ?? '';
$tanggalValid = DateTimeImmutable::createFromFormat('!Y-m-d', $tanggalBayar);

if (
    !$anggotaId
    || $bulan === false || $bulan < 1 || $bulan > 12
    || $tahun === false || $tahun < 2000 || $tahun > 2100
    || !$tanggalValid || $tanggalValid->format('Y-m-d') !== $tanggalBayar
    || $jumlah === false || $jumlah <= 0
    || !in_array($metode, ['tunai', 'transfer'], true)
) {
    $_SESSION['flash'] = ['pesan' => 'Data iuran belum valid. Periksa kembali formulir.'];
    header('Location: tambah.php');
    exit;
}

$namaBulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

try {
    $db = koneksiDatabase();
    $db->beginTransaction();

    $statement = $db->prepare("SELECT nama FROM anggota WHERE id = :id AND status = 'aktif' FOR UPDATE");
    $statement->execute([':id' => $anggotaId]);
    $anggota = $statement->fetch();

    if (!$anggota) {
        $db->rollBack();
        $_SESSION['flash'] = ['pesan' => 'Anggota tidak ditemukan atau sudah tidak aktif.'];
        header('Location: tambah.php');
        exit;
    }

    $keterangan = sprintf('Iuran anggota %s - %s %d', $anggota['nama'], $namaBulan[$bulan], $tahun);
    $statement = $db->prepare("INSERT INTO transaksi (tanggal, bulan, tahun, keterangan, jenis, kategori, jumlah, metode) VALUES (:tanggal, :bulan, :tahun, :keterangan, 'masuk', 'Iuran Anggota', :jumlah, :metode) RETURNING id");
    $statement->execute([
        ':tanggal' => $tanggalBayar,
        ':bulan' => $bulan,
        ':tahun' => $tahun,
        ':keterangan' => $keterangan,
        ':jumlah' => $jumlah,
        ':metode' => $metode,
    ]);
    $transaksiId = (int) $statement->fetchColumn();

    $statement = $db->prepare('INSERT INTO iuran (anggota_id, transaksi_id, bulan, tahun, tanggal_bayar, jumlah, metode) VALUES (:anggota_id, :transaksi_id, :bulan, :tahun, :tanggal_bayar, :jumlah, :metode)');
    $statement->execute([
        ':anggota_id' => $anggotaId,
        ':transaksi_id' => $transaksiId,
        ':bulan' => $bulan,
        ':tahun' => $tahun,
        ':tanggal_bayar' => $tanggalBayar,
        ':jumlah' => $jumlah,
        ':metode' => $metode,
    ]);

    $db->commit();
    $_SESSION['flash'] = ['pesan' => 'Pembayaran iuran dan transaksi kas berhasil disimpan.'];
} catch (PDOException $error) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    $_SESSION['flash'] = ['pesan' => $error->getCode() === '23505' ? 'Iuran anggota untuk bulan dan tahun tersebut sudah tercatat.' : 'Pembayaran iuran gagal disimpan. Tidak ada data yang diubah.'];
} catch (Throwable $error) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    error_log('Failed to record member dues: ' . $error->getMessage());
    $_SESSION['flash'] = ['pesan' => 'Pembayaran iuran gagal disimpan. Tidak ada data yang diubah.'];
}

header('Location: list.php');
exit;
