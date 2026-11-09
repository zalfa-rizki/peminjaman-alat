<?php
class AlatModel {
    private $db;

    public function __construct($db) {
    $this->db = $db;
    // Baris di bawah ini WAJIB ada agar $this->alatModel bisa digunakan
   
}
public function getAlatById($id) {
    // Gunakan prepared statement agar aman
    $stmt = $this->db->prepare("SELECT * FROM alat WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // AMBIL DATANYA sebagai array asosiatif
    return $result->fetch_assoc(); 
}

    public function getAllAlat() {
        return $this->db->query("SELECT * FROM alat")->fetch_all(MYSQLI_ASSOC);
    }

    public function tambahAlat($nama, $stok) {
        $stmt = $this->db->prepare("INSERT INTO alat (nama_alat, stok) VALUES (?, ?)");
        $stmt->bind_param("si", $nama, $stok);
        return $stmt->execute();
    }
}
?>
