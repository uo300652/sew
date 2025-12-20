<?php
include "DB.php";
include "TestResultadosModel.php";
include "CronometroClase.php";
session_start(); 

$db = new DB();
$conn = $db->getConnection();

$errorFormulario = false;

$errorComentarios = "";
$errorSugerencias = "";
$errorPuntuacion = "";

$formularioPOST  = [];

$comentarios = "";
$sugerencias = "";
$puntuacion = "";

if (count($_POST) > 0) {
    $formularioPOST = $_POST;

    // Validaciones
    $comentarios = trim($_POST["comentarios"] ?? '');
    if ($comentarios === "") {
        $errorComentarios = " * Por favor, escribe tus comentarios";
        $errorFormulario = true;
    }

    $sugerencias = trim($_POST["sugerencias"] ?? '');
    if ($sugerencias === "") {
        $errorSugerencias = " * Por favor, escribe tus sugerencias";
        $errorFormulario = true;
    }

    $puntuacion = $_POST["puntuacion"] ?? '';
    if (!is_numeric($puntuacion) || $puntuacion < 0 || $puntuacion > 10) {
        $errorPuntuacion = " * La puntuación debe estar entre 0 y 10";
        $errorFormulario = true;
    }

    if (!$errorFormulario) {
        // Guardar en la base de datos usando TestResultadosModel
        $testModel = new TestResultadosModel($conn);
        $test_id = $_SESSION["test_id"];
        $testModel->actualizarComentarios(
                $test_id,
                $comentarios,
                $sugerencias,
                $puntuacion
        );

        header("Location: observaciones.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Comentarios y sugerencias - MotoGP</title>
    <meta name="author" content="Matias Valle Trapiella" />
    <meta name="description" content="Formulario para que los usuarios envíen comentarios, sugerencias y puntuaciones sobre la experiencia en MotoGP Desktop." />
    <meta name="keywords" content="MotoGP, comentarios MotoGP, sugerencias MotoGP, 
    formulario de feedback, experiencia de usuario, evaluación de usuario, puntuación,
    opiniones, interacción web" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/x-icon" href="../multimedia/favicon-MotoGp.ico" />
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />
</head>
<body>
<header>
    <h1>Comentarios y sugerencias</h1>
</header>
<main>
<form action="#" method="post">

    <!-- Comentarios -->
    <p>
        <label for="comentarios">Comentarios:</label><br>
        <textarea id="comentarios" name="comentarios" rows="4" cols="50"><?= $comentarios ?></textarea>
        <span><?= $errorComentarios ?></span>
    </p>

    <!-- Sugerencias -->
    <p>
        <label for="sugerencias">Sugerencias:</label><br>
        <textarea id="sugerencias" name="sugerencias" rows="4" cols="50"><?= $sugerencias ?></textarea>
        <span><?= $errorSugerencias ?></span>
    </p>

    <!-- Puntuación -->
    <p>
        <label for="puntuacion">Puntuación (0-10):</label><br>
        <input id="puntuacion" type="number" name="puntuacion" min="0" max="10" value="<?= $puntuacion ?>"/>
        <span><?= $errorPuntuacion ?></span>
    </p>

    <!-- Botón de envío -->
    <p>
        <input type="submit" value="Enviar"/>
    </p>

</form>

<?php
if ($formularioPOST) {
    if ($errorFormulario) {
        echo "<h4>Formulario NO procesado debido a errores</h4>";
    } else {
        echo "<h4>Formulario válido.</h4>";
        if (isset($mensajeBD)) {
            echo "<p>$mensajeBD</p>";
        }
    }
}
?>
</main>
</body>
</html>
