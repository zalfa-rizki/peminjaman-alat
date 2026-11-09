        </div>
    </main>
</div>
<nav class="app-bottom-nav" aria-label="Menu utama">
    <?php $rl=$_SESSION['role']??''; ?>
    <a href="index.php?page=dashboard" class="active"><i class="bi bi-grid"></i><span>Beranda</span></a>
    <?php if($rl==='petugas'): ?>
    <a href="index.php?page=dashboard#tabel-pending"><i class="bi bi-hourglass-split"></i><span>Permintaan</span></a>
    <a href="index.php?page=dashboard#tabel-berjalan"><i class="bi bi-arrow-left-right"></i><span>Aktif</span></a>
    <a href="index.php?page=dashboard#tabel-pengembalian"><i class="bi bi-box-arrow-in-down"></i><span>Kembali</span></a>
    <?php else: ?>
    <a href="index.php?page=dashboard#katalog"><i class="bi bi-box-seam"></i><span>Katalog</span></a>
    <a href="index.php?page=dashboard#aktivitas"><i class="bi bi-clock-history"></i><span>Aktivitas</span></a>
    <?php endif; ?>
    <a href="index.php?page=logout"><i class="bi bi-box-arrow-left"></i><span>Keluar</span></a>
</nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
