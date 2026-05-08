<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'ten12_portfolio';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            // Try multiple connection methods
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            
            // Try with default socket first
            try {
                $this->conn = new PDO($dsn, $this->username, $this->password);
            } catch(PDOException $e) {
                // If that fails, try with unix_socket
                $dsn_socket = "mysql:unix_socket=/var/run/mysqld/mysqld.sock;dbname=" . $this->db_name . ";charset=utf8mb4";
                $this->conn = new PDO($dsn_socket, $this->username, $this->password);
            }
            
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch(PDOException $exception) {
            // Return null instead of echoing error
            error_log("Database connection error: " . $exception->getMessage());
            return null;
        }

        return $this->conn;
    }
}
?>
