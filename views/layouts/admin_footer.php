        </div>
    </main>
</div>
<nav class="app-bottom-nav" aria-label="Menu utama">
    <?php $cp=$_GET['page']??'dashboard'; ?>
    <a href="index.php?page=dashboard" class="<?= $cp==='dashboard'?'active':''; ?>"><i class="bi bi-grid"></i><span>Beranda</span></a>
    <a href="index.php?page=kelola-user" class="<?= $cp==='kelola-user'?'active':''; ?>"><i class="bi bi-people"></i><span>User</span></a>
    <a href="index.php?page=kelola-alat" class="<?= $cp==='kelola-alat'?'active':''; ?>"><i class="bi bi-tools"></i><span>Alat</span></a>
    <a href="index.php?page=log-aktivitas" class="<?= $cp==='log-aktivitas'?'active':''; ?>"><i class="bi bi-clock-history"></i><span>Log</span></a>
    <a href="index.php?page=logout"><i class="bi bi-box-arrow-left"></i><span>Keluar</span></a>
</nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
