<?php
require_once 'Database.php';

class Model {
    protected $db;
    protected $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->conn;
    }
}
?>