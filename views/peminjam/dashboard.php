
<?php
global $db;

$pageTitle = 'Dashboard Peminjam';
$userId = (int) ($_SESSION['user_id'] ?? 0);

// Pastikan koneksi database tersedia sebelum menjalankan query.
if (!isset($db) || !($db instanceof mysqli)) {
    http_response_code(500);
    exit(
        'Koneksi database tidak tersedia. ' .
        'Periksa config/database.php dan index.php.'
    );
}

require 'views/layouts/role_header.php';

// =====================================================
// PEMINJAMAN AKTIF
// =====================================================
$activeLoansStmt = $db->prepare("
    SELECT
        p.id,
        p.status_pinjam,
        p.tanggal_pinjam,
        p.tanggal_kembali_seharusnya,
        a.nama_alat
    FROM peminjaman p
    INNER JOIN alat a ON p.alat_id = a.id
    WHERE p.user_id = ?
      AND p.status_pinjam IN (
          'disetujui',
          'menunggu_pengembalian'
      )
    ORDER BY p.id DESC
");

$activeLoansStmt->bind_param('i', $userId);
$activeLoansStmt->execute();
$activeLoans = $activeLoansStmt->get_result();

// =====================================================
// RIWAYAT AKTIVITAS
// =====================================================
$activityStmt = $db->prepare("
    SELECT
        p.*,
        a.nama_alat
    FROM peminjaman p
    INNER JOIN alat a ON p.alat_id = a.id
    WHERE p.user_id = ?
    ORDER BY p.id DESC
    LIMIT 6
");

$activityStmt->bind_param('i', $userId);
$activityStmt->execute();
$activities = $activityStmt->get_result();

// =====================================================
// PESAN NOTIFIKASI
// =====================================================
$messageText = [
    'berhasil_pinjam' => [
        'success',
        'Pengajuan terkirim. Mohon tunggu verifikasi petugas.'
    ],

    'pengembalian_diajukan' => [
        'success',
        'Permintaan pengembalian terkirim. Serahkan barang kepada petugas untuk dikonfirmasi.'
    ],

    'gagal_stok' => [
        'danger',
        'Stok barang tidak mencukupi. Silakan muat ulang katalog dan coba lagi.'
    ],

    'gagal_sistem' => [
        'danger',
        'Pengajuan gagal diproses. Periksa data lalu coba lagi.'
    ],

    'pengembalian_gagal' => [
        'danger',
        'Pengembalian gagal diajukan. Pastikan peminjaman masih aktif.'
    ],

    'data_tanggal_tidak_valid' => [
        'danger',
        'Tanggal peminjaman tidak valid. Tanggal kembali harus sama atau setelah tanggal pinjam.'
    ],

    'alat_tidak_ditemukan' => [
        'danger',
        'Data alat tidak ditemukan.'
    ]
];

$messageKey = $_GET['pesan'] ?? '';
?>

<div class="app-page-head">
    <div>
        <h1 class="app-title">
            Halo,
            <?= htmlspecialchars(
                $_SESSION['nama'] ?? 'Peminjam',
                ENT_QUOTES,
                'UTF-8'
            ); ?>
            👋
        </h1>

        <p class="app-subtitle">
            Pilih alat yang ingin kamu pinjam dan pantau status pengajuanmu.
        </p>
    </div>

    <span
        class="app-badge app-badge-neutral"
        style="padding:8px 14px;"
    >
        Peminjam
    </span>
</div>

<?php if (isset($messageText[$messageKey])): ?>
    <?php
        [$alertType, $alertMessage] = $messageText[$messageKey];
    ?>

    <div
        class="alert alert-<?= htmlspecialchars(
            $alertType,
            ENT_QUOTES,
            'UTF-8'
        ); ?> bg-white d-flex align-items-center justify-content-between mb-4"
        role="alert"
    >
        <div>
            <i class="bi <?= $alertType === 'success'
                ? 'bi-check-circle'
                : 'bi-x-circle'; ?> me-2"></i>

            <?= htmlspecialchars(
                $alertMessage,
                ENT_QUOTES,
                'UTF-8'
            ); ?>
        </div>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Tutup"
        ></button>
    </div>
<?php endif; ?>

<div class="row g-3">

    <!-- ==============================================
         KATALOG ALAT
    =============================================== -->
    <div class="col-lg-8" id="katalog">
        <div class="app-card overflow-hidden h-100">

            <div
                class="app-card-header d-flex justify-content-between align-items-center"
            >
                <h2 class="app-card-title">
                    <i class="bi bi-archive me-2"></i>
                    Katalog Alat
                </h2>

                <span class="app-badge app-badge-neutral">
                    <?= count($daftarAlat ?? []); ?> alat
                </span>
            </div>

            <?php if (empty($daftarAlat)): ?>

                <div class="app-empty">
                    <div class="app-empty-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>

                    <div class="fw-semibold">
                        Belum ada alat
                    </div>

                    <div class="small">
                        Alat yang bisa dipinjam akan muncul di sini.
                    </div>
                </div>

            <?php else: ?>

                <div class="alat-grid">

                    <?php foreach ($daftarAlat as $alat): ?>

                        <?php
                            $gambarPath = 'assets/img/alat/' .
                                ($alat['foto'] ?? '');

                            $tampilFoto = (
                                !empty($alat['foto']) &&
                                file_exists($gambarPath)
                            )
                                ? $gambarPath
                                : 'assets/img/alat/default.jpg';

                            $habis = (int) ($alat['stok'] ?? 0) <= 0;
                        ?>

                        <div class="alat-card <?= $habis ? 'habis' : ''; ?>">

                            <img
                                src="<?= htmlspecialchars(
                                    $tampilFoto,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>"
                                alt="<?= htmlspecialchars(
                                    $alat['nama_alat'] ?? 'Alat',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>"
                                loading="lazy"
                                onerror="this.style.visibility='hidden'"
                            >

                            <div class="alat-body">

                                <div class="alat-name">
                                    <?= htmlspecialchars(
                                        $alat['nama_alat'] ?? 'Alat',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>
                                </div>

                                <div class="alat-meta">

                                    <span>
                                        AL-<?= str_pad(
                                            (string) (int) $alat['id'],
                                            3,
                                            '0',
                                            STR_PAD_LEFT
                                        ); ?>
                                    </span>

                                    <span class="app-badge <?= $habis
                                        ? 'app-badge-danger'
                                        : 'app-badge-success'; ?>">

                                        <?= $habis
                                            ? 'Habis'
                                            : (int) $alat['stok'] . ' tersedia'; ?>

                                    </span>
                                </div>

                                <?php if (!$habis): ?>

                                    <form
                                        action="index.php?page=ajukan-pinjam"
                                        method="POST"
                                    >
                                        <input
                                            type="hidden"
                                            name="alat_id"
                                            value="<?= (int) $alat['id']; ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="btn btn-primary btn-sm"
                                        >
                                            Pinjam alat
                                        </button>
                                    </form>

                                <?php else: ?>

                                    <button
                                        class="btn btn-ghost btn-sm"
                                        disabled
                                    >
                                        Stok habis
                                    </button>

                                <?php endif; ?>

                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>
    </div>

    <!-- ==============================================
         PEMINJAMAN AKTIF DAN PENGEMBALIAN
    =============================================== -->
    <div class="col-lg-4" id="aktivitas">

        <div class="app-card overflow-hidden mb-3">

            <div class="app-card-header">
                <h2 class="app-card-title">
                    <i class="bi bi-arrow-left-right me-2"></i>
                    Peminjaman Aktif Saya
                </h2>
            </div>

            <div class="list-group list-group-flush">

                <?php if ($activeLoans->num_rows === 0): ?>

                    <div class="app-empty">
                        <div class="app-empty-icon">
                            <i class="bi bi-inbox"></i>
                        </div>

                        <div class="fw-semibold">
                            Tidak ada peminjaman aktif
                        </div>

                        <div class="small">
                            Peminjaman yang disetujui muncul di sini.
                        </div>
                    </div>

                <?php else: ?>

                    <?php while ($loan = $activeLoans->fetch_assoc()): ?>

                        <div class="list-group-item p-3">

                            <div class="fw-semibold mb-1">
                                <?= htmlspecialchars(
                                    $loan['nama_alat'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>
                            </div>

                            <div
                                class="small mb-2"
                                style="color:#8a8a88;"
                            >
                                Rencana kembali:

                                <?php if (
                                    !empty($loan['tanggal_kembali_seharusnya'])
                                ): ?>

                                    <?= date(
                                        'd M Y',
                                        strtotime(
                                            $loan['tanggal_kembali_seharusnya']
                                        )
                                    ); ?>

                                <?php else: ?>

                                    Belum ditentukan

                                <?php endif; ?>
                            </div>

                            <?php if (
                                $loan['status_pinjam'] === 'disetujui'
                            ): ?>

                                <form
                                    action="index.php?page=ajukan-pengembalian"
                                    method="POST"
                                    onsubmit="return confirm('Ajukan pengembalian barang ini? Stok akan tersedia kembali setelah petugas menerima barang.');"
                                >
                                    <input
                                        type="hidden"
                                        name="id_peminjaman"
                                        value="<?= (int) $loan['id']; ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-outline-primary btn-sm w-100"
                                    >
                                        <i class="bi bi-box-arrow-in-down me-1"></i>
                                        Ajukan Pengembalian
                                    </button>
                                </form>

                            <?php else: ?>

                                <span class="app-badge app-badge-warning">
                                    <i class="bi bi-hourglass-split me-1"></i>
                                    Menunggu konfirmasi petugas
                                </span>

                            <?php endif; ?>

                        </div>

                    <?php endwhile; ?>

                <?php endif; ?>

            </div>
        </div>

        <!-- ==========================================
             AKTIVITAS SAYA
        =========================================== -->
        <div class="app-card overflow-hidden">

            <div class="app-card-header">
                <h2 class="app-card-title">
                    <i class="bi bi-clock-history me-2"></i>
                    Aktivitas Saya
                </h2>
            </div>

            <div class="list-group list-group-flush">

                <?php if ($activities->num_rows > 0): ?>

                    <?php while ($row = $activities->fetch_assoc()): ?>

                        <?php
                            $status = $row['status_pinjam'] ?? 'pending';

                            $statusMap = [
                                'pending' => [
                                    'app-badge-warning',
                                    'Menunggu persetujuan'
                                ],

                                'disetujui' => [
                                    'app-badge-success',
                                    'Disetujui'
                                ],

                                'menunggu_pengembalian' => [
                                    'app-badge-warning',
                                    'Menunggu pengembalian'
                                ],

                                'kembali' => [
                                    'app-badge-neutral',
                                    'Selesai'
                                ],

                                'ditolak' => [
                                    'app-badge-danger',
                                    'Ditolak'
                                ]
                            ];

                            [$badgeClass, $label] = $statusMap[$status]
                                ?? ['app-badge-neutral', ucfirst($status)];
                        ?>

                        <div class="list-group-item p-3">

                            <div
                                class="d-flex justify-content-between align-items-start gap-2 mb-2"
                            >
                                <span class="fw-semibold">
                                    <?= htmlspecialchars(
                                        $row['nama_alat'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>
                                </span>

                                <span class="app-badge <?= $badgeClass; ?>">
                                    <?= htmlspecialchars(
                                        $label,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>
                                </span>
                            </div>

                            <div
                                class="d-flex justify-content-between align-items-center"
                            >
                                <small style="color:#8a8a88;">

                                    <i class="bi bi-calendar-event me-1"></i>

                                    <?= !empty($row['tanggal_pinjam'])
                                        ? date(
                                            'd M Y',
                                            strtotime($row['tanggal_pinjam'])
                                        )
                                        : '-'; ?>

                                </small>

                                <?php if ((int) ($row['denda'] ?? 0) > 0): ?>

                                    <small
                                        class="fw-bold"
                                        style="color:#b71c1c;"
                                    >
                                        Denda: Rp<?= number_format(
                                            (float) $row['denda'],
                                            0,
                                            ',',
                                            '.'
                                        ); ?>
                                    </small>

                                <?php endif; ?>

                            </div>
                        </div>

                    <?php endwhile; ?>

                <?php else: ?>

                    <div class="app-empty">
                        <div class="app-empty-icon">
                            <i class="bi bi-inbox"></i>
                        </div>

                        <div class="fw-semibold">
                            Belum ada aktivitas
                        </div>
                    </div>

                <?php endif; ?>

            </div>

            <div
                class="p-3 text-center border-top"
                style="border-color:#f0f0ee!important;"
            >
                <a
                    href="index.php?page=riwayat-lengkap"
                    class="small fw-semibold text-decoration-none"
                >
                    Lihat Semua Riwayat →
                </a>
            </div>

        </div>
    </div>
</div>

<?php
// Tutup statement agar resource database tetap terkelola.
$activeLoansStmt->close();
$activityStmt->close();

require 'views/layouts/role_footer.php';
?>
