<?php

class DBConn {
    protected $conn;
    private $isClosed = false;

    public function __construct($dbConnect) {
        try {
            $dsn = "mysql:host={$dbConnect['host']};dbname={$dbConnect['dbname']};charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->conn = new PDO(
                $dsn, 
                $dbConnect['username'], 
                $dbConnect['password'], 
                $options
            );
        } catch (PDOException $e) {
            throw new Exception("Connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function close() {
        if (!$this->isClosed) {
            $this->conn = null;
            $this->isClosed = true;
        }
    }

    public function __destruct() {
        $this->close();
    }
}

?>