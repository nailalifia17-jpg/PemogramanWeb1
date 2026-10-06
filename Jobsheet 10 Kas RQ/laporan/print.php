<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/data.php';

$filterBulan = filter_var($_GET['bulan'] ?? '', FILTER_VALIDATE_INT) ?: null;
$filterTahun = filter_var($_GET['tahun'] ?? '', FILTER_VALIDATE_INT) ?: null;
$namaBulan = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];

try {
    $db = koneksiDatabase();
    $transaksi = ambilTransaksi($db, $filterBulan, $filterTahun);
} catch (Throwable $error) {
    tampilkanKesalahanDatabase($error);
}

$totalMasuk = 0;
$totalKeluar = 0;
foreach ($transaksi as $t) {
    if ($t['jenis'] === 'masuk') {
        $totalMasuk += (int) $t['jumlah'];
    }
    if ($t['jenis'] === 'keluar') {
        $totalKeluar += (int) $t['jumlah'];
    }
}
$saldoAwal = 0;
$saldoAkhir = $saldoAwal + $totalMasuk - $totalKeluar;
$periode = ($filterBulan ? ($namaBulan[$filterBulan] ?? '') . ' ' : '') . ($filterTahun ?: 'Semua Tahun');

$renderRows = function (string $jenis, string $judul, string $kosongLabel) use ($transaksi, $namaBulan, $periode): string {
    $rows = [];
    $jumlah = 0;
    $nomor = 1;
    $ada = false;

    foreach ($transaksi as $t) {
        if ($t['jenis'] !== $jenis) {
            continue;
        }
        $ada = true;
        $jumlah += (int) $t['jumlah'];
        $rows[] = '<tr>'
            . '<td>' . htmlspecialchars((string) $nomor++, ENT_QUOTES, 'UTF-8') . '</td>'
            . '<td>' . htmlspecialchars((string) $t['tanggal'], ENT_QUOTES, 'UTF-8') . '</td>'
            . '<td>' . htmlspecialchars((string) $t['kategori'], ENT_QUOTES, 'UTF-8') . '</td>'
            . '<td>' . htmlspecialchars((string) $t['keterangan'], ENT_QUOTES, 'UTF-8') . '</td>'
            . '<td class="num">Rp ' . number_format((int) $t['jumlah'], 0, ',', '.') . '</td>'
            . '</tr>';
    }

    if (!$ada) {
        $rows[] = '<tr><td colspan="5" class="muted">' . htmlspecialchars($kosongLabel, ENT_QUOTES, 'UTF-8') . '</td></tr>';
    }

    $rows[] = '<tr class="total-row">'
        . '<td colspan="4"><strong>Subtotal ' . htmlspecialchars($judul, ENT_QUOTES, 'UTF-8') . '</strong></td>'
        . '<td class="num"><strong>Rp ' . number_format($jumlah, 0, ',', '.') . '</strong></td>'
        . '</tr>';

    return '<h3>' . htmlspecialchars($judul, ENT_QUOTES, 'UTF-8') . '</h3>'
        . '<table>'
        . '<thead><tr><th>No</th><th>Tanggal</th><th>Kategori</th><th>Keterangan</th><th>Jumlah</th></tr></thead>'
        . '<tbody>' . implode('', $rows) . '</tbody>'
        . '</table>';
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kas - Yayasan Rumah Quran Mumtazah</title>
    <style>
        :root {
            --hijau: #14532d;
            --hijau-soft: #e9f8ee;
            --gelap: #1e293b;
            --abu: #64748b;
            --garis: #d9e2ec;
            --putih: #ffffff;
            --kuning: #fef3c7;
            --merah-soft: #fdeeee;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 32px;
            font-family: Arial, Helvetica, sans-serif;
            background: #fff;
            color: var(--gelap);
        }
        .report {
            max-width: 1000px;
            margin: 0 auto;
            border: 1px solid var(--garis);
            border-radius: 12px;
            padding: 28px 30px;
            background: var(--putih);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid var(--hijau);
            padding-bottom: 18px;
            margin-bottom: 18px;
        }
        .header h1 {
            margin: 0;
            font-size: 30px;
            color: var(--hijau);
        }
        .header h2 {
            margin: 8px 0 0;
            font-size: 20px;
            color: var(--gelap);
        }
        .header p {
            margin: 8px 0 0;
            color: var(--abu);
            font-size: 14px;
        }
        h3 {
            margin: 22px 0 10px;
            font-size: 18px;
            color: var(--hijau);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            font-size: 13px;
        }
        th, td {
            border: 1px solid var(--garis);
            padding: 9px 10px;
            vertical-align: top;
            text-align: left;
        }
        th {
            background: #f1f5f9;
            color: var(--gelap);
            font-weight: 700;
        }
        .num {
            text-align: right;
            white-space: nowrap;
        }
        .total-row td {
            background: #f8fafc;
            font-weight: 700;
        }
        .muted {
            text-align: center;
            color: var(--abu);
        }
        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 18px;
        }
        .btn {
            display: inline-block;
            padding: 8px 14px;
            border: 1px solid var(--hijau);
            border-radius: 8px;
            background: var(--hijau);
            color: #fff;
            text-decoration: none;
            font-size: 12px;
            cursor: pointer;
        }
        @media print {
            .actions { display: none !important; }
            body { padding: 0; }
            .report { border: none; border-radius: 0; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="report">
        <div class="actions no-print">
            <button class="btn" type="button" onclick="window.print()">Simpan ke PDF</button>
        </div>

        <div class="header">
            <h1>Yayasan Rumah Quran Mumtazah</h1>
            <h2>Laporan Keuangan Pemasukan dan Pengeluaran</h2>
            <p>Periode: <?php echo htmlspecialchars($periode, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>

        <?php echo $renderRows('masuk', 'A. Pemasukan (Kas Masuk)', 'Belum ada pemasukan.'); ?>
        <?php echo $renderRows('keluar', 'B. Pengeluaran (Kas Keluar)', 'Belum ada pengeluaran.'); ?>

        <h3>C. Rekapitulasi</h3>
        <table>
            <tr>
                <th style="width: 60%;">Total Pemasukan</th>
                <td class="num">Rp <?php echo number_format($totalMasuk, 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <th>Total Pengeluaran</th>
                <td class="num">Rp <?php echo number_format($totalKeluar, 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <th>Saldo Akhir</th>
                <td class="num"><strong>Rp <?php echo number_format($saldoAkhir, 0, ',', '.'); ?></strong></td>
            </tr>
        </table>
    </div>
</body>
</html>
