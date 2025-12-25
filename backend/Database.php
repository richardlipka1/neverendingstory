<?php
// Database helper class
class Database {
    private $conn;
    private $config;
    
    public function __construct() {
        $configFile = __DIR__ . '/config/database.php';
        if (!file_exists($configFile)) {
            $configFile = __DIR__ . '/config/database.example.php';
        }
        $this->config = require $configFile;
    }
    
    public function connect() {
        if ($this->conn) {
            return $this->conn;
        }
        
        try {
            $dsn = "mysql:host={$this->config['host']};dbname={$this->config['database']};charset={$this->config['charset']}";
            $this->conn = new PDO($dsn, $this->config['username'], $this->config['password']);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $this->conn;
        } catch(PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database connection failed']);
            exit;
        }
    }
}
