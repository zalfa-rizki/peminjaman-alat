<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once 'config/database.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/PeminjamController.php';
require_once 'controllers/PetugasController.php';
require_once 'controllers/AdminController.php';

$database = new Database();
$db = $database->getConnection();
$page = isset($_GET['page']) ? (string) $_GET['page'] : 'login';
$role = $_SESSION['role'] ?? null;

$requireLogin = static function () use ($role) {
    if (!$role) {
        header('Location: index.php?page=login');
        exit;
    }
};
$requireRole = static function (array $allowed) use ($role, $requireLogin) {
    $requireLogin();
    if (!in_array($role, $allowed, true)) {
        http_response_code(403);
        echo '<!doctype html><html lang="id"><meta charset="utf-8"><title>Akses Ditolak</title><body style="font-family:Arial,sans-serif;padding:32px"><h1>403 - Akses Ditolak</h1><p>Anda tidak memiliki izin untuk membuka halaman ini.</p><a href="index.php?page=dashboard">Kembali ke dashboard</a></body></html>';
        exit;
    }
};

switch ($page) {
    // Autentikasi
    case 'login':
        if ($role) {
            header('Location: index.php?page=dashboard');
            exit;
        }
        include 'views/auth/login.php';
        break;

    case 'proses-login':
        (new AuthController($db))->auth();
        break;

    case 'proses-register':
        (new AuthController($db))->register();
        break;

    case 'logout':
        (new AuthController($db))->logout();
        break;

    // Dashboard sesuai role
    case 'dashboard':
        $requireLogin();
        if ($role === 'admin') {
            include 'views/admin/dashboard.php';
        } elseif ($role === 'petugas') {
            include 'views/petugas/dashboard.php';
        } elseif ($role === 'peminjam') {
            (new PeminjamController($db))->index();
        } else {
            header('Location: index.php?page=logout');
            exit;
        }
        break;

    // Menu petugas (tautan lama tetap berfungsi)
    case 'persetujuan':
        $requireRole(['petugas', 'admin']);
        header('Location: index.php?page=dashboard#tabel-pending');
        exit;

    case 'pantau-pengembalian':
        $requireRole(['petugas', 'admin']);
        header('Location: index.php?page=dashboard#tabel-pengembalian');
        exit;

    case 'acc-peminjaman':
        $requireRole(['petugas', 'admin']);
        (new PetugasController($db))->accPeminjaman();
        break;

    case 'tolak-peminjaman':
        $requireRole(['petugas', 'admin']);
        (new PetugasController($db))->tolakPeminjaman();
        break;

    case 'proses-kembali':
        $requireRole(['petugas', 'admin']);
        (new PetugasController($db))->prosesKembali();
        break;

    // Menu peminjam: pengajuan dan permintaan pengembalian
    case 'ajukan-pinjam':
        $requireRole(['peminjam']);
        (new PeminjamController($db))->formAjukan();
        break;

    case 'proses-ajukan':
        $requireRole(['peminjam']);
        (new PeminjamController($db))->simpanPengajuan();
        break;

    case 'ajukan-pengembalian':
        $requireRole(['peminjam']);
        (new PeminjamController($db))->ajukanPengembalian();
        break;

    // Menu admin. Alias crud-* lama diarahkan ke halaman pengelolaan yang sebenarnya.
    case 'crud-user':
    case 'kelola-user':
        $requireRole(['admin']);
        (new AdminController($db))->indexUser();
        break;

    case 'simpan-user':
        $requireRole(['admin']);
        (new AdminController($db))->simpanUser();
        break;

    case 'update-user':
        $requireRole(['admin']);
        (new AdminController($db))->updateUser();
        break;

    case 'hapus-user':
        $requireRole(['admin']);
        (new AdminController($db))->hapusUser();
        break;

    case 'crud-alat':
    case 'kelola-alat':
        $requireRole(['admin']);
        (new AdminController($db))->indexAlat();
        break;

    case 'simpan-alat':
        $requireRole(['admin']);
        (new AdminController($db))->simpanAlat();
        break;

    case 'update-alat':
        $requireRole(['admin']);
        (new AdminController($db))->updateAlat();
        break;

    case 'hapus-alat':
        $requireRole(['admin']);
        (new AdminController($db))->hapusAlat();
        break;

    case 'log-aktivitas':
        $requireRole(['admin']);
        (new AdminController($db))->indexLog();
        break;

    case 'riwayat-lengkap':
        $requireLogin();
        include 'views/riwayat.php';
        break;

    default:
        http_response_code(404);
        echo '<!doctype html><html lang="id"><meta charset="utf-8"><title>Halaman Tidak Ditemukan</title><body style="font-family:Arial,sans-serif;padding:32px"><h1>404 - Halaman Tidak Ditemukan</h1><a href="index.php?page=dashboard">Kembali ke dashboard</a></body></html>';
        break;
}
