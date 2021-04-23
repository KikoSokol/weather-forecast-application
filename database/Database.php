<?php
require_once 'config.php';

class Database
{
    private $conn;

    public function __construct()
    {
        try {
            $this->conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,DB_USER,DB_PASS);
//            $this->conn->exec("set name utf8_general_ci");
        }
        catch (PDOException $exception)
        {
            echo "Database could not be connected: " . $exception->getMessage();
        }
    }

    public function getConn()
    {
        return $this->conn;
    }
}