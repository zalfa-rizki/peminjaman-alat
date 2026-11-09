<?php
class AdminController {
    private $db;
    private $model;

    public function __construct($db) {
        $this->db = $db;
        $this->model = new AlatModel($db);
      
    }

    public function dashboard() {
        // Cek Session
        if ($_SESSION['role'] !== 'admin') { header("Location: index.php"); exit; }

        $total_user = mysqli_num_rows(mysqli_query($this->db, "SELECT id FROM users"));
        $total_alat = mysqli_num_rows(mysqli_query($this->db, "SELECT id FROM alat"));
        $logs = mysqli_query($this->conn, "SELECT * FROM logs ORDER BY waktu DESC LIMIT 10");

        include 'views/admin/dashboard.php';
    }

   
    public function kelolaAlat() {
        $data_alat = $this->model->getAllAlat();
        include 'views/alat/index.php';
    }

    public function simpanUser() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi password
        $role = $_POST['role'];

        // Query untuk memasukkan data baru
        $stmt = $this->db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $role);

        if ($stmt->execute()) {
            // Catat ke Log Aktivitas
            $this->simpanLog("Tambah User", "Admin menambah user baru: $username dengan role: $role");
            
            header("Location: index.php?page=kelola-user&msg=tambah_sukses");
            exit();
        } else {
            die("Gagal menambah user: " . $this->db->error);
        }
    }
}

   
    

   

    public function updateUser() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id']);
        $username = $_POST['username'];
        $role = $_POST['role'];

        // Cek apakah password diisi (ingin ganti password)
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?");
            $stmt->bind_param("sssi", $username, $password, $role, $id);
        } else {
            // Jika password kosong, jangan update kolom password
            $stmt = $this->db->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
            $stmt->bind_param("ssi", $username, $role, $id);
        }

        if ($stmt->execute()) {
            // Catat ke Log Aktivitas
            $this->simpanLog("Update User", "Admin mengubah data user: $username (ID: $id)");
            header("Location: index.php?page=kelola-user&msg=update_sukses");
            exit();
        } else {
            die("Gagal update: " . $this->db->error);
        }
    }
}

 
public function hapusUser() {
    // 1. Pastikan ID ada dan berupa angka untuk keamanan (SQL Injection)
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id > 0) {
        // 2. Gunakan query yang lebih aman
        $sql = "DELETE FROM users WHERE id = $id";
        $query = $this->db->query($sql);

        if ($query) {
            // 3. Catat ke Log Aktivitas (Opsional jika fungsi simpanLog sudah dibuat)
            if (method_exists($this, 'simpanLog')) {
                $this->simpanLog("Hapus User", "Admin menghapus akun user dengan ID: $id");
            }
            
            // 4. Redirect kembali ke halaman kelola user dengan pesan sukses
            header("Location: index.php?page=kelola-user&msg=hapus_berhasil");
            exit();
        } else {
            // Jika query gagal (misal karena ada relasi foreign key di tabel lain)
            die("Gagal menghapus data: " . $this->db->error);
        }
    } else {
        die("ID User tidak valid atau tidak ditemukan.");
    }

}

    public function indexUser() {
    $query = "SELECT * FROM users WHERE role != 'admin' ORDER BY id DESC";
    // PERBAIKAN: Ganti $this->conn menjadi $this->db
    $users = $this->db->query($query); 
    require 'views/admin/kelola_user.php';
}
// --- MANAJEMEN ALAT ---

public function indexAlat() {
    $alat = $this->db->query("SELECT * FROM alat ORDER BY id DESC");
    require 'views/admin/kelola_alat.php';
}

public function simpanAlat() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nama_alat = $_POST['nama_alat'];
        $stok = intval($_POST['stok']);
        $status = ($stok > 0) ? 'tersedia' : 'kosong';

        // Logika Upload Foto
        $foto = 'default.jpg'; // Nama default jika tidak upload
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $target_dir = "assets/img/alat/"; // Pastikan folder ini sudah kamu buat!
            $foto = time() . "_" . $_FILES['foto']['name'];
            move_uploaded_file($_FILES['foto']['tmp_name'], $target_dir . $foto);
        }

        // Query (Tanpa kategori)
        $stmt = $this->db->prepare("INSERT INTO alat (nama_alat, foto, stok, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $nama_alat, $foto, $stok, $status);
        
        if ($stmt->execute()) {
            header("Location: index.php?page=kelola-alat");
        }
    }
}

public function updateAlat() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id']);
        $nama_alat = $_POST['nama_alat'];
        $stok = intval($_POST['stok']);
        $status = ($stok > 0) ? 'tersedia' : 'kosong';

        // Ambil nama foto lama dari database (jaga-jaga jika tidak ganti foto)
        $result = $this->db->query("SELECT foto FROM alat WHERE id = $id");
        $old_data = $result->fetch_assoc();
        $foto = $old_data['foto'];

        // Cek apakah user mengunggah foto baru
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $target_dir = "assets/img/alat/";
            $foto_baru = time() . "_" . $_FILES['foto']['name'];
            
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_dir . $foto_baru)) {
                // Hapus foto lama jika bukan default.jpg agar penyimpanan tidak penuh
                if ($foto != 'default.jpg' && file_exists($target_dir . $foto)) {
                    unlink($target_dir . $foto);
                }
                $foto = $foto_baru;
            }
        }

        $stmt = $this->db->prepare("UPDATE alat SET nama_alat=?, foto=?, stok=?, status=? WHERE id=?");
        $stmt->bind_param("ssisi", $nama_alat, $foto, $stok, $status, $id);
        
        if ($stmt->execute()) {
            header("Location: index.php?page=kelola-alat&status=updated");
        }
    }
}
public function hapusAlat() {
    $id = $_GET['id'];
    $this->db->query("DELETE FROM alat WHERE id = $id");
    header("Location: index.php?page=kelola-alat");
}

private function simpanLog($aksi, $keterangan) {
    // Ambil user_id dari session, jika tidak ada (belum login/gagal login) set jadi null
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    // Gunakan query yang mengizinkan NULL pada user_id
    $stmt = $this->db->prepare("INSERT INTO log_aktivitas (user_id, aksi, keterangan) VALUES (?, ?, ?)");
    
    // "i" untuk integer, "s" untuk string. Jika null, kirimkan null.
    $stmt->bind_param("iss", $user_id, $aksi, $keterangan);
    $stmt->execute();
}

// Fungsi untuk menampilkan halaman Log
public function indexLog() {
    $query = "SELECT l.*, u.username FROM log_aktivitas l 
              LEFT JOIN users u ON l.user_id = u.id 
              ORDER BY l.tanggal DESC";
    $logs = $this->db->query($query);
    require 'views/admin/log_aktivitas.php';
}

    
   
}
?>