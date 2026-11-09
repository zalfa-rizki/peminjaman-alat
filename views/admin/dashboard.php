<?php
global $db;
$qAlat = $db->query("SELECT COUNT(*) as total FROM alat");
$totalAlat = $qAlat->fetch_assoc()['total'];
$qAktif = $db->query("SELECT COUNT(*) as total FROM peminjaman WHERE status_pinjam IN ('disetujui', 'menunggu_pengembalian')");
$totalAktif = $qAktif->fetch_assoc()['total'];
$qPending = $db->query("SELECT COUNT(*) as total FROM peminjaman WHERE status_pinjam = 'pending'");
$totalPending = $qPending->fetch_assoc()['total'];
$qUser = $db->query("SELECT COUNT(*) as total FROM users WHERE role != 'admin'");
$totalUser = $qUser->fetch_assoc()['total'];
$pageTitle = 'Dashboard';
require 'views/layouts/admin_header.php';
?>
<div class="app-page-head">
    <div>
        <h1 class="app-title">Ringkasan</h1>
        <p class="app-subtitle">Selamat datang kembali, <?= htmlspecialchars($_SESSION['nama']); ?></p>
    </div>
    <span class="app-badge app-badge-dark" style="padding:8px 14px;">Administrator</span>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3"><div class="app-stat"><div class="app-stat-icon"><i class="bi bi-box-seam"></i></div><div class="app-stat-label">Total Alat</div><div class="app-stat-value"><?= $totalAlat; ?></div></div></div>
    <div class="col-6 col-xl-3"><div class="app-stat"><div class="app-stat-icon" style="background:var(--brand);color:#fff;"><i class="bi bi-arrow-left-right"></i></div><div class="app-stat-label">Peminjaman Aktif</div><div class="app-stat-value"><?= $totalAktif; ?></div></div></div>
    <div class="col-6 col-xl-3"><div class="app-stat"><div class="app-stat-icon" style="background:var(--amber-soft);color:var(--warn);"><i class="bi bi-hourglass-split"></i></div><div class="app-stat-label">Menunggu</div><div class="app-stat-value"><?= $totalPending; ?></div></div></div>
    <div class="col-6 col-xl-3"><div class="app-stat"><div class="app-stat-icon"><i class="bi bi-people"></i></div><div class="app-stat-label">Total User</div><div class="app-stat-value"><?= $totalUser; ?></div></div></div>
</div>

<div class="app-card overflow-hidden">
    <div class="app-card-header d-flex justify-content-between align-items-center">
        <h2 class="app-card-title">Aktivitas Terbaru</h2>
        <span class="small" style="color:#b8b8b5;">5 transaksi terakhir</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Tanggal</th><th>User</th><th>Status</th><th>Alat</th></tr></thead>
            <tbody>
            <?php
            $qLog = $db->query("SELECT p.*, u.username, a.nama_alat FROM peminjaman p JOIN users u ON p.user_id = u.id JOIN alat a ON p.alat_id = a.id ORDER BY p.id DESC LIMIT 5");
            if ($qLog->num_rows === 0): ?>
                <tr><td colspan="4"><div class="app-empty"><div class="app-empty-icon"><i class="bi bi-inbox"></i></div><div class="fw-semibold">Belum ada aktivitas</div><div class="small">Transaksi terbaru akan muncul di sini.</div></div></td></tr>
            <?php else: while($log = $qLog->fetch_assoc()):
                $s = $log['status_pinjam'];
                $map = [
                    'disetujui'=>['app-badge-success','Disetujui'],
                    'ditolak'=>['app-badge-danger','Ditolak'],
                    'kembali'=>['app-badge-neutral','Selesai'],
                    'menunggu_pengembalian'=>['app-badge-warning','Menunggu pengembalian'],
                    'pending'=>['app-badge-warning','Pending']
                ];
                [$badgeClass,$label] = $map[$s] ?? ['app-badge-neutral',ucfirst($s)];
            ?>
            <tr>
                <td style="color:#666;"><?= date('d/m/Y', strtotime($log['tanggal_pinjam'])); ?></td>
                <td class="fw-semibold"><?= htmlspecialchars($log['username']); ?></td>
                <td><span class="app-badge <?= $badgeClass; ?>"><?= $label; ?></span></td>
                <td><?= htmlspecialchars($log['nama_alat']); ?></td>
            </tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require 'views/layouts/admin_footer.php'; ?>
