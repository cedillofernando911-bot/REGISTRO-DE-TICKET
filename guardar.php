<?php
session_start();
$conexion = mysqli_connect("localhost", "root", "root", "soporte_tecnico");

// REGLA DE ORO: Solo Admin y Editor pueden pasar de aquí
if ($_SESSION['rol'] == 'Consultor') {
    die("<h2 style='color:red; text-align:center;'>ERROR: No tienes permisos de escritura. Contacta al Administrador Fernando.</h2>");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Aquí va tu código de INSERT que ya tienes...
    // $sql = "INSERT INTO tickets ...";
}
?>