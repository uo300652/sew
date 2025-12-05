<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - Base de Datos</title>
    <meta name="author" content="Matias Valle Trapiella">
    <meta name="description" content="Panel para gestionar la base de datos de MotoGP">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" type="image/x-icon" href="../multimedia/favicon-MotoGp.ico" />
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />
</head>
<body>
    <header>
        <h1>Panel de Configuracion del Test</h1>
    </header>
    <main>
        <form action="#" method="post">
                <h2>Reiniciar tablas (vaciar datos)</h2>
                <input type="submit" name="reiniciar_tablas" value="Reiniciar tablas">
                <h2>Borrar Base de Datos completa</h2>
                <input type="submit" name="borrar_db" value="Borrar DB">
                <h2>Crear Base de Datos y Tablas</h2>
                <input type="submit" name="crear_db" value="Crear DB">
                <h2>Reiniciar Base de Datos completa</h2>
                <input type="submit" name="reiniciar_db_completo" value="Reiniciar DB completo">
                <h2>Exportar tabla UsuarioTests a CSV</h2>
                <input type="submit" name="exportar_csv" value="Exportar CSV">
        </form>

        <?php
            include "ConfiguracionClase.php";
            $accionRealizada = ""; // Mensaje de resultado
            $db = new Configuracion();

            // Detectar qué botón se ha pulsado usando count($_POST)
            if (count($_POST) > 0) {
                if (isset($_POST['reiniciar_tablas'])) {
                    $db->reiniciar();
                    $accionRealizada = "Reinicio de tablas realizado exitosamente.";
                } elseif (isset($_POST['borrar_db'])) {
                    $db->borrar_DB();
                    $accionRealizada = "Borrado de base de datos realizado exitosamente.";
                } elseif (isset($_POST['crear_db'])) {
                    $db->crear_DB();
                    $accionRealizada = "Creación de base de datos realizado exitosamente.";
                } elseif (isset($_POST['reiniciar_db_completo'])) {
                    $db->reiniciar_DB();
                    $accionRealizada = "Reinicio completo de base de datos realizado exitosamente.";
                } elseif (isset($_POST['exportar_csv'])) {
                    $db->exportar_CSV();
                    $accionRealizada = "Exportación a CSV realizada exitosamente.";
                }
            }
            ?>
    </main>
</body>
</html>

