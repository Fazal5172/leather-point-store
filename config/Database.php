<?php
/**
 * Database Connection Class (OOP PDO Pattern)
 * Displays secure connection to the database.
 */
class Database {
    private $host = "localhost";
    private $db_name = "leather_point_db";
    private $username = "root";
    private $password = "";
    public $conn;

    /**
     * Establish PDO connection to MySQL database
     * @return PDO|null
     */
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                $this->username, 
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ]
            );
        } catch(PDOException $exception) {
            // Note: In real production systems, errors are logged silently, 
            // but for a portfolio demo, we capture it to show to recruiters.
            error_log("Connection failed: " . $exception->getMessage());
        }
        return $this->conn;
    }
}
?>