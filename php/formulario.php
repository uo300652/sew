<?php
session_start(); 
include "DB.php";
include "UsuarioTestModel.php"; 
include "TestResultadosModel.php";
include "CronometroClase.php";

$db = new DB();
$conn = $db->getConnection();

$errorFormulario = false;

$errorProfesion = "";
$errorEdad      = "";
$errorGenero    = "";
$errorPericia   = "";
$errorDevice    = "";

$formularioPOST  = [];

if (count($_POST) > 0) {
    $formularioPOST = $_POST;

    // Validaciones
    if (!isset($_POST["profesion"]) || trim($_POST["profesion"]) == "") {
        $errorProfesion = " * La profesión es obligatoria";
        $errorFormulario = true;
    }

    if (!isset($_POST["edad"]) || !is_numeric($_POST["edad"]) || $_POST["edad"] < 0) {
        $errorEdad = " * Edad no válida";
        $errorFormulario = true;
    }

    if (!isset($_POST["genero"])) {
        $errorGenero = " * El género es obligatorio";
        $errorFormulario = true;
    }

    if (!isset($_POST["pericia_informatica"]) || !is_numeric($_POST["pericia_informatica"]) ||
        $_POST["pericia_informatica"] < 0 || $_POST["pericia_informatica"] > 10) {
        $errorPericia = " * Valor entre 0 y 10";
        $errorFormulario = true;
    }

    // Validación del dispositivo
    $validDevices = ["ordenador", "tablet", "telefono"];
    if (!isset($_POST["device"]) || !in_array($_POST["device"], $validDevices)) {
        $errorDevice = " * Selecciona un dispositivo válido";
        $errorFormulario = true;
    }

    if (!$errorFormulario) {

        $usuarioModel = new UsuarioTestModel($conn);
        $resultado = $usuarioModel->insertarUsuario(
            $_POST['profesion'],
            (int)$_POST['edad'],
            $_POST['genero'],
            (int)$_POST['pericia_informatica']
        );

        if ($resultado === true) {
            $mensajeBD = "Datos insertados correctamente en la base de datos.";

            $usuario_id = $usuarioModel->getUsuarioId();
            $cronometro = new Cronometro();
            $cronometro->arrancar();

            $_SESSION["cronometro"] = $cronometro;

            // Tomar dispositivo seleccionado
            $device = $_POST["device"];

            $testModel = new TestResultadosModel($conn);

            // Insertar test inicial como incompleto
            $test_id = $testModel->insertarResultado($usuario_id, $device);

            $_SESSION["test_id"] = $test_id;
            $_SESSION["usuario_id"] = $usuario_id;

            header("Location: preguntas.php");
            exit;
        }
        else {
            $mensajeBD = $resultado; 
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Formulario de usuario - MotoGP</title>
    <meta name="author" content="Matias Valle Trapiella" />
    <meta name="description" content="Formulario para recoger datos de usuarios para el sistema MotoGP." />
    <meta name="keywords" content="MotoGP, formulario usuario, profesión, edad, género, pericia informática" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <link rel="icon" type="image/x-icon" href="../multimedia/favicon-MotoGp.ico" />
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />
</head>
<body>

<header>
    <h1>Formulario de datos de usuario</h1>
</header>

<main>

<form action="#" method="post">
    <p>Profesión:</p>
    <p>
        <textarea name="profesion" rows="3" cols="30"><?php echo isset($_POST["profesion"]) ? $_POST["profesion"] : ""; ?></textarea>
        <span><?php echo $errorProfesion; ?></span>
    </p>

    <p>Edad:</p>
    <p>
        <input type="number" name="edad" min="0" value="<?php echo isset($_POST["edad"]) ? $_POST["edad"] : ""; ?>"/>
        <span><?php echo $errorEdad; ?></span>
    </p>

    <p>Género:</p>
    <p>
        <input type="radio" name="genero" value="Hombre" <?php if(isset($_POST["genero"]) && $_POST["genero"]=="Hombre") echo "checked"; ?>>Hombre
        <input type="radio" name="genero" value="Mujer"  <?php if(isset($_POST["genero"]) && $_POST["genero"]=="Mujer")  echo "checked"; ?>>Mujer
        <input type="radio" name="genero" value="Otro"   <?php if(isset($_POST["genero"]) && $_POST["genero"]=="Otro")   echo "checked"; ?>>Otro
        <span><?php echo $errorGenero; ?></span>
    </p>

    <p>Pericia informática (0-10):</p>
    <p>
        <input type="number" name="pericia_informatica" min="0" max="10"
            value="<?php echo isset($_POST["pericia_informatica"]) ? $_POST["pericia_informatica"] : ""; ?>"/>
        <span><?php echo $errorPericia; ?></span>
    </p>

    <p>Dispositivo utilizado:</p>
    <p>
        <select name="device">
            <option value="">-- Selecciona --</option>
            <option value="ordenador" <?php if(isset($_POST["device"]) && $_POST["device"]=="ordenador") echo "selected"; ?>>Ordenador</option>
            <option value="tablet"     <?php if(isset($_POST["device"]) && $_POST["device"]=="tablet") echo "selected"; ?>>Tablet</option>
            <option value="telefono"   <?php if(isset($_POST["device"]) && $_POST["device"]=="telefono") echo "selected"; ?>>Teléfono</option>
        </select>
        <span><?php echo $errorDevice; ?></span>
    </p>

    <p>
        <input type="submit" value="Iniciar prueba"/>
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
