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

$usuarioModel = new UsuarioTestModel($conn);
$testModel    = new TestResultadosModel($conn);

// --- PROCESO DE FORMULARIO NORMAL ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["form_tipo"]) && $_POST["form_tipo"] === "nuevo_usuario") {
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

    $validDevices = ["ordenador", "tablet", "telefono"];
    if (!isset($_POST["dispositivo_electronico"]) || !in_array($_POST["dispositivo_electronico"], $validDevices)) {
        $errorDevice = " * Selecciona un dispositivo válido";
        $errorFormulario = true;
    }

    if (!$errorFormulario) {
        $resultado = $usuarioModel->insertarUsuario(
            $_POST['profesion'],
            (int)$_POST['edad'],
            $_POST['genero'],
            (int)$_POST['pericia_informatica']
        );

        if ($resultado === true) {
            $usuario_id = $usuarioModel->getUsuarioId();
            $device     = $_POST["dispositivo_electronico"];

            $test_id = $testModel->insertarResultado($usuario_id, $device);

            $cronometro = new Cronometro();
            $cronometro->arrancar();
            $_SESSION["cronometro"]  = $cronometro;
            $_SESSION["test_id"]     = $test_id;
            $_SESSION["usuario_id"]  = $usuario_id;

            header("Location: preguntas.php");
            exit;
        } else {
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
    <input type="hidden" name="form_tipo" value="nuevo_usuario">

    <!-- PROFESIÓN -->
    <p>
        <label for="profesion">Profesión:</label>
    </p>
    <p>
        <textarea id="profesion" name="profesion" rows="3" cols="30"><?php
            echo isset($_POST["profesion"]) ? $_POST["profesion"] : "";
        ?></textarea>
        <span><?php echo $errorProfesion; ?></span>
    </p>

    <!-- EDAD -->
    <p>
        <label for="edad">Edad:</label>
    </p>
    <p>
        <input id="edad" type="number" name="edad" min="0"
               value="<?php echo isset($_POST["edad"]) ? $_POST["edad"] : ""; ?>" />
        <span><?php echo $errorEdad; ?></span>
    </p>

    <!-- GÉNERO -->
    <fieldset>
        <legend>Género</legend>

        <p>
            <input id="genero_hombre" type="radio" name="genero" value="Hombre"
                <?php if(isset($_POST["genero"]) && $_POST["genero"]=="Hombre") echo "checked"; ?> />
            <label for="genero_hombre">Hombre</label>
        </p>

        <p>
            <input id="genero_mujer" type="radio" name="genero" value="Mujer"
                <?php if(isset($_POST["genero"]) && $_POST["genero"]=="Mujer") echo "checked"; ?> />
            <label for="genero_mujer">Mujer</label>
        </p>

        <p>
            <input id="genero_otro" type="radio" name="genero" value="Otro"
                <?php if(isset($_POST["genero"]) && $_POST["genero"]=="Otro") echo "checked"; ?> />
            <label for="genero_otro">Otro</label>
        </p>

        <span><?php echo $errorGenero; ?></span>
    </fieldset>

    <!-- PERICIA INFORMÁTICA -->
    <p>
        <label for="pericia_informatica">Pericia informática (0–10):</label>
    </p>
    <p>
        <input id="pericia_informatica" type="number" name="pericia_informatica"
               min="0" max="10"
               value="<?php echo isset($_POST["pericia_informatica"]) ? $_POST["pericia_informatica"] : ""; ?>" />
        <span><?php echo $errorPericia; ?></span>
    </p>

    <!-- DISPOSITIVO -->
    <p>
        <label for="dispositivo_electronico">Dispositivo utilizado:</label>
    </p>
    <p>
        <select id="dispositivo_electronico" name="dispositivo_electronico">
            <option value="">-- Selecciona --</option>
            <option value="ordenador"
                <?php if(isset($_POST["dispositivo_electronico"]) && $_POST["dispositivo_electronico"]=="ordenador") echo "selected"; ?>>
                Ordenador
            </option>
            <option value="tablet"
                <?php if(isset($_POST["dispositivo_electronico"]) && $_POST["dispositivo_electronico"]=="tablet") echo "selected"; ?>>
                Tablet
            </option>
            <option value="telefono"
                <?php if(isset($_POST["dispositivo_electronico"]) && $_POST["dispositivo_electronico"]=="telefono") echo "selected"; ?>>
                Teléfono
            </option>
        </select>
        <span><?php echo $errorDevice; ?></span>
    </p>

    <!-- BOTÓN (SIN LABEL) -->
    <p>
        <input type="submit" value="Iniciar prueba" />
    </p>
</form>

<?php if ($formularioPOST && $errorFormulario): ?>
    <h4>Formulario NO procesado debido a errores</h4>
<?php elseif ($formularioPOST && isset($mensajeBD)): ?>
    <h4>Formulario válido.</h4>
    <p><?php echo $mensajeBD; ?></p>
<?php endif; ?>

</main>
</body>
</html>
