<?php
require_once __DIR__ . '/../includes/auth.php';
wajibPeran(['bendahara']);
$page_title = 'Edit Anggota';
require_once __DIR__ . '/../includes/data.php';
include __DIR__ . '/../includes/header.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
try {
    $db = koneksiDatabase();
    $anggota = $id ? ambilAnggotaDenganId($db, $id) : null;
} catch (Throwable $error) {
    tampilkanKesalahanDatabase($error);
}
if (!$anggota) {
    $_SESSION['flash'] = ['pesan' => 'Data anggota tidak ditemukan.'];
    header('Location: list.php');
    exit;
}
?>
<div class="card shadow-sm border-0">
    <h2>Edit Anggota</h2>
    <form id="form-tambah" method="post" action="../proses/proses_edit_anggota.php" class="row g-3">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="id" value="<?php echo (int) $anggota['id']; ?>">
        <div class="col-md-6"><label for="nama" class="form-label">Nama Anggota Yayasan</label><input type="text" id="nama" name="nama" class="form-control" value="<?php echo e($anggota['nama']); ?>" required></div>
        <div class="col-md-6"><label for="no_anggota" class="form-label">Nomor Anggota</label><input type="text" id="no_anggota" name="no_anggota" class="form-control" value="<?php echo e($anggota['no_anggota']); ?>" required></div>
        <div class="col-md-8"><label for="alamat" class="form-label">Alamat</label><input type="text" id="alamat" name="alamat" class="form-control" value="<?php echo e($anggota['alamat']); ?>" required></div>
        <div class="col-md-4"><label for="no_hp" class="form-label">No. HP</label><input type="text" id="no_hp" name="no_hp" class="form-control" value="<?php echo e($anggota['no_hp']); ?>"></div>
        <div class="col-md-6"><label for="status" class="form-label">Status Keanggotaan</label><select id="status" name="status" class="form-select" required><option value="aktif"<?php echo $anggota['status'] === 'aktif' ? ' selected' : ''; ?>>Aktif</option><option value="nonaktif"<?php echo $anggota['status'] === 'nonaktif' ? ' selected' : ''; ?>>Nonaktif</option></select></div>
        <div class="col-12"><button type="submit" class="btn btn-primary">Simpan Perubahan</button><a href="list.php" class="btn btn-outline-secondary ms-2">Batal</a></div>
    </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
