<?php
class Database {
    public $conn;

    public function __construct() {
        try {
            $this->conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8", DB_USER, DB_PASS);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // [THÊM DÒNG NÀY] Để tránh lỗi "Expression #1 of SELECT list is not in GROUP BY clause..."
            $this->conn->exec("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
            
        } catch(PDOException $e) {
            die("Lỗi kết nối DB: " . $e->getMessage());
        }
    }
}
?>