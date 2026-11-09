<?php
require_once 'models/AuthModel.php';

class AuthController {
    private $model;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->model = new AuthModel($db);
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

    public function auth() {
        if ($_POST) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $user = $this->model->login($username);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role']    = $user['role'];
                $_SESSION['nama']    = $user['username'];

                // Simpan Log Login Berhasil
                $this->simpanLog("Login", "User '$username' berhasil login ke sistem.");

                header("Location: index.php?page=dashboard");
            } else {
                // Simpan Log Percobaan Login Gagal (Opsional untuk keamanan)
                $this->simpanLog("Login Gagal", "Percobaan login gagal untuk username: $username");
                
                header("Location: index.php?page=login&error=1");
            }
        }
    }

    public function register() {
        if ($_POST) {
            $username = $_POST['username'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role = 'peminjam'; 

            $stmt = $this->db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $password, $role);
            
            if ($stmt->execute()) {
                // Ambil ID user yang baru saja terdaftar
                $new_id = $this->db->insert_id;

                // Simpan Log Registrasi
                $this->simpanLog("Registrasi", "User baru '$username' telah mendaftar sebagai peminjam.", $new_id);

                header("Location: index.php?page=login&success=1");
            } else {
                header("Location: index.php?page=register&error=1");
            }
        }
    }

    public function logout() {
        // Catat log sebelum session dihancurkan
        if (isset($_SESSION['user_id'])) {
            $username = $_SESSION['nama'];
            $this->simpanLog("Logout", "User '$username' telah keluar dari sistem.");
        }

        session_destroy();
        header("Location: index.php?page=login");
    }
}