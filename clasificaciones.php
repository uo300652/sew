<?php
class Clasificacion
{
    private $documento;

    public function __construct()
    {
        $this->documento = "xml/circuitoEsquema.xml";
    }

    public function consultar()
    {
        $datos = file_get_contents($this->documento);
        $xml = new SimpleXMLElement($datos);

        echo "<h3>Información ganador</h3>";
        echo "<p>Ganador: {$xml->vencedor}</p>";
        $raw = $xml->tiempoVencedor;

        $raw = trim($raw, "PTS");            

        list($min, $sec) = explode('M', $raw); 

        $min = str_pad($min, 2, '0', STR_PAD_LEFT);

        $formatted = $min . ':' . $sec;

        echo "<p>Tiempo empleado: {$formatted}</p>";

        echo "<h3>Clasificación</h3>";
        echo "<ol>";

        foreach ($xml->clasificados->clasificado as $clasificado) {
            $nombre = $clasificado;
            echo "<li>{$nombre}</li>";
        }

        echo "</ol>";
    }
}
?>

<!DOCTYPE HTML>

<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Clasificaciones de MotoGP</title>

    <meta name ="author" content ="Matias Valle Trapiella" />
    <meta name="description" content="Resultados completos de MotoGP en el International 
    Chang Circuit: posiciones de pilotos, victorias, podios y clasificación final de la temporada." />

    <meta name="keywords" content="MotoGP, MotoGP-Desktop, clasificaciones MotoGP, resultados MotoGP, 
    ganador MotoGP, tiempos de carrera, ranking pilotos, clasificación final" />
    <meta name ="viewport" content ="width=device-width, initial-scale=1.0" />

    <link rel="icon" type = "image/x-icon" href="./multimedia/favicon-MotoGp.ico" />

    <link rel="stylesheet" type="text/css" href="estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="estilo/layout.css" />

    <script src="js/MenuMovil.js"></script>
</head>

<body>
    <header>
        <h1>
            <a href="index.html" title="Página de incio">MotoGP-Desktop</a>
        </h1>

        <button>☰ Menú</button>

        <nav>
            <a href="index.html">Inicio</a>
            <a href="piloto.html">Piloto</a>
            <a href="circuito.html">Circuito</a>
            <a href="meteorologia.html">Meteorología</a>
            <a href="clasificaciones.php" class="active">Clasificaciones</a>
            <a href="juegos.html">Juegos</a>
            <a href="ayuda.html">Ayuda</a>
        </nav>
    </header>

     <p>Estás en: <a href="index.html" title="Página de incio">Inicio</a> >> <strong>Clasificaciones</strong></p>

    <main>
        <h2>Clasificaciones de la carrera en el International Chang Circuit</h2>
        <?php
            $clasificaciones = new Clasificacion();
            $clasificaciones->consultar();
        ?>
    </main>
    <script>
        const menu = new MenuMovil();
    </script>
</body>
</html>