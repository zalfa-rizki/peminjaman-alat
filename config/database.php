<?php
class Database {
    private $host = "127.0.0.1";
    private $user = "root";
    private $pass = "root"; // di aWeb biasanya kosong, bukan "root"
    private $db   = "peminjaman";
    public $conn;

    public function getConnection(){
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db);
            $this->conn->set_charset("utf8mb4");
        } catch(mysqli_sql_exception $e) {
            die("Koneksi Error: " . $e->getMessage() . " | Cek apakah MySQL di aWeb udah ON dan DB db_peminjaman udah dibuat");
        }
        return $this->conn;
    }
}
?>