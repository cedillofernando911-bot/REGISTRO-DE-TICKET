<?php
// Usamos los nombres exactos que salen en tu captura de Railway
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db   = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');

// Conexión con puerto incluido para evitar el Error 500
$conexion = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conexion) {
    die("Fallo total: " . mysqli_connect_error());
}
?>
