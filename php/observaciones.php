<?php
include "DB.php";
include "FacilitadorObservacionesModel.php";
session_start(); 

$db = new DB();
$conn = $db->getConnection();

$errorFormulario = false;
$errorObservaciones = "";
$observaciones = "";

// Recibir el usuario_id como GET o POST
$usuario_id = $_SESSION['usuario_id'];

if (!$usuario_id) {
    die("ID de usuario no especificado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $observaciones = trim($_POST["observaciones"] ?? '');
    if ($observaciones === "") {
        $errorObservaciones = " * Por favor, escribe tus observaciones";
        $errorFormulario = true;
    }

    if (!$errorFormulario) {
        // Guardar en la base de datos usando FacilitadorObservacionesModel
        $obsModel = new FacilitadorObservacionesModel($conn);
        $insert_id = $obsModel->insertarObservacion($usuario_id, $observaciones);

        if (is_numeric($insert_id)) {
            // Si se insertó correctamente, cerrar la ventana
            header("Location: formulario.php");
            exit;
        } else {
            $mensajeBD = $insert_id; // mensaje de error devuelto por el modelo
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Observaciones del Facilitador - MotoGP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />
</head>
<body>
<header>
    <h1>Observaciones del Facilitador para el Usuario <?= htmlspecialchars($usuario_id) ?></h1>
</header>
<main>

<form action="#" method="post">
    <h3>Escribe tus observaciones:</h3>
    <p>
        <textarea name="observaciones" rows="6" cols="60"><?= htmlspecialchars($observaciones) ?></textarea>
        <span style="color:red;"><?= $errorObservaciones ?></span>
    </p>

    <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario_id) ?>" />
    <p>
        <input type="submit" value="Guardar y cerrar"/>
    </p>
</form>

<?php
if (!empty($mensajeBD)) {
    echo "<p><strong>$mensajeBD</strong></p>";
}
?>

</main>
</body>
</html>
