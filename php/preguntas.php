<?php
    include "CronometroClase.php";
    include "TestResultadosModel.php";
    include "DB.php";

    session_start();

    $db = new DB();
    $conn = $db->getConnection();

    $errorFormulario = false;
    $respuestas = [];
    $errorRespuestas = [];

    // Lista de preguntas y respuestas por defecto
    $preguntas = [
        1 => "¿Cuál fue el tiempo del ganador de la carrera?",
        2 => "¿Cuántos podios tuvo Jorge Martín en la temporada de 2024?",
        3 => "¿Qué es una pole?",
        4 => "¿A qué hora fue el amanecer el día de la carrera?",
        5 => "¿Cuál es la longitud del circuito?",
        6 => "¿Quién fue el ganador de la carrera?",
        7 => "¿Cuál es el equipo actual de Jorge Martín?",
        8 => "Altura de Jorge Martín",
        9 => "¿Quién quedó en segundo lugar?",
        10 => "¿Cuál fue la temperatura media el último día de entramiento?"
    ];

    $respuestasPorDefecto = [
        1 => ["01:30.637"],
        2 => ["16"],
        3 => ["es el término que se utiliza para designar el primer lugar en la parrilla de salida de una carrera"],
        4 => ["06:23"],
        5 => ["4553"],
        6 => ["Marc Márquez"],
        7 => ["Aprilia Racing"],
        8 => ["168"],
        9 => ["Álex Márquez"],
        10 => ["29.92"]
    ];

    // Procesamiento del formulario usando count($_POST)
    if (count($_POST) > 0) {
        $respuestas = $_POST;

        // Validar que todas las respuestas estén llenas
        foreach ($preguntas as $num => $pregunta) {
            $campo = "p$num";
            
            if (empty($respuestas[$campo])) {
                $errorRespuestas[$campo] = " * Esta pregunta es obligatoria";
                $errorFormulario = true;
            } elseif (!in_array($respuestas[$campo], $respuestasPorDefecto[$num])) {
                $errorRespuestas[$campo] = " * Respuesta incorrecta";
                $errorFormulario = true;
            }
        }

        // Si todo está correcto, se puede insertar en la base de datos
        if (!$errorFormulario) {
            $cronometro = $_SESSION["cronometro"];
            $cronometro->parar();
            $time_seconds = $cronometro->getTiempo();
            $testModel = new TestResultadosModel($conn);
            $completed = 1; 
            $test_id = $_SESSION["test_id"];

            $testModel->actualizarResultado($test_id, $time_seconds, $completed);

            header("Location: sugerencias.php");
            exit;
        }
    } else {
        // Si no se ha enviado el formulario, prellenamos con las respuestas por defecto
        $respuestas = $respuestasPorDefecto;
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Prueba MotoGP</title>
    <meta name="author" content="Matias Valle Trapiella" />
    <meta name="description" content="Formulario con las 10 preguntas de la prueba MotoGP." />
    <meta name="keywords" content="MotoGP, prueba MotoGP, cuestionario MotoGP, test MotoGP, 
    preguntas MotoGP, formulario de evaluación, evaluación de conocimientos, usabilidad web, 
    test de usuario, experiencia de usuario, interacción usuario" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/x-icon" href="../multimedia/favicon-MotoGp.ico" />
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />
</head>
<body>

<header>
    <h1>Prueba MotoGP - 10 preguntas</h1>
</header>

<main>
<form action="#" method="post">

    <!-- Pregunta 1 -->
    <fieldset>
        <legend><strong>Pregunta 1:</strong> <?= $preguntas[1] ?></legend>

        <label><input type="radio" name="p1" value="01:29.842"
            <?= (isset($respuestas["p1"]) && $respuestas["p1"]=="01:29.842")?"checked":"" ?>> 01:29.842</label><br>

        <label><input type="radio" name="p1" value="01:31.102"
            <?= (isset($respuestas["p1"]) && $respuestas["p1"]=="01:31.102")?"checked":"" ?>> 01:31.102</label><br>

        <label><input type="radio" name="p1" value="01:30.637"
            <?= (isset($respuestas["p1"]) && $respuestas["p1"]=="01:30.637")?"checked":"" ?>> 01:30.637</label><br>

        <label><input type="radio" name="p1" value="01:28.991"
            <?= (isset($respuestas["p1"]) && $respuestas["p1"]=="01:28.991")?"checked":"" ?>> 01:28.991</label><br>

        <label><input type="radio" name="p1" value="01:30.400"
            <?= (isset($respuestas["p1"]) && $respuestas["p1"]=="01:30.400")?"checked":"" ?>> 01:30.400</label>

        <span><?= $errorRespuestas["p1"] ?? "" ?></span>
    </fieldset>

    <!-- Pregunta 2 -->
    <p><strong>Pregunta 2:</strong> <?= $preguntas[2] ?></p>
    <p>
        <label for="p2">Respuesta numérica:</label>
        <input id="p2" type="number" name="p2" step="1"
            value="<?= $respuestas["p2"] ?? "" ?>">
        <span><?= $errorRespuestas["p2"] ?? "" ?></span>
    </p>

    <!-- Pregunta 3 -->
    <fieldset>
        <legend><strong>Pregunta 3:</strong> <?= $preguntas[3] ?></legend>

        <label><input type="radio" name="p3"
            value="<?= $respuestasPorDefecto[3][0] ?>"
            <?= (isset($respuestas["p3"]) && $respuestas["p3"]==$respuestasPorDefecto[3][0])?"checked":"" ?>>
            Es el término usado en automovilismo y motociclismo para designar el primer lugar en parrilla.
        </label><br><br>

        <label><input type="radio" name="p3" value="es la vuelta mas rapida de la carrera">
            Es la vuelta más rápida de la carrera.
        </label><br>

        <label><input type="radio" name="p3" value="es el nombre del sistema de puntuacion en motogp">
            Es el nombre del sistema de puntuación en MotoGP.
        </label><br>

        <label><input type="radio" name="p3" value="es un tipo especial de neumatico utilizado en clasificacion">
            Es un tipo especial de neumático utilizado en clasificación.
        </label>

        <span><?= $errorRespuestas["p3"] ?? "" ?></span>
    </fieldset>

    <!-- Pregunta 4 -->
    <fieldset>
        <legend><strong>Pregunta 4:</strong> <?= $preguntas[4] ?></legend>

        <label><input type="radio" name="p4" value="06:23" <?= ($respuestas["p4"] ?? "")=="06:23"?"checked":"" ?>> 06:23</label><br>
        <label><input type="radio" name="p4" value="06:10" <?= ($respuestas["p4"] ?? "")=="06:10"?"checked":"" ?>> 06:10</label><br>
        <label><input type="radio" name="p4" value="06:37" <?= ($respuestas["p4"] ?? "")=="06:37"?"checked":"" ?>> 06:37</label><br>
        <label><input type="radio" name="p4" value="05:58" <?= ($respuestas["p4"] ?? "")=="05:58"?"checked":"" ?>> 05:58</label><br>
        <label><input type="radio" name="p4" value="06:30" <?= ($respuestas["p4"] ?? "")=="06:30"?"checked":"" ?>> 06:30</label>

        <span><?= $errorRespuestas["p4"] ?? "" ?></span>
    </fieldset>

    <!-- Pregunta 5 -->
    <p><strong>Pregunta 5:</strong> <?= $preguntas[5] ?></p>
    <p>
        <label for="p5">Respuesta numérica:</label>
        <input id="p5" type="number" name="p5" step="1"
            value="<?= $respuestas["p5"] ?? "" ?>">
        <span><?= $errorRespuestas["p5"] ?? "" ?></span>
    </p>

    <!-- Pregunta 6 -->
    <fieldset>
        <legend><strong>Pregunta 6:</strong> <?= $preguntas[6] ?></legend>

        <label><input type="radio" name="p6" value="Álex Márquez" <?= ($respuestas["p6"] ?? "")=="Álex Márquez"?"checked":"" ?>> Álex Márquez</label><br>
        <label><input type="radio" name="p6" value="Jorge Martín" <?= ($respuestas["p6"] ?? "")=="Jorge Martín"?"checked":"" ?>> Jorge Martín</label><br>
        <label><input type="radio" name="p6" value="Marc Márquez" <?= ($respuestas["p6"] ?? "")=="Marc Márquez"?"checked":"" ?>> Marc Márquez</label><br>
        <label><input type="radio" name="p6" value="Fabio Quartararo" <?= ($respuestas["p6"] ?? "")=="Fabio Quartararo"?"checked":"" ?>> Fabio Quartararo</label>

        <span><?= $errorRespuestas["p6"] ?? "" ?></span>
    </fieldset>

    <!-- Pregunta 7 -->
    <fieldset>
        <legend><strong>Pregunta 7:</strong> <?= $preguntas[7] ?></legend>

        <label><input type="radio" name="p7" value="Ducati" <?= ($respuestas["p7"] ?? "")=="Ducati"?"checked":"" ?>> Ducati</label><br>
        <label><input type="radio" name="p7" value="Honda" <?= ($respuestas["p7"] ?? "")=="Honda"?"checked":"" ?>> Honda</label><br>
        <label><input type="radio" name="p7" value="Yamaha" <?= ($respuestas["p7"] ?? "")=="Yamaha"?"checked":"" ?>> Yamaha</label><br>
        <label><input type="radio" name="p7" value="Aprilia Racing" <?= ($respuestas["p7"] ?? "")=="Aprilia Racing"?"checked":"" ?>> Aprilia Racing</label>

        <span><?= $errorRespuestas["p7"] ?? "" ?></span>
    </fieldset>

    <!-- Pregunta 8 -->
    <p><strong>Pregunta 8:</strong> <?= $preguntas[8] ?></p>
    <p>
        <label for="p8">Respuesta numérica:</label>
        <input id="p8" type="number" name="p8" step="1"
            value="<?= $respuestas["p8"] ?? "" ?>">
        <span><?= $errorRespuestas["p8"] ?? "" ?></span>
    </p>

    <!-- Pregunta 9 -->
    <fieldset>
        <legend><strong>Pregunta 9:</strong> <?= $preguntas[9] ?></legend>

        <label><input type="radio" name="p9" value="Álex Márquez" <?= ($respuestas["p9"] ?? "")=="Álex Márquez"?"checked":"" ?>> Álex Márquez</label><br>
        <label><input type="radio" name="p9" value="Marc Márquez" <?= ($respuestas["p9"] ?? "")=="Marc Márquez"?"checked":"" ?>> Marc Márquez</label><br>
        <label><input type="radio" name="p9" value="Jorge Martín" <?= ($respuestas["p9"] ?? "")=="Jorge Martín"?"checked":"" ?>> Jorge Martín</label><br>
        <label><input type="radio" name="p9" value="Francesco Bagnaia" <?= ($respuestas["p9"] ?? "")=="Francesco Bagnaia"?"checked":"" ?>> Francesco Bagnaia</label>

        <span><?= $errorRespuestas["p9"] ?? "" ?></span>
    </fieldset>

    <!-- Pregunta 10 -->
    <p><strong>Pregunta 10:</strong> <?= $preguntas[10] ?></p>
    <p>
        <label for="p10">Respuesta numérica:</label>
        <input id="p10" type="number" name="p10" step="0.01"
            value="<?= $respuestas["p10"] ?? "" ?>">
        <span><?= $errorRespuestas["p10"] ?? "" ?></span>
    </p>

    <p>
        <input type="submit" value="Terminar prueba">
    </p>

</form>
</main>
</body>
</html>
