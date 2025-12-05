<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Cronómetro MotoGP</title>
    <meta name="author" content="Matias Valle Trapiella" />
    <meta name="description" content="Cronómetro MotoGP 2025" />
    <meta name="keywords" content="MotoGP, Cronómetro, Juego" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="icon" type="image/x-icon" href="multimedia/favicon-MotoGp.ico" />
    <link rel="stylesheet" type="text/css" href="estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="estilo/layout.css" />
</head>

<body>
    <header>
        <h1><a href="index.html" title="Página de inicio">MotoGP-Desktop</a></h1>
        <nav>
            <a href="index.html" title="Inicio">Inicio</a>
            <a href="piloto.html" title="Piloto">Piloto</a>
            <a href="circuito.html" title="Circuito">Circuito</a>
            <a href="meteorologia.html" title="Meteorología">Meteorología</a>
            <a href="clasificaciones.php" title="Clasificaciones">Clasificaciones</a>
            <a href="juegos.html" title="Juegos" class="active">Juegos</a>
            <a href="ayuda.html" title="Ayuda">Ayuda</a>
        </nav>
    </header>

    <p>Estás en: <a href="index.html">Inicio</a> >> <a href="juegos.html">Juegos</a> >> <strong>Cronómetro PHP</strong></p>

    <main>
        <h2>Cronómetro MotoGP</h2>

        <?php
            session_start();

            include './php/CronometroClase.php';

            // Guardamos el cronómetro en sesión para mantener el estado entre peticiones
            if (!isset($_SESSION['cronometro'])) {
                $_SESSION['cronometro'] = serialize(new Cronometro());
            }

            // Recuperamos el cronómetro
            $cronometro = $_SESSION['cronometro'];

            // Procesamos botones
            $mensaje = "";
            if (isset($_POST['arrancar'])) {
                $cronometro->arrancar();
                $mensaje = "Cronómetro arrancado.";
            }

            if (isset($_POST['parar'])) {
                $cronometro->parar();
                $mensaje = "Cronómetro detenido.";
            }

            if (isset($_POST['mostrar'])) {
                $cronometro->mostrar();
            }

            // Guardamos nuevamente en sesión
            $_SESSION['cronometro'] = serialize($cronometro);
        ?>

        <form method="post" action="#">
            <button type="submit" name="arrancar">Arrancar</button>
            <button type="submit" name="parar">Parar</button>
            <button type="submit" name="mostrar">Mostrar tiempo</button>
        </form>
    </main>
</body>
</html>

