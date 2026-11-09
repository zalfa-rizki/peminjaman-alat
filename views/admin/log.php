<?php
$pageTitle='Log Aktivitas Sistem';
require 'views/layouts/admin_header.php';
?>
<div class="app-page-head"><div><h1 class="app-title">Log Aktivitas Sistem</h1><p class="app-subtitle">Catatan aktivitas pengguna dan perubahan data.</p></div></div>
<div class="app-card overflow-hidden"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>User</th><th>Aksi</th><th>Waktu</th></tr></thead><tbody><tr><td colspan="3"><div class="app-empty"><div class="app-empty-icon"><i class="bi bi-clock-history"></i></div><div class="fw-semibold">Gunakan menu Log Aktivitas</div><div class="small">Halaman ini mempertahankan route lama, sementara tampilan log aktif tersedia di menu utama.</div></div></td></tr></tbody></table></div></div>
<?php require 'views/layouts/admin_footer.php'; ?>
