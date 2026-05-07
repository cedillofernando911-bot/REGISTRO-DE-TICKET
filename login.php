<?php
// 1. INICIAMOS SESIÓN Y CONEXIÓN
session_start();
include('conexion.php');

// Si el usuario ya está logueado, lo mandamos al index automáticamente
if (isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$error_login = "";

// 2. LÓGICA DE VALIDACIÓN (Se activa al dar clic al botón)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Limpiamos los datos para evitar hackeos (Inyección SQL)
    $user = mysqli_real_escape_string($conexion, $_POST['usuario']);
    $pass = mysqli_real_escape_string($conexion, $_POST['password']);

    // Buscamos al usuario en la base de datos
    $sql = "SELECT * FROM usuarios WHERE usuario = '$user' AND password = '$pass'";
    $resultado = mysqli_query($conexion, $sql);

    if (mysqli_num_rows($resultado) > 0) {
        $datos = mysqli_fetch_assoc($resultado);
        
        // Guardamos los datos en la sesión
        $_SESSION['usuario'] = $datos['usuario'];
        $_SESSION['rol'] = $datos['rol']; 
        
        // ¡Vámonos al index!
        header("Location: index.php");
        exit();
    } else {
        $error_login = "Usuario o contraseña incorrectos. Intenta de nuevo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soporte FIME | Acceso</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at top right, #003366, #001a33);
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin: 0;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            padding: 40px; 
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 340px; 
            text-align: center;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        }

        .logo-header {
            width: 110px; height: 110px; margin: 0 auto 20px;
            border-radius: 50%; border: 3px solid #007bff;
            overflow: hidden; box-shadow: 0 0 20px rgba(0, 123, 255, 0.4);
            display: flex; align-items: center; justify-content: center;
            background: #000;
        }

        .logo-header img { width: 100%; height: 100%; object-fit: cover; }

        h2 { color: #fff; margin-bottom: 20px; font-weight: 600; margin-top: 0; }

        /* Estilo para el mensaje de error */
        .error-box {
            color: #ff4d4d;
            background: rgba(255, 77, 77, 0.1);
            padding: 10px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 77, 77, 0.2);
        }

        input {
            width: 100%; padding: 14px; margin: 12px 0;
            background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px; color: #fff; outline: none; box-sizing: border-box;
        }

        input:focus { border-color: #007bff; background: rgba(255, 255, 255, 0.15); }

        button {
            width: 100%; padding: 14px; background: #007bff;
            border: none; color: white; font-weight: 600;
            border-radius: 12px; cursor: pointer; margin-top: 20px;
            transition: all 0.3s; box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }

        button:hover { background: #0056b3; transform: translateY(-2px); }

        .footer-text { color: rgba(255,255,255,0.4); font-size: 0.75rem; margin-top: 25px; }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo-header">
            <img src="fondo_soporte.jpg" alt="Logo FIME">
        </div>

        <h2>Portal de Soporte</h2>

        <?php if($error_login != ""): ?>
            <div class="error-box"><?php echo $error_login; ?></div>
        <?php endif; ?>
        
        <form action="login.php" method="POST">
            <input type="text" name="usuario" placeholder="Nombre de usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Iniciar Sesión</button>
        </form>
        
        <div class="footer-text">© 2026 FIME | Systems Engineering</div>
    </div>

</body>
</html>
