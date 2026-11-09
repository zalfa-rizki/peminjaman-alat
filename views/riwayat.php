<?php
$pageTitle = 'Riwayat Peminjaman';
$role = $_SESSION['role'] ?? '';
$userId = (int) ($_SESSION['user_id'] ?? 0);
if (in_array($role, ['admin', 'petugas'], true)) {
    if ($role === 'admin') {
        require 'views/layouts/admin_header.php';
    } else {
        require 'views/layouts/role_header.php';
    }
    $stmt = $db->prepare("SELECT p.*, u.username, a.nama_alat FROM peminjaman p JOIN users u ON u.id = p.user_id JOIN alat a ON a.id = p.alat_id ORDER BY p.id DESC");
} else {
    require 'views/layouts/role_header.php';
    $stmt = $db->prepare("SELECT p.*, u.username, a.nama_alat FROM peminjaman p JOIN users u ON u.id = p.user_id JOIN alat a ON a.id = p.alat_id WHERE p.user_id = ? ORDER BY p.id DESC");
    $stmt->bind_param('i', $userId);
}
$stmt->execute();
$rows = $stmt->get_result();
$statusMap = [
    'pending' => ['app-badge-warning', 'Menunggu persetujuan'],
    'disetujui' => ['app-badge-success', 'Disetujui'],
    'menunggu_pengembalian' => ['app-badge-warning', 'Menunggu konfirmasi pengembalian'],
    'kembali' => ['app-badge-neutral', 'Selesai'],
    'ditolak' => ['app-badge-danger', 'Ditolak'],
];
?>
<div class="app-page-head">
    <div><h1 class="app-title">Riwayat Peminjaman</h1><p class="app-subtitle"><?= in_array($role, ['admin', 'petugas'], true) ? 'Riwayat peminjaman seluruh pengguna.' : 'Semua pengajuan, status, dan pengembalian barang milikmu.'; ?></p></div>
    <a href="index.php?page=dashboard" class="btn btn-ghost"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>
<div class="app-card overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><?php if (in_array($role, ['admin', 'petugas'], true)): ?><th>Peminjam</th><?php endif; ?><th>Alat</th><th>Tanggal Pinjam</th><th>Rencana Kembali</th><th>Pengembalian</th><th>Status</th><th>Denda</th></tr></thead>
            <tbody>
            <?php if ($rows->num_rows === 0): ?><tr><td colspan="<?= in_array($role, ['admin', 'petugas'], true) ? 7 : 6; ?>"><div class="app-empty"><div class="app-empty-icon"><i class="bi bi-inbox"></i></div><div class="fw-semibold">Belum ada riwayat peminjaman</div></div></td></tr>
            <?php else: while ($row = $rows->fetch_assoc()):
                $status = $row['status_pinjam'] ?: 'pending';
                [$badgeClass, $label] = $statusMap[$status] ?? ['app-badge-neutral', ucfirst($status)];
            ?>
                <tr>
                    <?php if (in_array($role, ['admin', 'petugas'], true)): ?><td><?= htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); ?></td><?php endif; ?>
                    <td><?= htmlspecialchars($row['nama_alat'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= !empty($row['tanggal_pinjam']) ? date('d M Y', strtotime($row['tanggal_pinjam'])) : '-'; ?></td>
                    <td><?= !empty($row['tanggal_kembali_seharusnya']) ? date('d M Y', strtotime($row['tanggal_kembali_seharusnya'])) : '-'; ?></td>
                    <td><?= !empty($row['tanggal_kembali_realitas']) ? date('d M Y', strtotime($row['tanggal_kembali_realitas'])) : '-'; ?></td>
                    <td><span class="app-badge <?= $badgeClass; ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></span></td>
                    <td><?= (int) $row['denda'] > 0 ? 'Rp ' . number_format((float) $row['denda'], 0, ',', '.') : '-'; ?></td>
                </tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$stmt->close();
if ($role === 'admin') {
    require 'views/layouts/admin_footer.php';
} else {
    require 'views/layouts/role_footer.php';
}
?>
