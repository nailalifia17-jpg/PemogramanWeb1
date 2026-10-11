<?php
require_once __DIR__ . '/../includes/auth.php';
wajibPeran(['bendahara']);
$page_title = "Tambah Anggota";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>

<div class="card shadow-sm border-0">
    <h2>Tambah Anggota</h2>

    <?php if ($flash): ?>
        <p class="flash"><?php echo e($flash['pesan']); ?></p>
    <?php endif; ?>

    <form id="form-tambah" method="post" action="../proses/proses_tambah_anggota.php" class="row g-3">
        <?php echo csrf_field(); ?>
        <div class="col-md-6">
            <label for="nama" class="form-label">Nama Anggota Yayasan</label>
            <input type="text" id="nama" name="nama" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="no_anggota" class="form-label">Nomor Anggota</label>
            <input type="text" id="no_anggota" name="no_anggota" class="form-control" placeholder="Contoh: RQ-0001" required>
        </div>
        <div class="col-md-8">
            <label for="alamat" class="form-label">Alamat</label>
            <input type="text" id="alamat" name="alamat" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label for="no_hp" class="form-label">No. HP</label>
            <input type="text" id="no_hp" name="no_hp" class="form-control">
        </div>
        <div class="col-md-6">
            <label for="status" class="form-label">Status Keanggotaan</label>
            <select id="status" name="status" class="form-select" required>
                <option value="aktif" selected>Aktif</option>
                <option value="nonaktif">Nonaktif</option>
            </select>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Simpan Anggota</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
