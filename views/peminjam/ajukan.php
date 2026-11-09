<?php
$pageTitle='Ajukan Peminjaman';
require 'views/layouts/role_header.php';
?>
<div class="app-page-head"><div><h1 class="app-title">Ajukan Peminjaman</h1><p class="app-subtitle">Lengkapi jadwal penggunaan alat yang dipilih.</p></div><a href="index.php?page=dashboard" class="btn btn-ghost"><i class="bi bi-arrow-left me-1"></i> Kembali</a></div>
<div class="row justify-content-center"><div class="col-lg-7 col-xl-6"><div class="app-card p-0 overflow-hidden">
    <div class="app-card-header"><h2 class="app-card-title"><i class="bi bi-file-earmark-plus me-2"></i><?= htmlspecialchars($alat['nama_alat']); ?></h2></div>
    <div class="p-4">
        <div class="d-flex align-items-center gap-3 mb-4 p-3" style="background:#f5f5f3;border:1px solid #eee;border-radius:14px;"><div class="app-avatar"><i class="bi bi-box-seam"></i></div><div><div class="small" style="color:#8a8a88;">Stok tersedia</div><div class="fw-bold"><?= (int)$alat['stok']; ?> unit</div></div></div>
        <form action="index.php?page=proses-ajukan" method="POST">
            <input type="hidden" name="alat_id" value="<?= $alat['id']; ?>">
            <div class="mb-3"><label class="form-label">Tanggal Pinjam</label><input type="date" name="tanggal_pinjam" class="form-control" value="<?= date('Y-m-d'); ?>" required></div>
            <div class="mb-3"><label class="form-label">Rencana Tanggal Kembali</label><input type="date" name="tanggal_kembali_seharusnya" class="form-control" required></div>
            <div class="alert alert-warning bg-white"><i class="bi bi-exclamation-triangle me-2"></i><strong>Aturan denda:</strong> keterlambatan pengembalian dikenakan denda otomatis Rp 20.000 per hari.</div>
            <div class="d-flex gap-2 mt-4"><a href="index.php?page=dashboard" class="btn btn-ghost flex-fill">Batal</a><button type="submit" class="btn btn-primary flex-fill">Kirim Pengajuan</button></div>
        </form>
    </div>
</div></div></div>
<?php require 'views/layouts/role_footer.php'; ?>
