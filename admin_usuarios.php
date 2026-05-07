<?php 
session_start();
include('conexion.php'); 

// SEGURIDAD
if (!isset($_SESSION['rol']) || (strtolower($_SESSION['rol']) != 'admin')) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Control de Personal | SRI FIME</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        
        /* FORZAMOS EL DISEÑO AZUL */
        body {
            font-family: 'Inter', sans-serif !important;
            background: radial-gradient(circle at top right, #003366, #000d1a) !important;
            color: #fff !important;
            margin: 0; min-height: 100vh;
            display: flex; align-items: center; justify-content: center; padding: 20px;
        }

        .container {
            width: 100%; max-width: 900px;
            background: rgba(255, 255, 255, 0.05) !important;
            backdrop-filter: blur(15px) !important;
            padding: 40px; border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        }

        h1 { color: #007bff !important; margin: 0 0 20px 0; font-size: 26px; }

        table {
            width: 100%; border-collapse: collapse;
            background: rgba(0, 0, 0, 0.2); border-radius: 16px;
            overflow: hidden; margin-top: 10px;
        }

        th {
            background: rgba(0, 123, 255, 0.15) !important;
            color: #007bff !important;
            text-align: left; padding: 18px;
            font-size: 0.85rem; text-transform: uppercase;
        }

        td { padding: 16px; border-bottom: 1px solid rgba(255, 255, 255, 0.05); font-size: 14px; }

        .badge {
            padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 700;
            background: rgba(0, 123, 255, 0.2); color: #007bff; border: 1px solid rgba(0, 123, 255, 0.3);
        }

        .btn-back { display: inline-block; margin-top: 30px; color: rgba(255, 255, 255, 0.4); text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛡️ Control de Personal</h1>
        
        <table>
            <thead>
                <tr>
                    <th>Nombre Registrado</th>
                    <th>ID / Usuario</th>
                    <th>Nivel de Acceso</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // AQUI ESTÁ LA PRUEBA: Usamos SELECT * , no SELECT nombre
                $res = mysqli_query($conexion, "SELECT * FROM usuarios");
                
                if($res) {
                    while($u = mysqli_fetch_array($res)) {
                        $nombre_mostrar = $u['nombre'] ?? $u[1] ?? 'Usuario FIME';
                        $usuario_mostrar = $u['usuario'] ?? $u['user'] ?? $u[2] ?? 'ID-000';
                        $rol_mostrar = $u['rol'] ?? $u[4] ?? $u[3] ?? 'Personal';

                        echo "<tr>
                                <td>".htmlspecialchars($nombre_mostrar)."</td>
                                <td>".htmlspecialchars($usuario_mostrar)."</td>
                                <td><span class='badge'>".htmlspecialchars($rol_mostrar)."</span></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' style='text-align:center;'>Verifica tu base de datos.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        
        <a href="index.php" class="btn-back">← Volver al Panel Principal</a>
    </div>
</body>
</html>