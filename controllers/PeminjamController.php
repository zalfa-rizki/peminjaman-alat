<?php
require_once 'models/AlatModel.php';

class PeminjamController {
    private $db;
    private $alatModel;

    public function __construct($db) {
        $this->db = $db;
        $this->alatModel = new AlatModel($db);
    }

    private function simpanLog($aksi, $keterangan) {
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
        $stmt = $this->db->prepare("INSERT INTO log_aktivitas (user_id, aksi, keterangan) VALUES (?, ?, ?)");
        $stmt->bind_param('iss', $userId, $aksi, $keterangan);
        $stmt->execute();
        $stmt->close();
    }

    public function index() {
        $result = $this->db->query("SELECT id, nama_alat, stok, status, foto FROM alat ORDER BY nama_alat ASC");
        $daftarAlat = [];
        while ($row = $result->fetch_assoc()) {
            $daftarAlat[] = $row;
        }
        require 'views/peminjam/dashboard.php';
    }

    public function formAjukan() {
        $id = filter_input(INPUT_POST, 'alat_id', FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            header('Location: index.php?page=dashboard');
            exit;
        }

        $alat = $this->alatModel->getAlatById($id);
        if (!$alat) {
            header('Location: index.php?page=dashboard&pesan=alat_tidak_ditemukan');
            exit;
        }
        if ((int) $alat['stok'] < 1) {
            header('Location: index.php?page=dashboard&pesan=gagal_stok');
            exit;
        }
        require 'views/peminjam/ajukan.php';
    }

    public function simpanPengajuan() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=dashboard');
            exit;
        }

        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $alatId = filter_input(INPUT_POST, 'alat_id', FILTER_VALIDATE_INT);
        $tglPinjam = trim((string) ($_POST['tanggal_pinjam'] ?? ''));
        $tglSeharusnya = trim((string) ($_POST['tanggal_kembali_seharusnya'] ?? ''));

        $pinjamDate = DateTime::createFromFormat('!Y-m-d', $tglPinjam);
        $dueDate = DateTime::createFromFormat('!Y-m-d', $tglSeharusnya);
        if ($userId < 1 || !$alatId || $alatId < 1 || !$pinjamDate || !$dueDate || $pinjamDate->format('Y-m-d') !== $tglPinjam || $dueDate->format('Y-m-d') !== $tglSeharusnya || $dueDate < $pinjamDate) {
            header('Location: index.php?page=dashboard&pesan=data_tanggal_tidak_valid');
            exit;
        }

        try {
            $this->db->begin_transaction();
            $cekAlat = $this->db->prepare('SELECT nama_alat, stok FROM alat WHERE id = ? FOR UPDATE');
            $cekAlat->bind_param('i', $alatId);
            $cekAlat->execute();
            $dataAlat = $cekAlat->get_result()->fetch_assoc();
            $cekAlat->close();

            if (!$dataAlat || (int) $dataAlat['stok'] <= 0) {
                throw new RuntimeException('stok_habis');
            }

            $stmt = $this->db->prepare("INSERT INTO peminjaman (user_id, alat_id, tanggal_pinjam, tanggal_kembali_seharusnya, status_pinjam, denda) VALUES (?, ?, ?, ?, 'pending', 0)");
            $stmt->bind_param('iiss', $userId, $alatId, $tglPinjam, $tglSeharusnya);
            $stmt->execute();
            $stmt->close();

            $stock = $this->db->prepare("UPDATE alat SET stok = stok - 1, status = CASE WHEN stok <= 0 THEN 'kosong' ELSE 'tersedia' END WHERE id = ? AND stok > 0");
            $stock->bind_param('i', $alatId);
            $stock->execute();
            if ($stock->affected_rows !== 1) {
                $stock->close();
                throw new RuntimeException('stok_habis');
            }
            $stock->close();

            $namaAlat = $dataAlat['nama_alat'];
            $this->simpanLog('Ajukan Peminjaman', "Peminjam mengajukan alat: {$namaAlat} (ID: {$alatId}) untuk tanggal: {$tglPinjam}");
            $this->db->commit();
            header('Location: index.php?page=dashboard&pesan=berhasil_pinjam');
            exit;
        } catch (Throwable $e) {
            try { $this->db->rollback(); } catch (Throwable $ignored) {}
            $message = $e->getMessage() === 'stok_habis' ? 'gagal_stok' : 'gagal_sistem';
            header('Location: index.php?page=dashboard&pesan=' . $message);
            exit;
        }
    }

    /** Peminjam meminta pengembalian; stok baru bertambah setelah petugas menerima barang. */
    public function ajukanPengembalian() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=dashboard');
            exit;
        }

        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $loanId = filter_input(INPUT_POST, 'id_peminjaman', FILTER_VALIDATE_INT);
        if ($userId < 1 || !$loanId || $loanId < 1) {
            header('Location: index.php?page=dashboard&pesan=pengembalian_gagal');
            exit;
        }

        try {
            $this->db->begin_transaction();
            $stmt = $this->db->prepare("SELECT p.status_pinjam, a.nama_alat FROM peminjaman p JOIN alat a ON a.id = p.alat_id WHERE p.id = ? AND p.user_id = ? FOR UPDATE");
            $stmt->bind_param('ii', $loanId, $userId);
            $stmt->execute();
            $loan = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$loan || $loan['status_pinjam'] !== 'disetujui') {
                throw new RuntimeException('Peminjaman tidak aktif atau bukan milik akun ini.');
            }

            $update = $this->db->prepare("UPDATE peminjaman SET status_pinjam = 'menunggu_pengembalian' WHERE id = ? AND user_id = ? AND status_pinjam = 'disetujui'");
            $update->bind_param('ii', $loanId, $userId);
            $update->execute();
            if ($update->affected_rows !== 1) {
                $update->close();
                throw new RuntimeException('Status peminjaman sudah berubah.');
            }
            $update->close();

            $namaAlat = $loan['nama_alat'];
            $this->simpanLog('Ajukan Pengembalian', "Peminjam mengajukan pengembalian {$namaAlat} pada peminjaman ID: {$loanId}.");
            $this->db->commit();
            header('Location: index.php?page=dashboard&pesan=pengembalian_diajukan');
            exit;
        } catch (Throwable $e) {
            try { $this->db->rollback(); } catch (Throwable $ignored) {}
            header('Location: index.php?page=dashboard&pesan=pengembalian_gagal');
            exit;
        }
    }
}
