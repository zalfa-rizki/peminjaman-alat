<?php
$pageTitle='Daftar Alat';
require 'views/layouts/admin_header.php';
?>
<div class="app-page-head"><div><h1 class="app-title">Daftar Alat</h1><p class="app-subtitle">Ringkasan alat yang tersedia di inventory.</p></div></div>
<div class="app-card overflow-hidden"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>ID</th><th>Nama Alat</th><th>Stok</th></tr></thead><tbody>
<?php if(empty($data)): ?><tr><td colspan="3"><div class="app-empty"><div class="app-empty-icon"><i class="bi bi-box-seam"></i></div><div class="fw-semibold">Belum ada data alat</div></div></td></tr><?php else: foreach($data as $row): ?><tr><td>#<?= (int)$row['id']; ?></td><td class="fw-semibold"><?= htmlspecialchars($row['nama_alat']); ?></td><td><?= (int)$row['stok']; ?></td></tr><?php endforeach; endif; ?>
</tbody></table></div></div>
<?php require 'views/layouts/admin_footer.php'; ?>
