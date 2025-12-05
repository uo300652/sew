<?php
class Cronometro
{
    private $tiempo;   
    private $inicio;   

    public function __construct()
    {
        $this->tiempo = 0;  
        $this->inicio = null;
    }

    public function arrancar()
    {
        $this->inicio = microtime(true); 
        $this->tiempo = 0;               
    }

    public function parar()
    {
        if ($this->inicio === null) return; 

        $fin = microtime(true);
        $this->tiempo = $fin - $this->inicio;
        $this->inicio = null;
    }

    public function mostrar()
    {
        $totalSegundos = $this->tiempo;
        $minutos = floor($totalSegundos / 60);
        $segundos = $totalSegundos - ($minutos * 60);
        $segundosFormateados = number_format($segundos, 1);

        echo "<p>Tiempo transcurrido: " . sprintf("%02d:%04.1f", $minutos, $segundosFormateados) . "</p>";
    }

    public function getTiempo()
    {
        return $this->tiempo;
    }
}
?>
