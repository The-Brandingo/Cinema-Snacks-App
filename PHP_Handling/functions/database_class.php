<?php

class Database
{
    private static $instance;
    private $connection;
    private $conn_id;

    private function __construct()
    {
        $db_username = "abc";
        $db_password = "password";
        $dbname = "cinema_snacks_app"; // Access the database

        if (!isset($_SESSION['connection_id']) || $_SESSION['connection_id'] !== $this->conn_id) {
            $_SESSION['connection_id'] = uniqid(); // Generate a unique session ID
            $this->set_connection_id();
        }
        // Create a connection
        $this->connection = new mysqli('p:localhost', $db_username, $db_password, $dbname);
        $this->connection->set_charset('utf8mb4');
        // Check the connection
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function set_connection_id()
    {
        $this->conn_id = $_SESSION['connection_id'];
    }

    public function get_connection_id()
    {
        if ($this->conn_id) {
            return $this->conn_id;
        } else {
            echo 'CONNECTION NOT FOUND';
            return null; // or return some default value
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }
}

?>