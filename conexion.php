<?php
// Reportar todos los errores para ver qué pasa realmente
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db   = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');

// Si alguna variable está vacía, Railway no las leyó bien
if (!$host || !$user || !$db) {
    die("Error: Faltan variables de entorno en Railway.");
}

$conexion = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conexion) {
    die("Fallo de conexión: " . mysqli_connect_error());
}
?>
