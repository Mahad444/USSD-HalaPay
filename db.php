<?php

class DB {
    private $host = "localhost";
    private $db_name = "ussd_app";
    private $username = "root"; // Default XAMPP username
    private $password = "";     // Default XAMPP password is empty
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            // Log error or handle gracefully in production
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
