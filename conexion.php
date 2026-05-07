<?php
$host = "localhost";
$user = "root";       
$pass = "root";       // Cambiamos de "" a "root"
$db   = "soporte_tecnico"; 

// Intentar la conexión
$conexion = mysqli_connect($host, $user, $pass, $db);

// Si falla con 'root', intentamos sin contraseña por si acaso
if (!$conexion) {
    $conexion = mysqli_connect($host, $user, "", $db);
}

// Reporte de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!$conexion) {
    die("❌ Error definitivo de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8");
?>