<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/data.php';

$format = $_GET['format'] ?? 'excel';
$filterBulan = filter_var($_GET['bulan'] ?? '', FILTER_VALIDATE_INT) ?: null;
$filterTahun = filter_var($_GET['tahun'] ?? '', FILTER_VALIDATE_INT) ?: null;

try {
    $db = koneksiDatabase();
    $transaksi = ambilTransaksi($db, $filterBulan, $filterTahun);
} catch (Throwable $error) {
    tampilkanKesalahanDatabase($error);
}

if ($format !== 'excel') {
    header('Location: index.php?bulan=' . urlencode((string) ($filterBulan ?? '')) . '&tahun=' . urlencode((string) ($filterTahun ?? '')));
    exit;
}

if (!class_exists('ZipArchive')) {
    http_response_code(500);
    exit('Ekstensi PHP "zip" belum aktif, jadi file Excel belum bisa dibuat. Aktifkan extension=zip di php.ini.');
}

$namaBulan = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];

/* ---------- fungsi bantu pembuat sel Excel ---------- */
function xlEsc(string $teks): string
{
    $teks = (string) preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $teks);
    return htmlspecialchars($teks, ENT_XML1 | ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function xlStr(string $ref, string $teks, int $gaya): string
{
    return '<c r="' . $ref . '" s="' . $gaya . '" t="inlineStr"><is><t xml:space="preserve">' . xlEsc($teks) . '</t></is></c>';
}

function xlNum(string $ref, int $angka, int $gaya): string
{
    return '<c r="' . $ref . '" s="' . $gaya . '"><v>' . $angka . '</v></c>';
}

function xlKosong(string $ref, int $gaya): string
{
    return '<c r="' . $ref . '" s="' . $gaya . '"/>';
}

function xlRumus(string $ref, string $rumus, int $nilai, int $gaya): string
{
    return '<c r="' . $ref . '" s="' . $gaya . '"><f>' . $rumus . '</f><v>' . $nilai . '</v></c>';
}

function xlBaris(int $nomor, string $isi, ?int $tinggi = null): string
{
    $atribut = $tinggi !== null ? ' ht="' . $tinggi . '" customHeight="1"' : '';
    return '<row r="' . $nomor . '"' . $atribut . '>' . $isi . '</row>';
}

function xlSerialTanggal(string $tanggal): ?int
{
    try {
        $utc = new DateTimeZone('UTC');
        $tgl = new DateTimeImmutable(substr($tanggal, 0, 10), $utc);
        $awal = new DateTimeImmutable('1899-12-30', $utc);
        return (int) $awal->diff($tgl)->format('%r%a');
    } catch (Throwable $e) {
        return null;
    }
}

/* ---------- susun isi lembar kerja ---------- */
$baris = [];
$gabung = [];
$kolom = ['A', 'B', 'C', 'D', 'E'];

$periode = 'Periode: ' . ($filterBulan ? ($namaBulan[$filterBulan] ?? '') . ' ' : '') . ($filterTahun ?: 'Semua Tahun');

$baris[] = xlBaris(1, xlStr('A1', 'Yayasan Rumah Quran Mumtazah', 1), 28);
$gabung[] = 'A1:E1';
$baris[] = xlBaris(2, xlStr('A2', 'Laporan Keuangan Pemasukan dan Pengeluaran', 2));
$gabung[] = 'A2:E2';
$baris[] = xlBaris(3, xlStr('A3', $periode, 2));
$gabung[] = 'A3:E3';

$r = 5;
$barisTotal = [];
$nilaiTotal = [];

$bagian = [
    ['A. Pemasukan (Kas Masuk)', 'masuk', 'Subtotal Pemasukan', 'Belum ada pemasukan.'],
    ['B. Pengeluaran (Kas Keluar)', 'keluar', 'Subtotal Pengeluaran', 'Belum ada pengeluaran.'],
];

foreach ($bagian as [$judul, $jenis, $labelTotal, $pesanKosong]) {
    $baris[] = xlBaris($r, xlStr('A' . $r, $judul, 3));
    $r++;

    $judulKolom = ['No.', 'Tanggal', 'Kategori', 'Keterangan', 'Jumlah (Rp)'];
    $sel = '';
    foreach ($judulKolom as $i => $teks) {
        $sel .= xlStr($kolom[$i] . $r, $teks, 4);
    }
    $baris[] = xlBaris($r, $sel, 22);
    $r++;

    $pertama = $r;
    $no = 0;
    $subtotal = 0;

    foreach ($transaksi as $item) {
        if ($item['jenis'] !== $jenis) {
            continue;
        }
        $no++;
        $jumlah = (int) $item['jumlah'];
        $subtotal += $jumlah;
        $serial = xlSerialTanggal((string) $item['tanggal']);

        $sel = xlNum('A' . $r, $no, 6);
        $sel .= $serial !== null
            ? xlNum('B' . $r, $serial, 7)
            : xlStr('B' . $r, (string) $item['tanggal'], 6);
        $sel .= xlStr('C' . $r, (string) $item['kategori'], 5);
        $sel .= xlStr('D' . $r, (string) $item['keterangan'], 5);
        $sel .= xlNum('E' . $r, $jumlah, 8);
        $baris[] = xlBaris($r, $sel);
        $r++;
    }

    $terakhir = $r - 1;

    if ($no === 0) {
        $sel = xlStr('A' . $r, $pesanKosong, 6);
        foreach (['B', 'C', 'D', 'E'] as $huruf) {
            $sel .= xlKosong($huruf . $r, 6);
        }
        $baris[] = xlBaris($r, $sel);
        $gabung[] = 'A' . $r . ':E' . $r;
        $r++;
    }

    $sel = xlStr('A' . $r, $labelTotal, 9);
    foreach (['B', 'C', 'D'] as $huruf) {
        $sel .= xlKosong($huruf . $r, 9);
    }
    $sel .= $no > 0
        ? xlRumus('E' . $r, 'SUM(E' . $pertama . ':E' . $terakhir . ')', $subtotal, 10)
        : xlNum('E' . $r, 0, 10);
    $baris[] = xlBaris($r, $sel);
    $gabung[] = 'A' . $r . ':D' . $r;

    $barisTotal[$jenis] = $r;
    $nilaiTotal[$jenis] = $subtotal;
    $r += 2;
}

/* ---------- ringkasan saldo ---------- */
$baris[] = xlBaris($r, xlStr('A' . $r, 'C. Ringkasan Saldo', 3));
$r++;

$totalMasuk = $nilaiTotal['masuk'];
$totalKeluar = $nilaiTotal['keluar'];
$saldoAwal = 0;

$ringkasan = [
    ['Saldo Awal', null, $saldoAwal, 11, 12],
    ['Total Pemasukan', 'E' . $barisTotal['masuk'], $totalMasuk, 11, 12],
    ['Total Pengeluaran', 'E' . $barisTotal['keluar'], $totalKeluar, 11, 12],
    ['Saldo Akhir', null, $saldoAwal + $totalMasuk - $totalKeluar, 13, 14],
];
$awalRingkasan = $r;

foreach ($ringkasan as $indeks => [$label, $rumus, $nilai, $gayaLabel, $gayaAngka]) {
    if ($label === 'Saldo Akhir') {
        $rumus = 'E' . $awalRingkasan . '+E' . ($awalRingkasan + 1) . '-E' . ($awalRingkasan + 2);
    }
    $sel = xlStr('A' . $r, $label, $gayaLabel);
    foreach (['B', 'C', 'D'] as $huruf) {
        $sel .= xlKosong($huruf . $r, $gayaLabel);
    }
    $sel .= $rumus !== null
        ? xlRumus('E' . $r, $rumus, $nilai, $gayaAngka)
        : xlNum('E' . $r, $nilai, $gayaAngka);
    $baris[] = xlBaris($r, $sel);
    $gabung[] = 'A' . $r . ':D' . $r;
    $r++;
}

$barisTerakhir = $r - 1;

$mergeXml = '';
foreach ($gabung as $rentang) {
    $mergeXml .= '<mergeCell ref="' . $rentang . '"/>';
}

$sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
    . '<sheetPr><pageSetUpPr fitToPage="1"/></sheetPr>'
    . '<dimension ref="A1:E' . $barisTerakhir . '"/>'
    . '<sheetViews><sheetView showGridLines="0" workbookViewId="0"/></sheetViews>'
    . '<sheetFormatPr defaultRowHeight="15"/>'
    . '<cols>'
    . '<col min="1" max="1" width="6" customWidth="1"/>'
    . '<col min="2" max="2" width="14" customWidth="1"/>'
    . '<col min="3" max="3" width="26" customWidth="1"/>'
    . '<col min="4" max="4" width="42" customWidth="1"/>'
    . '<col min="5" max="5" width="20" customWidth="1"/>'
    . '</cols>'
    . '<sheetData>' . implode('', $baris) . '</sheetData>'
    . '<mergeCells count="' . count($gabung) . '">' . $mergeXml . '</mergeCells>'
    . '<pageMargins left="0.5" right="0.5" top="0.75" bottom="0.75" header="0.3" footer="0.3"/>'
    . '<pageSetup paperSize="9" orientation="portrait" fitToWidth="1" fitToHeight="0"/>'
    . '</worksheet>';

/* ---------- bagian tetap file .xlsx ---------- */
$contentTypes = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/></Types>
XML;

$relsUtama = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>
XML;

$workbook = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Laporan Kas" sheetId="1" r:id="rId1"/></sheets></workbook>
XML;

$relsWorkbook = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>
XML;

$styles = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<numFmts count="2"><numFmt numFmtId="164" formatCode="dd/mm/yyyy"/><numFmt numFmtId="165" formatCode="&quot;Rp &quot;#,##0"/></numFmts>
<fonts count="5">
<font><sz val="11"/><name val="Calibri"/><family val="2"/></font>
<font><b/><sz val="11"/><name val="Calibri"/><family val="2"/></font>
<font><b/><sz val="16"/><color rgb="FF1B5E3F"/><name val="Calibri"/><family val="2"/></font>
<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/><family val="2"/></font>
<font><b/><sz val="12"/><color rgb="FF1B5E3F"/><name val="Calibri"/><family val="2"/></font>
</fonts>
<fills count="5">
<fill><patternFill patternType="none"/></fill>
<fill><patternFill patternType="gray125"/></fill>
<fill><patternFill patternType="solid"><fgColor rgb="FF1B5E3F"/><bgColor indexed="64"/></patternFill></fill>
<fill><patternFill patternType="solid"><fgColor rgb="FFEAF3DE"/><bgColor indexed="64"/></patternFill></fill>
<fill><patternFill patternType="solid"><fgColor rgb="FFFDF1CF"/><bgColor indexed="64"/></patternFill></fill>
</fills>
<borders count="2">
<border><left/><right/><top/><bottom/><diagonal/></border>
<border><left style="thin"><color rgb="FF808080"/></left><right style="thin"><color rgb="FF808080"/></right><top style="thin"><color rgb="FF808080"/></top><bottom style="thin"><color rgb="FF808080"/></bottom><diagonal/></border>
</borders>
<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>
<cellXfs count="15">
<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
<xf numFmtId="0" fontId="4" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
<xf numFmtId="0" fontId="3" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center" wrapText="1"/></xf>
<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
<xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
<xf numFmtId="165" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
<xf numFmtId="0" fontId="1" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
<xf numFmtId="165" fontId="1" fillId="3" borderId="1" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
<xf numFmtId="165" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
<xf numFmtId="0" fontId="1" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
<xf numFmtId="165" fontId="1" fillId="4" borderId="1" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
</cellXfs>
<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>
</styleSheet>
XML;

/* ---------- kemas jadi .xlsx lalu kirim ke browser ---------- */
$tmp = tempnam(sys_get_temp_dir(), 'xlsx');
$zip = new ZipArchive();
if ($zip->open($tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    http_response_code(500);
    exit('Gagal membuat file Excel.');
}
$zip->addFromString('[Content_Types].xml', $contentTypes);
$zip->addFromString('_rels/.rels', $relsUtama);
$zip->addFromString('xl/workbook.xml', $workbook);
$zip->addFromString('xl/_rels/workbook.xml.rels', $relsWorkbook);
$zip->addFromString('xl/styles.xml', $styles);
$zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
$zip->close();

$namaFile = 'laporan-kas-yayasan-rumah-quran-mumtazah-' . date('Y-m-d') . '.xlsx';

if (ob_get_length()) {
    ob_end_clean();
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $namaFile . '"');
header('Content-Length: ' . filesize($tmp));
header('Cache-Control: max-age=0');
readfile($tmp);
unlink($tmp);
exit;