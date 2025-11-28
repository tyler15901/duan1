<?php
class Database {
    public $conn;

    public function __construct() {
        try {
            // Kết nối PDO sử dụng thông tin từ config.php
            $this->conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8", DB_USER, DB_PASS);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Lỗi kết nối DB: " . $e->getMessage());
        }
    }
}
?>