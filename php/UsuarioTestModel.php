<?php

class UsuarioTestModel
{
    private $conn;
    private $usuario_id;  

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function insertarUsuario($profesion, $edad, $genero, $pericia)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO UsuarioTests (profesion, edad, genero, pericia_informatica) VALUES (?, ?, ?, ?)"
        );

        if ($stmt === false) {
            return "Error en la preparación de la consulta: " . $this->conn->error;
        }

        $stmt->bind_param("sisi", $profesion, $edad, $genero, $pericia);

        if ($stmt->execute()) {
            $this->usuario_id = $this->conn->insert_id;

            $stmt->close();

            return true;

        } else {
            $error = $stmt->error;
            $stmt->close();
            return "Error al insertar datos: " . $error;
        }
    }

    public function getUsuarioId()
    {
        return $this->usuario_id;
    }
}
?>
