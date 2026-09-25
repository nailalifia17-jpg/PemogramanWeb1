<?php
$page_title = "Tambah Warga";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>

<div class="card shadow-sm border-0">
    <h2>Tambah Warga</h2>

    <?php if ($flash): ?>
        <p class="flash"><?php echo htmlspecialchars($flash['pesan']); ?></p>
    <?php endif; ?>

    <form id="form-tambah" method="post" action="../proses/proses_tambah_warga.php" class="row g-3">
        <div class="col-md-6">
            <label for="nama" class="form-label">Nama Kepala Keluarga</label>
            <input type="text" id="nama" name="nama" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="no_kk" class="form-label">No. KK</label>
            <input type="text" id="no_kk" name="no_kk" class="form-control" required>
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
            <label for="status" class="form-label">Status Kependudukan</label>
            <select id="status" name="status" class="form-select" role="status" aria-label="Status kependudukan" required>
                <option value="aktif" selected>Aktif</option>
                <option value="pindah">Pindah</option>
            </select>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Simpan Warga</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>