<?php
$page_title = 'Edit Warga';
require_once __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/header.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
try {
    $db = koneksiDatabase();
    $warga = $id ? ambilWargaDenganId($db, $id) : null;
} catch (Throwable $error) {
    tampilkanKesalahanDatabase($error);
}
if (!$warga) {
    $_SESSION['flash'] = ['pesan' => 'Data warga tidak ditemukan.'];
    header('Location: list.php');
    exit;
}
?>
<div class="card shadow-sm border-0">
    <h2>Edit Warga</h2>
    <form id="form-tambah" method="post" action="../proses/proses_edit_warga.php" class="row g-3">
        <input type="hidden" name="id" value="<?php echo (int) $warga['id']; ?>">
        <div class="col-md-6"><label for="nama" class="form-label">Nama Kepala Keluarga</label><input type="text" id="nama" name="nama" class="form-control" value="<?php echo htmlspecialchars($warga['nama']); ?>" required></div>
        <div class="col-md-6"><label for="no_kk" class="form-label">No. KK</label><input type="text" id="no_kk" name="no_kk" class="form-control" value="<?php echo htmlspecialchars($warga['no_kk']); ?>" required></div>
        <div class="col-md-8"><label for="alamat" class="form-label">Alamat</label><input type="text" id="alamat" name="alamat" class="form-control" value="<?php echo htmlspecialchars($warga['alamat']); ?>" required></div>
        <div class="col-md-4"><label for="no_hp" class="form-label">No. HP</label><input type="text" id="no_hp" name="no_hp" class="form-control" value="<?php echo htmlspecialchars($warga['no_hp']); ?>"></div>
        <div class="col-md-6"><label for="status" class="form-label">Status Kependudukan</label><select id="status" name="status" class="form-select" required><option value="aktif"<?php echo $warga['status'] === 'aktif' ? ' selected' : ''; ?>>Aktif</option><option value="pindah"<?php echo $warga['status'] === 'pindah' ? ' selected' : ''; ?>>Pindah</option></select></div>
        <div class="col-12"><button type="submit" class="btn btn-primary">Simpan Perubahan</button><a href="list.php" class="btn btn-outline-secondary ms-2">Batal</a></div>
    </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
