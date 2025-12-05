<?php
class Clasificaciones
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
    <!-- Datos que describen el documento -->
    <meta charset="UTF-8" />
    <title>MotoGp-Piloto</title>

    <meta name ="author" content ="Matias Valle Trapiella" />
    <meta name ="description" content ="Clasificaciones de MotoGp" />
    <meta name ="keywords" content ="MotoGP" />
    <meta name ="viewport" content ="width=device-width, initial-scale=1.0" />

    <link rel="icon" type = "image/x-icon" href="./multimedia/favicon-MotoGp.ico" />

    <link rel="stylesheet" type="text/css" href="estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="estilo/layout.css" />
</head>

<body>
    <header>
        <!-- Datos con el contenidos que aparece en el navegador -->
        <h1>
            <a href="index.html" title="Página de incio">MotoGP-Desktop</a>
        </h1>

        <nav>
            <a href="index.html" title="Página de incio">Inicio</a>
            <a href="piloto.html" title="Información del piloto">Piloto</a>
            <a href="circuito.html" title="Información del circuito">Circuito</a>
            <a href="meteorologia.html" title="Meteorología de MotoGp">Meteorología</a>
            <a href="clasificaciones.php" title="Clasificaciones de MotoGp" class="active">Clasificaciones</a>
            <a href="juegos.html" title="Juegos de MotoGp">Juegos</a>
            <a href="ayuda.html" title="Información de ayuda del proyecto MotoGP-Desktop">Ayuda</a>
        </nav>
    </header>

     <p>Estás en: <a href="index.html" title="Página de incio">Inicio</a> >> <strong>Clasificaciones</strong></p>

    <main>
        <h2>Clasificaciones de MotoGp-Desktop</h2>
        <?php
            $clasificaciones = new Clasificaciones();
            $clasificaciones->consultar();
        ?>
    </main>
</body>
</html>