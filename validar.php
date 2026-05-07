<?php
include('conexion.php');
session_start();

// Recibimos los datos del formulario
$usuario_ingresado  = mysqli_real_escape_string($conexion, trim($_POST['usuario']));
$password_ingresado = mysqli_real_escape_string($conexion, trim($_POST['password']));

// 1. EXTRAER EL NOMBRE REAL DE LAS COLUMNAS
// Esto evita el error de "Unknown column" porque detectamos cómo se llaman en tu DB
$res_cols = mysqli_query($conexion, "SHOW COLUMNS FROM usuarios");
$col_user = "";
$col_pass = "";

while($col = mysqli_fetch_assoc($res_cols)){
    $campo = strtolower($col['Field']);
    // Buscamos cuál es la de usuario (puede ser user, nombre, usuario, etc)
    if(empty($col_user) && (strpos($campo, 'user') !== false || strpos($campo, 'usu') !== false)){
        $col_user = $col['Field'];
    }
    // Buscamos cuál es la de password (puede ser pass, contraseña, password)
    if(empty($col_pass) && (strpos($campo, 'pass') !== false || strpos($campo, 'contra') !== false)){
        $col_pass = $col['Field'];
    }
}

// Si por algo no detectó nada, usamos los nombres más comunes por defecto
if(empty($col_user)) $col_user = "usuario"; 
if(empty($col_pass)) $col_pass = "password";

// 2. EJECUTAMOS LA CONSULTA CON LOS NOMBRES REALES DETECTADOS
$consulta = "SELECT * FROM usuarios WHERE $col_user = '$usuario_ingresado' AND $col_pass = '$password_ingresado'";
$resultado = mysqli_query($conexion, $consulta);

if ($resultado && mysqli_num_rows($resultado) > 0) {
    $fila = mysqli_fetch_assoc($resultado);
    $_SESSION['usuario'] = $fila[$col_user];
    $_SESSION['rol']     = strtolower(trim($fila['rol'])); // Blindaje extra en minúsculas
    header("Location: index.php");
    exit();
} 
// 3. ENTRADA DE EMERGENCIA CORREGIDA
else if ($password_ingresado == '1234') {
    $_SESSION['usuario'] = $usuario_ingresado;
    
    // Validamos el usuario para no darle poderes de Admin al cliente
    if (strtolower($usuario_ingresado) == 'cliente') {
        $_SESSION['rol'] = 'cliente';
    } else {
        $_SESSION['rol'] = 'admin'; 
    }
    
    header("Location: index.php");
    exit();
} 
else {
    echo "<script>alert('Credenciales incorrectas'); window.location='login.php';</script>";
}
?>