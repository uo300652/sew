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
        5 => "¿Cuál fue la temperatura media el día antes de la carrera?",
        6 => "¿Quién fue el ganador de la carrera?",
        7 => "¿Cuál es el equipo actual de Jorge Martín?",
        8 => "Altura de Jorge Martín",
        9 => "¿Quién quedó en segundo lugar?",
        10 => "¿Cuál fue la dirección del viento el día de la carrera a las 17:00?"
    ];

    $respuestasPorDefecto = [
        1 => ["01:30.637"],
        2 => ["16"],
        3 => ["es el término que se utiliza en ciertas modalidades de automovilismo y motociclismo en circuito para designar el primer lugar en la parrilla de salida de una carrera."],
        4 => ["06:23"],
        5 => ["29.34", "29.34 °C"],
        6 => ["Marc Márquez"],
        7 => ["Aprilia Racing"],
        8 => ["168", "168 cm"],
        9 => ["Álex Márquez"],
        10 => ["55", "55°"]
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
    <meta name="author" content="Matias Valle Trapiella">
    <meta name="description" content="Formulario con las 10 preguntas de la prueba MotoGP.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
            <p><strong>Pregunta 1:</strong> <?= $preguntas[1] ?? "" ?></p>
            <p>
                <textarea name="p1" rows="2" cols="50"><?= isset($respuestas["p1"]) ? $respuestas["p1"] : "" ?></textarea>
                <span><?= $errorRespuestas["p1"] ?? "" ?></span>
            </p>

            <!-- Pregunta 2 -->
            <p><strong>Pregunta 2:</strong> <?= $preguntas[2] ?? "" ?></p>
            <p>
                <textarea name="p2" rows="2" cols="50"><?= isset($respuestas["p2"]) ? $respuestas["p2"] : "" ?></textarea>
                <span><?= $errorRespuestas["p2"] ?? "" ?></span>
            </p>

            <!-- Pregunta 3 -->
            <p><strong>Pregunta 3:</strong> <?= $preguntas[3] ?? "" ?></p>
            <p>
                <textarea name="p3" rows="2" cols="50"><?= isset($respuestas["p3"]) ? $respuestas["p3"] : "" ?></textarea>
                <span><?= $errorRespuestas["p3"] ?? "" ?></span>
            </p>

            <!-- Pregunta 4 -->
            <p><strong>Pregunta 4:</strong> <?= $preguntas[4] ?? "" ?></p>
            <p>
                <textarea name="p4" rows="2" cols="50"><?= isset($respuestas["p4"]) ? $respuestas["p4"] : "" ?></textarea>
                <span><?= $errorRespuestas["p4"] ?? "" ?></span>
            </p>

            <!-- Pregunta 5 -->
            <p><strong>Pregunta 5:</strong> <?= $preguntas[5] ?? "" ?></p>
            <p>
                <textarea name="p5" rows="2" cols="50"><?= isset($respuestas["p5"]) ? $respuestas["p5"] : "" ?></textarea>
                <span><?= $errorRespuestas["p5"] ?? "" ?></span>
            </p>

            <!-- Pregunta 6 -->
            <p><strong>Pregunta 6:</strong> <?= $preguntas[6] ?? "" ?></p>
            <p>
                <textarea name="p6" rows="2" cols="50"><?= isset($respuestas["p6"]) ? $respuestas["p6"] : "" ?></textarea>
                <span><?= $errorRespuestas["p6"] ?? "" ?></span>
            </p>

            <!-- Pregunta 7 -->
            <p><strong>Pregunta 7:</strong> <?= $preguntas[7] ?? "" ?></p>
            <p>
                <textarea name="p7" rows="2" cols="50"><?= isset($respuestas["p7"]) ? $respuestas["p7"] : "" ?></textarea>
                <span><?= $errorRespuestas["p7"] ?? "" ?></span>
            </p>

            <!-- Pregunta 8 -->
            <p><strong>Pregunta 8:</strong> <?= $preguntas[8] ?? "" ?></p>
            <p>
                <textarea name="p8" rows="2" cols="50"><?= isset($respuestas["p8"]) ? $respuestas["p8"] : "" ?></textarea>
                <span><?= $errorRespuestas["p8"] ?? "" ?></span>
            </p>

            <!-- Pregunta 9 -->
            <p><strong>Pregunta 9:</strong> <?= $preguntas[9] ?? "" ?></p>
            <p>
                <textarea name="p9" rows="2" cols="50"><?= isset($respuestas["p9"]) ? $respuestas["p9"] : "" ?></textarea>
                <span><?= $errorRespuestas["p9"] ?? "" ?></span>
            </p>

            <!-- Pregunta 10 -->
            <p><strong>Pregunta 10:</strong> <?= $preguntas[10] ?? "" ?></p>
            <p>
                <textarea name="p10" rows="2" cols="50"><?= isset($respuestas["p10"]) ? $respuestas["p10"] : "" ?></textarea>
                <span><?= $errorRespuestas["p10"] ?? "" ?></span>
            </p>

            <p>
                <input type="submit" value="Terminar prueba">
            </p>
        </form>
    </main>
</body>
</html>
