<?php
class TestResultadosModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Inserta un resultado del test
     */
    public function insertarResultado($usuario_id, $device, $time_seconds = 0, $completed = 0, $user_comments = '', $user_suggestions = '', $rating = 0)
    {
        $sql = "INSERT INTO TestResultados 
                (usuario_id, dispositivo_electronico, tiempo_segundos, completado, usuario_comentarios, usuario_sugerencias, puntuacion)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) return "Error en prepare(): " . $this->conn->error;

        $stmt->bind_param(
            "isiissi",
            $usuario_id,
            $device,
            $time_seconds,
            $completed,
            $user_comments,
            $user_suggestions,
            $rating
        );

        if ($stmt->execute()) {
            $inserted_id = $this->conn->insert_id; // Guardar ID
            $stmt->close();
            return $inserted_id; // Devolver ID del test creado
        } else {
            $error = $stmt->error;
            $stmt->close();
            return "Error al insertar datos: " . $error;
        }
    }

    public function actualizarComentarios($test_id, $usuario_comentarios, $usuarioSugerencias, $puntuacion)
    {
        $sql = "UPDATE TestResultados SET usuario_comentarios=?, usuario_sugerencias=?, puntuacion=? WHERE test_id=?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) return "Error en prepare(): " . $this->conn->error;

        $stmt->bind_param("ssii", $usuario_comentarios, $usuarioSugerencias, $puntuacion, $test_id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $error = $stmt->error;
            $stmt->close();
            return "Error al actualizar datos: " . $error;
        }
    }

    /**
     * Actualiza un test existente (tiempo y estado completed)
     */
    public function actualizarResultado($test_id, $time_seconds, $completed)
    {
        $sql = "UPDATE TestResultados SET tiempo_segundos=?, completado=? WHERE test_id=?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) return "Error en prepare(): " . $this->conn->error;

        $stmt->bind_param("iii", $time_seconds, $completed, $test_id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $error = $stmt->error;
            $stmt->close();
            return "Error al actualizar datos: " . $error;
        }
    }
}
?>