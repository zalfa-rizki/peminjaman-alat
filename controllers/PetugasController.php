<?php
class PetugasController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    private function simpanLog($aksi, $keterangan) {
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
        $stmt = $this->db->prepare("INSERT INTO log_aktivitas (user_id, aksi, keterangan) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $userId, $aksi, $keterangan);
        $stmt->execute();
        $stmt->close();
    }

    private function redirectDashboard($query = '') {
        header('Location: index.php?page=dashboard' . ($query !== '' ? '&' . ltrim($query, '&') : ''));
        exit;
    }

    public function accPeminjaman() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectDashboard();
        }

        $id = filter_input(INPUT_POST, 'id_peminjaman', FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            $this->redirectDashboard('aksi=gagal');
        }

        try {
            $this->db->begin_transaction();
            $stmt = $this->db->prepare("UPDATE peminjaman SET status_pinjam = 'disetujui' WHERE id = ? AND status_pinjam = 'pending'");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $changed = $stmt->affected_rows;
            $stmt->close();

            if ($changed !== 1) {
                throw new RuntimeException('Pengajuan tidak ditemukan atau sudah diproses.');
            }

            $this->simpanLog('Setujui Peminjaman', "Petugas menyetujui peminjaman dengan ID: {$id}");
            $this->db->commit();
            $this->redirectDashboard('status=disetujui');
        } catch (Throwable $e) {
            try { $this->db->rollback(); } catch (Throwable $ignored) {}
            $this->redirectDashboard('aksi=gagal');
        }
    }

    /** Konfirmasi penerimaan barang. Stok hanya ditambah jika status belum kembali. */
    public function prosesKembali() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectDashboard();
        }

        $id = filter_input(INPUT_POST, 'id_peminjaman', FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            $this->redirectDashboard('status=kembali_gagal');
        }

        $today = date('Y-m-d');
        $finePerDay = 20000;

        try {
            $this->db->begin_transaction();

            // Lock peminjaman agar dua klik/permintaan bersamaan tidak menggandakan stok.
            $stmt = $this->db->prepare("SELECT alat_id, tanggal_kembali_seharusnya, status_pinjam FROM peminjaman WHERE id = ? FOR UPDATE");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $loan = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$loan || !in_array($loan['status_pinjam'], ['disetujui', 'menunggu_pengembalian'], true)) {
                throw new RuntimeException('Peminjaman tidak aktif atau sudah dikembalikan.');
            }

            $fine = 0;
            $dueDate = $loan['tanggal_kembali_seharusnya'] ?? null;
            if ($dueDate && $dueDate !== '0000-00-00' && strtotime($today) > strtotime($dueDate)) {
                $daysLate = (int) floor((strtotime($today) - strtotime($dueDate)) / 86400);
                $fine = max(0, $daysLate) * $finePerDay;
            }

            $update = $this->db->prepare("UPDATE peminjaman SET tanggal_kembali_realitas = ?, status_pinjam = 'kembali', denda = ? WHERE id = ? AND status_pinjam IN ('disetujui', 'menunggu_pengembalian')");
            $update->bind_param('sii', $today, $fine, $id);
            $update->execute();
            if ($update->affected_rows !== 1) {
                $update->close();
                throw new RuntimeException('Status peminjaman sudah berubah.');
            }
            $update->close();

            $alatId = (int) $loan['alat_id'];
            $stock = $this->db->prepare("UPDATE alat SET stok = COALESCE(stok, 0) + 1, status = 'tersedia' WHERE id = ?");
            $stock->bind_param('i', $alatId);
            $stock->execute();
            if ($stock->affected_rows !== 1) {
                $stock->close();
                throw new RuntimeException('Data alat tidak ditemukan.');
            }
            $stock->close();

            $this->simpanLog('Proses Pengembalian', "Peminjaman ID: {$id}, alat ID: {$alatId} dikembalikan. Denda: Rp" . number_format($fine, 0, ',', '.'));
            $this->db->commit();
            $this->redirectDashboard('status=selesai&denda=' . $fine);
        } catch (Throwable $e) {
            try { $this->db->rollback(); } catch (Throwable $ignored) {}
            $this->redirectDashboard('status=kembali_gagal');
        }
    }

    public function tolakPeminjaman() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectDashboard();
        }

        $id = filter_input(INPUT_POST, 'id_peminjaman', FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            $this->redirectDashboard('aksi=gagal');
        }

        try {
            $this->db->begin_transaction();
            $stmt = $this->db->prepare("SELECT alat_id FROM peminjaman WHERE id = ? AND status_pinjam = 'pending' FOR UPDATE");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $loan = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if (!$loan) {
                throw new RuntimeException('Pengajuan tidak ditemukan atau sudah diproses.');
            }

            $update = $this->db->prepare("UPDATE peminjaman SET status_pinjam = 'ditolak' WHERE id = ? AND status_pinjam = 'pending'");
            $update->bind_param('i', $id);
            $update->execute();
            if ($update->affected_rows !== 1) {
                $update->close();
                throw new RuntimeException('Pengajuan sudah berubah.');
            }
            $update->close();

            $alatId = (int) $loan['alat_id'];
            $stock = $this->db->prepare("UPDATE alat SET stok = COALESCE(stok, 0) + 1, status = 'tersedia' WHERE id = ?");
            $stock->bind_param('i', $alatId);
            $stock->execute();
            $stock->close();

            $this->simpanLog('Tolak Peminjaman', "Pengajuan ID: {$id} ditolak. Stok alat ID: {$alatId} dikembalikan.");
            $this->db->commit();
            $this->redirectDashboard('status=ditolak');
        } catch (Throwable $e) {
            try { $this->db->rollback(); } catch (Throwable $ignored) {}
            $this->redirectDashboard('aksi=gagal');
        }
    }
}
