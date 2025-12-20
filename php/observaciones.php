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
    <meta name="author" content="Matias Valle Trapiella" />
    <meta name="description" content="Formulario para recoger observaciones del facilitador de como el usuario realizó los tests." />
    <meta name="keywords" content="usabilidad web, test de usabilidad, evaluación de usuarios, 
    observaciones del facilitador, formulario de evaluación, experiencia de usuario" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/x-icon" href="../multimedia/favicon-MotoGp.ico" />
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />
</head>
<body>
<header>
    <h1>Observaciones del Facilitador para el Usuario <?= $usuario_id ?></h1>
</header>
<main>

<form action="#" method="post">
    <p>
        <label for="observaciones">Observaciones:</label><br>
        <textarea id="observaciones" name="observaciones" rows="6" cols="60"><?= $observaciones ?></textarea>
        <span style="color:red;"><?= $errorObservaciones ?></span>
    </p>

    <!-- Usuario ID oculto (no requiere label visible) -->
    <input type="hidden" name="usuario_id" value="<?= $usuario_id ?>" />

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
