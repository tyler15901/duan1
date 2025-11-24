<?php
// app/Core/Database.php
class Database {
    private static $pdo;

    public static function connect() {
        if (!self::$pdo) {
            // Gọi cấu hình từ file config
            // Lưu ý: Vì Database.php được require từ index.php, 
            // nên các hằng số DB_HOST... đã có sẵn.
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                self::$pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                die("Lỗi kết nối Database: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}