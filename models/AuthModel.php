<?php
class AuthModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function login($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
