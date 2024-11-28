<?php
session_start();
include("includes/conexion.php");
conectar();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Programación II</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h2>Agencia de Viajes</h2>
        <ul>
            <li><a href="php/ABM_paquetes.php">ABM Paquetes</a></li>
            <li><a href="php/consulta_paquetes.php">Consultar Paquetes</a></li>
        </ul>
    </header>
</body>
</html>