<?php
$pageTitle = 'Log Aktivitas';
require 'views/layouts/admin_header.php';
?>
<div class="app-page-head">
    <div><h1 class="app-title">Aktivitas Sistem</h1><p class="app-subtitle">Monitor perubahan data dan aktivitas penting dalam sistem.</p></div>
    <button class="btn btn-ghost" onclick="location.reload()"><i class="bi bi-arrow-clockwise me-1"></i> Refresh</button>
</div>
<div class="app-card overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Tanggal & Waktu</th><th>Pengguna</th><th>Aktivitas</th><th>Detail Keterangan</th></tr></thead>
            <tbody>
            <?php if ($logs->num_rows > 0): while($row = $logs->fetch_assoc()):
                $aksi = strtolower($row['aksi']);
                $badge = 'app-badge-neutral';
                if (strpos($aksi,'tambah')!==false) $badge='app-badge-success';
                elseif (strpos($aksi,'hapus')!==false) $badge='app-badge-danger';
                elseif (strpos($aksi,'edit')!==false || strpos($aksi,'update')!==false) $badge='app-badge-dark';
                elseif (strpos($aksi,'login')!==false) $badge='app-badge-warning';
            ?>
            <tr>
                <td><div class="fw-semibold"><?= date('d M Y', strtotime($row['tanggal'])); ?></div><div class="small" style="color:#8a8a88;"><?= date('H:i:s', strtotime($row['tanggal'])); ?></div></td>
                <td><div class="d-flex align-items-center gap-3"><div class="app-avatar"><?= strtoupper(substr($row['username'] ?? 'S',0,1)); ?></div><span class="fw-medium"><?= htmlspecialchars($row['username'] ?? 'System Admin'); ?></span></div></td>
                <td><span class="app-badge <?= $badge; ?>"><i class="bi bi-dot"></i><?= ucfirst(htmlspecialchars($row['aksi'])); ?></span></td>
                <td style="max-width:360px;"><div class="text-truncate" title="<?= htmlspecialchars($row['keterangan']); ?>" style="color:#666; font-size:13px;"><?= htmlspecialchars($row['keterangan']); ?></div></td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="4"><div class="app-empty"><div class="app-empty-icon"><i class="bi bi-inbox"></i></div><div class="fw-semibold">Tidak ada rekaman aktivitas</div><div class="small">Aktivitas sistem akan tampil di sini.</div></div></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="p-3 text-center border-top" style="border-color:#f0f0ee!important;"><small style="color:#b8b8b5;">Data diperbarui oleh server</small></div>
</div>
<?php require 'views/layouts/admin_footer.php'; ?>
