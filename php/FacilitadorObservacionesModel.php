<?php
class FacilitadorObservacionesModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function insertarObservacion($usuario_id, $observador_comentarios)
    {
        $sql = "INSERT INTO FacilitadorObservaciones (usuario_id, observador_comentarios) 
                VALUES (?, ?)";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return "Error en prepare(): " . $this->conn->error;
        }

        $stmt->bind_param("is", $usuario_id, $observador_comentarios);

        if ($stmt->execute()) {
            $inserted_id = $this->conn->insert_id;
            $stmt->close();
            return $inserted_id;
        } else {
            $error = $stmt->error;
            $stmt->close();
            return "Error al insertar datos: " . $error;
        }
    }

    /**
     * Actualiza una observación existente
     *
     * @param int $observador_id
     * @param string $observador_comentarios
     * @return bool|string True si se actualizó, mensaje de error si falla
     */
    public function actualizarObservacion($observador_id, $observador_comentarios)
    {
        $sql = "UPDATE FacilitadorObservaciones SET observador_comentarios=? WHERE observador_id=?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return "Error en prepare(): " . $this->conn->error;
        }

        $stmt->bind_param("si", $observador_comentarios, $observador_id);

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
