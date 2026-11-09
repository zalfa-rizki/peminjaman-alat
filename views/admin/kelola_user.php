<?php
$pageTitle = 'Kelola User';
require 'views/layouts/admin_header.php';
$userRows = [];
while ($r = $users->fetch_assoc()) { $userRows[] = $r; }
?>
<div class="app-page-head">
    <div><h1 class="app-title">Kelola User</h1><p class="app-subtitle">Kelola akun petugas dan peminjam sistem.</p></div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="bi bi-plus-lg me-1"></i> Tambah User</button>
</div>
<div class="app-card overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th style="width:40%">Informasi Pengguna</th><th style="width:30%">Level Akses</th><th class="text-end" style="width:30%">Tindakan</th></tr></thead>
            <tbody>
            <?php if (!$userRows): ?>
                <tr><td colspan="3"><div class="app-empty"><div class="app-empty-icon"><i class="bi bi-people"></i></div><div class="fw-semibold">Belum ada user</div><div class="small">Tambahkan akun untuk mulai mengelola pengguna.</div></div></td></tr>
            <?php else: foreach($userRows as $row): ?>
                <tr>
                    <td><div class="d-flex align-items-center gap-3"><div class="app-avatar"><?= strtoupper(substr($row['username'],0,1)); ?></div><div><div class="fw-semibold"><?= htmlspecialchars($row['username']); ?></div><div class="small" style="color:#8a8a88;">UID-<?= (int)$row['id']; ?></div></div></div></td>
                    <td><?php if($row['role']==='petugas'): ?><span class="app-badge app-badge-dark"><i class="bi bi-shield-lock-fill"></i> Petugas</span><?php else: ?><span class="app-badge app-badge-neutral"><i class="bi bi-person-fill"></i> Peminjam</span><?php endif; ?></td>
                    <td class="text-end"><div class="d-inline-flex gap-2"><button class="app-icon-btn" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id']; ?>" title="Edit"><i class="bi bi-pencil"></i></button><a href="index.php?page=hapus-user&id=<?= $row['id']; ?>" class="app-icon-btn" title="Hapus" onclick="return confirm('Hapus user ini?')"><i class="bi bi-trash3"></i></a></div></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php foreach($userRows as $row): ?>
<div class="modal fade" id="modalEdit<?= $row['id']; ?>" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><form action="index.php?page=update-user" method="POST" class="modal-content">
    <div class="modal-header px-4 pt-4"><h5 class="modal-title fw-bold">Edit Pengguna</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body p-4"><input type="hidden" name="id" value="<?= $row['id']; ?>"><div class="mb-3"><label class="form-label">Username</label><input type="text" name="username" class="form-control" value="<?= htmlspecialchars($row['username']); ?>" required></div><div class="mb-3"><label class="form-label">Ganti Password</label><input type="password" name="password" class="form-control" placeholder="Isi hanya jika ingin mengganti"></div><div><label class="form-label">Role Akses</label><select name="role" class="form-select" required><option value="peminjam" <?= $row['role']==='peminjam'?'selected':''; ?>>Peminjam</option><option value="petugas" <?= $row['role']==='petugas'?'selected':''; ?>>Petugas</option></select></div></div>
    <div class="modal-footer px-4 pb-4"><button type="button" class="btn btn-ghost flex-fill" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary flex-fill">Update Data</button></div>
</form></div></div>
<?php endforeach; ?>

<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><form action="index.php?page=simpan-user" method="POST" class="modal-content">
    <div class="modal-header px-4 pt-4"><h5 class="modal-title fw-bold">Tambah Pengguna Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body p-4"><div class="mb-3"><label class="form-label">Username</label><input type="text" name="username" class="form-control" placeholder="Masukkan username" required></div><div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" placeholder="Masukkan password" required></div><div><label class="form-label">Role / Hak Akses</label><select name="role" class="form-select" required><option value="" selected disabled>Pilih Role</option><option value="peminjam">Peminjam</option><option value="petugas">Petugas</option></select></div></div>
    <div class="modal-footer px-4 pb-4"><button type="button" class="btn btn-ghost flex-fill" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary flex-fill">Simpan User</button></div>
</form></div></div>
<?php require 'views/layouts/admin_footer.php'; ?>
