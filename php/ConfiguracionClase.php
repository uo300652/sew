<?php
include "DB.php"; 

class Configuracion
{
    private $conn;
    private $dbname = "UO300652_DB";

    public function __construct()
    {
        $db = new DB();          
        $this->conn = $db->getConnection(); 

        if ($this->conn->connect_error) {
            die("Conexión fallida: " . $this->conn->connect_error);
        }
    }

    public function reiniciar()
    {
        $tables = ["FacilitadorObservaciones", "TestResultados", "UsuarioTests"];
        $this->conn->select_db($this->dbname);

        // Desactivar temporalmente las claves foráneas
        $this->conn->query("SET FOREIGN_KEY_CHECKS = 0");

        foreach ($tables as $table) {
            // Borrar todos los registros
            $sql = "DELETE FROM $table";
            if (!$this->conn->query($sql)) {
                echo "Error al reiniciar $table: " . $this->conn->error . "<br>";
                continue;
            }

            // Reiniciar el AUTO_INCREMENT
            $sql = "ALTER TABLE $table AUTO_INCREMENT = 1";
            if (!$this->conn->query($sql)) {
                echo "Error al reiniciar AUTO_INCREMENT de $table: " . $this->conn->error . "<br>";
            }
        }

        // Volver a activar las claves foráneas
        $this->conn->query("SET FOREIGN_KEY_CHECKS = 1");

        echo "Base de datos reiniciada correctamente.<br>";
    }


    public function reiniciar_DB()
    {
        $this->borrar_DB();
        $this->crear_DB();
        echo "Base de datos reiniciada completamente.<br>";
    }

    public function borrar_DB()
    {
        $sql = "DROP DATABASE IF EXISTS $this->dbname";
        if ($this->conn->query($sql)) {
            echo "Base de datos $this->dbname eliminada.<br>";
        } else {
            echo "Error al eliminar DB: " . $this->conn->error . "<br>";
        }
    }

    public function crear_DB()
    {
        $sql = "CREATE DATABASE IF NOT EXISTS $this->dbname";
        if ($this->conn->query($sql)) {
            $this->conn->select_db($this->dbname);

            $this->conn->query("
                CREATE TABLE IF NOT EXISTS UsuarioTests (
                    usuario_id INT AUTO_INCREMENT PRIMARY KEY,
                    profesion TEXT NOT NULL,
                    edad INT NOT NULL,
                    genero ENUM('Hombre','Mujer','Otro') NOT NULL,
                    pericia_informatica INT CHECK (pericia_informatica BETWEEN 0 AND 10)
                )
            ");

            $this->conn->query("
                CREATE TABLE IF NOT EXISTS TestResultados (
                    test_id INT AUTO_INCREMENT PRIMARY KEY,
                    usuario_id INT NOT NULL,
                    dispositivo_electronico ENUM('ordenador','tableta','telefono') NOT NULL,
                    tiempo_segundos INT UNSIGNED NOT NULL,
                    completado INT CHECK (completado = 0 OR completado = 1),
                    usuario_comentarios TEXT,
                    usuario_sugerencias TEXT,
                    puntuacion INT CHECK (puntuacion BETWEEN 0 AND 10),
                    FOREIGN KEY (usuario_id) REFERENCES UsuarioTests(usuario_id) ON DELETE CASCADE
                )
            ");

            $this->conn->query("
                CREATE TABLE IF NOT EXISTS FacilitadorObservaciones (
                    observador_id INT AUTO_INCREMENT PRIMARY KEY,
                    usuario_id INT NOT NULL,
                    observador_comentarios TEXT NOT NULL,
                    FOREIGN KEY (usuario_id) REFERENCES UsuarioTests(usuario_id) ON DELETE CASCADE ON UPDATE CASCADE
                )
            ");

            echo "Base de datos y tablas creadas correctamente.<br>";
        } else {
            echo "Error al crear DB: " . $this->conn->error . "<br>";
        }
    }

    public function exportar_CSV($table = "UsuarioTests", $file = null)
    {
        $this->conn->select_db($this->dbname);
        if ($file === null) {
            $file = $table . ".csv";
        }

        $result = $this->conn->query("SELECT * FROM $table");
        if (!$result) {
            die("Error al consultar $table: " . $this->conn->error);
        }

        $fp = fopen($file, 'w');

        $fields = $result->fetch_fields();
        $headers = [];
        foreach ($fields as $field) {
            $headers[] = $field->name;
        }
        fputcsv($fp, $headers);

        while ($row = $result->fetch_assoc()) {
            fputcsv($fp, $row);
        }

        fclose($fp);
        echo "Datos exportados a $file correctamente.<br>";
    }
}
?>
