<?php
class Database {
    private static $pdo;
    public static function connect() {
        if (!self::$pdo) {
            $config = require __DIR__ . '/../../configs/database.php';
            self::$pdo = new PDO(
                "mysql:host={$config['host']};dbname={$config['name']}",
                $config['user'], $config['pass'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }
        return self::$pdo;
    }
}