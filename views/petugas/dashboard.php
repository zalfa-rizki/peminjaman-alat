<?php
$pageTitle = 'Dashboard Petugas';
require 'views/layouts/role_header.php';
$jumlahPending = (int) $db->query("SELECT COUNT(*) AS total FROM peminjaman WHERE status_pinjam = 'pending'")->fetch_assoc()['total'];
$jumlahBerjalan = (int) $db->query("SELECT COUNT(*) AS total FROM peminjaman WHERE status_pinjam = 'disetujui'")->fetch_assoc()['total'];
$jumlahPengembalian = (int) $db->query("SELECT COUNT(*) AS total FROM peminjaman WHERE status_pinjam = 'menunggu_pengembalian'")->fetch_assoc()['total'];
?>
<div class="app-page-head">
    <div><h1 class="app-title">Dashboard Petugas</h1><p class="app-subtitle">Kelola permintaan pinjam dan konfirmasi barang yang dikembalikan.</p></div>
    <span class="app-badge app-badge-dark" style="padding:8px 14px;">Petugas</span>
</div>

<?php if (($_GET['status'] ?? '') === 'selesai'): ?>
<div class="alert alert-success bg-white text-dark d-flex align-items-center justify-content-between mb-4" role="alert">
    <div><i class="bi bi-check-circle me-2"></i><strong>Berhasil!</strong> Barang telah dikonfirmasi kembali dan stok diperbarui.<?php $denda = (float) ($_GET['denda'] ?? 0); if ($denda > 0): ?><span class="app-badge app-badge-danger ms-2">Denda Rp <?= number_format($denda, 0, ',', '.'); ?></span><?php endif; ?></div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
</div>
<?php elseif (($_GET['status'] ?? '') === 'kembali_gagal'): ?>
<div class="alert alert-danger bg-white mb-4" role="alert">Pengembalian gagal diproses. Periksa apakah peminjaman masih aktif dan belum pernah diselesaikan.</div>
<?php elseif (($_GET['aksi'] ?? '') === 'gagal'): ?>
<div class="alert alert-danger bg-white mb-4" role="alert">Aksi gagal diproses. Data mungkin sudah diproses oleh petugas lain.</div>
<?php elseif (($_GET['status'] ?? '') === 'ditolak'): ?>
<div class="alert alert-success bg-white mb-4" role="alert">Pengajuan ditolak dan stok dikembalikan ke inventaris.</div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <div class="col-md-4"><a href="#tabel-pending" class="text-decoration-none"><div class="app-stat"><div class="app-stat-icon" style="background:var(--amber-soft);color:var(--warn)"><i class="bi bi-hourglass-split"></i></div><div class="app-stat-label">Menunggu Persetujuan</div><div class="app-stat-value"><?= $jumlahPending; ?></div><div class="small mt-2" style="color:#8a8a88;">Tinjau permintaan →</div></div></a></div>
    <div class="col-md-4"><a href="#tabel-berjalan" class="text-decoration-none"><div class="app-stat"><div class="app-stat-icon" style="background:var(--brand);color:#fff"><i class="bi bi-arrow-left-right"></i></div><div class="app-stat-label">Peminjaman Aktif</div><div class="app-stat-value"><?= $jumlahBerjalan; ?></div><div class="small mt-2" style="color:#8a8a88;">Peminjaman berjalan →</div></div></a></div>
    <div class="col-md-4"><a href="#tabel-pengembalian" class="text-decoration-none"><div class="app-stat"><div class="app-stat-icon" style="background:#e6f4ea;color:#237344"><i class="bi bi-box-arrow-in-down"></i></div><div class="app-stat-label">Permintaan Pengembalian</div><div class="app-stat-value"><?= $jumlahPengembalian; ?></div><div class="small mt-2" style="color:#8a8a88;">Konfirmasi barang →</div></div></a></div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="app-card overflow-hidden mb-3" id="tabel-pending">
            <div class="app-card-header"><h2 class="app-card-title"><i class="bi bi-list-stars me-2"></i>Permintaan Peminjaman</h2></div>
            <div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>User</th><th>Alat/Barang</th><th class="text-end">Aksi</th></tr></thead><tbody>
            <?php $res = $db->query("SELECT p.id, u.username, a.nama_alat FROM peminjaman p JOIN users u ON p.user_id = u.id JOIN alat a ON p.alat_id = a.id WHERE p.status_pinjam = 'pending' ORDER BY p.id DESC"); if ($res->num_rows === 0): ?>
                <tr><td colspan="3"><div class="app-empty"><div class="app-empty-icon"><i class="bi bi-inbox"></i></div><div class="fw-semibold">Tidak ada permintaan baru</div></div></td></tr>
            <?php else: while ($row = $res->fetch_assoc()): ?>
                <tr><td class="fw-semibold"><?= htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); ?></td><td><span class="app-badge app-badge-neutral"><?= htmlspecialchars($row['nama_alat'], ENT_QUOTES, 'UTF-8'); ?></span></td><td class="text-end"><div class="d-inline-flex gap-2"><form action="index.php?page=acc-peminjaman" method="POST"><input type="hidden" name="id_peminjaman" value="<?= (int) $row['id']; ?>"><button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check2-circle me-1"></i>Setujui</button></form><form action="index.php?page=tolak-peminjaman" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menolak pengajuan ini?');"><input type="hidden" name="id_peminjaman" value="<?= (int) $row['id']; ?>"><button type="submit" class="btn btn-ghost btn-sm">Tolak</button></form></div></td></tr>
            <?php endwhile; endif; ?></tbody></table></div>
        </div>

        <div class="app-card overflow-hidden mb-3" id="tabel-pengembalian">
            <div class="app-card-header"><h2 class="app-card-title"><i class="bi bi-box-arrow-in-down me-2"></i>Permintaan Pengembalian</h2></div>
            <div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Peminjam</th><th>Alat</th><th>Rencana Kembali</th><th class="text-end">Aksi</th></tr></thead><tbody>
            <?php $res = $db->query("SELECT p.id, p.tanggal_kembali_seharusnya, u.username, a.nama_alat FROM peminjaman p JOIN users u ON p.user_id = u.id JOIN alat a ON p.alat_id = a.id WHERE p.status_pinjam = 'menunggu_pengembalian' ORDER BY p.id DESC"); if ($res->num_rows === 0): ?>
                <tr><td colspan="4"><div class="app-empty"><div class="app-empty-icon"><i class="bi bi-inbox"></i></div><div class="fw-semibold">Belum ada permintaan pengembalian</div></div></td></tr>
            <?php else: while ($row = $res->fetch_assoc()): ?>
                <tr><td class="fw-semibold"><?= htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); ?></td><td><?= htmlspecialchars($row['nama_alat'], ENT_QUOTES, 'UTF-8'); ?></td><td><?= !empty($row['tanggal_kembali_seharusnya']) ? date('d M Y', strtotime($row['tanggal_kembali_seharusnya'])) : '-'; ?></td><td class="text-end"><form action="index.php?page=proses-kembali" method="POST" onsubmit="return confirm('Konfirmasi barang sudah diterima secara fisik? Stok akan bertambah satu unit.');"><input type="hidden" name="id_peminjaman" value="<?= (int) $row['id']; ?>"><button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check2-circle me-1"></i>Terima Barang</button></form></td></tr>
            <?php endwhile; endif; ?></tbody></table></div>
        </div>

        <div class="app-card overflow-hidden" id="tabel-berjalan">
            <div class="app-card-header"><h2 class="app-card-title"><i class="bi bi-arrow-repeat me-2"></i>Peminjaman Aktif</h2><span class="small" style="color:#8a8a88;">Konfirmasi manual tetap tersedia</span></div>
            <div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Peminjam</th><th>Alat</th><th class="text-end">Aksi</th></tr></thead><tbody>
            <?php $res = $db->query("SELECT p.id, u.username, a.nama_alat FROM peminjaman p JOIN users u ON p.user_id = u.id JOIN alat a ON p.alat_id = a.id WHERE p.status_pinjam = 'disetujui' ORDER BY p.id DESC"); if ($res->num_rows === 0): ?>
                <tr><td colspan="3"><div class="app-empty"><div class="app-empty-icon"><i class="bi bi-inbox"></i></div><div class="fw-semibold">Tidak ada peminjam aktif</div></div></td></tr>
            <?php else: while ($row = $res->fetch_assoc()): ?>
                <tr><td><?= htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); ?></td><td><span class="app-badge app-badge-neutral"><?= htmlspecialchars($row['nama_alat'], ENT_QUOTES, 'UTF-8'); ?></span></td><td class="text-end"><form action="index.php?page=proses-kembali" method="POST" onsubmit="return confirm('Pastikan barang sudah diterima dari peminjam. Lanjutkan?');"><input type="hidden" name="id_peminjaman" value="<?= (int) $row['id']; ?>"><button type="submit" class="btn btn-outline-primary btn-sm"><i class="bi bi-box-arrow-in-down me-1"></i>Konfirmasi Kembali</button></form></td></tr>
            <?php endwhile; endif; ?></tbody></table></div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="app-card overflow-hidden">
            <div class="app-card-header"><h2 class="app-card-title"><i class="bi bi-clock-history me-2"></i>Baru Saja Kembali</h2></div>
            <div class="list-group list-group-flush">
                <?php $res = $db->query("SELECT p.*, u.username, a.nama_alat FROM peminjaman p JOIN users u ON p.user_id = u.id JOIN alat a ON p.alat_id = a.id WHERE p.status_pinjam = 'kembali' ORDER BY p.tanggal_kembali_realitas DESC, p.id DESC LIMIT 5"); if ($res->num_rows === 0): ?>
                    <div class="app-empty"><div class="app-empty-icon"><i class="bi bi-inbox"></i></div><div class="fw-semibold">Belum ada pengembalian</div></div>
                <?php else: while ($row = $res->fetch_assoc()): ?>
                    <div class="list-group-item p-3"><div class="d-flex justify-content-between align-items-start gap-2"><div><div class="fw-semibold"><?= htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); ?></div><div class="small" style="color:#8a8a88;"><?= htmlspecialchars($row['nama_alat'], ENT_QUOTES, 'UTF-8'); ?></div></div><?php if ((int) $row['denda'] > 0): ?><span class="app-badge app-badge-danger">Rp <?= number_format((float) $row['denda'], 0, ',', '.'); ?></span><?php else: ?><span class="app-badge app-badge-neutral">Selesai</span><?php endif; ?></div><div class="small mt-2" style="color:#b8b8b5;">Kembali: <?= !empty($row['tanggal_kembali_realitas']) ? date('d M Y', strtotime($row['tanggal_kembali_realitas'])) : '-'; ?></div></div>
                <?php endwhile; endif; ?>
            </div>
            <div class="p-3 text-center border-top" style="border-color:#f0f0ee!important;"><a href="index.php?page=riwayat-lengkap" class="small fw-semibold text-decoration-none">Lihat Semua Riwayat</a></div>
        </div>
    </div>
</div>
<?php require 'views/layouts/role_footer.php'; ?>
