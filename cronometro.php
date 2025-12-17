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

    <script src="js/MenuMovil.js"></script>
</head>

<body>
    <header>
        <h1><a href="index.html" title="Página de inicio">MotoGP-Desktop</a></h1>
        <button>☰ Menú</button>

        <nav>
            <a href="index.html">Inicio</a>
            <a href="piloto.html">Piloto</a>
            <a href="circuito.html">Circuito</a>
            <a href="meteorologia.html">Meteorología</a>
            <a href="clasificaciones.php">Clasificaciones</a>
            <a href="juegos.html" class="active">Juegos</a>
            <a href="ayuda.html">Ayuda</a>
        </nav>
    </header>

    <p>Estás en: <a href="index.html">Inicio</a> >> <a href="juegos.html">Juegos</a> >> <strong>Cronómetro PHP</strong></p>

    <main>
        <h2>Cronómetro MotoGP</h2>

        <?php
            include './php/CronometroClase.php';
            session_start();     

            // Guardamos el cronómetro en sesión para mantener el estado entre peticiones
            if (!isset($_SESSION['cronometro'])) {
                $_SESSION['cronometro'] = new Cronometro();
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
            $_SESSION['cronometro'] = $cronometro;
        ?>

        <form method="post" action="#">
            <button type="submit" name="arrancar">Arrancar</button>
            <button type="submit" name="parar">Parar</button>
            <button type="submit" name="mostrar">Mostrar tiempo</button>
        </form>
    </main>
    <script>
        const menu = new MenuMovil();
    </script>
</body>
</html>

