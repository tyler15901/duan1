<?php
// app/Core/BaseModel.php
class BaseModel {
    protected $conn;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct() {
        // Kết nối DB thông qua class Database
        $this->conn = Database::connect();
    }

    // Hàm lấy tất cả dữ liệu
    public function all() {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Hàm tìm 1 dòng
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    

    // Chúng ta sẽ thêm hàm create/update sau
    public function insert($data) {
    $columns = implode(", ", array_keys($data));
    $placeholders = implode(", ", array_map(fn($key) => ":$key", array_keys($data)));

    $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute($data);
}

}