<?php
class DB
{
    private $host = "localhost";  
    private $user = "DBUSER2025";          
    private $pass = "DBPSWD2025";              
    private $dbname = "UO300652_DB";
    private $conn;

    public function __construct()
    {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);

        if ($this->conn->connect_error) {
            die("Conexión fallida: " . $this->conn->connect_error);
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
?>
