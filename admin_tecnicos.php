<?php
include('conexion.php');
session_start();

// Seguridad: Solo el Admin entra aquí
if (!isset($_SESSION['usuario'])) { header("Location: login.php"); exit(); }

// --- LÓGICA PARA BORRAR TÉCNICO ---
if (isset($_GET['eliminar'])) {
    $id_borrar = mysqli_real_escape_string($conexion, $_GET['eliminar']);
    mysqli_query($conexion, "DELETE FROM tecnicos WHERE id_tecnico = '$id_borrar'");
    header("Location: admin_tecnicos.php");
    exit();
}

// Lógica para GUARDAR un nuevo técnico
if (isset($_POST['btn_guardar'])) {
    $nom = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $tel = mysqli_real_escape_string($conexion, $_POST['telefono']);
    
    if(!empty($nom) && !empty($tel)){
        mysqli_query($conexion, "INSERT INTO tecnicos (nombre_tecnico, telefono) VALUES ('$nom', '$tel')");
        header("Location: admin_tecnicos.php");
        exit();
    }
}

// Consultar la lista de técnicos
$res = mysqli_query($conexion, "SELECT * FROM tecnicos ORDER BY id_tecnico ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Directorio de Técnicos | FIME</title>
    <style>
        body { background: #000812; color: white; font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; }
        .sidebar { width: 280px; background: #0a111a; border-right: 1px solid #1a2433; padding: 25px; height: 100vh; position: fixed; }
        .main { flex: 1; margin-left: 280px; padding: 40px; }
        .card { background: #0a111a; padding: 30px; border-radius: 20px; border: 1px solid #1a2433; margin-bottom: 30px; }
        
        input { background: #161f2b; border: 1px solid #333; color: white; padding: 12px; border-radius: 10px; width: 250px; margin-right: 10px; outline: none; }
        .btn-blue { background: #007bff; color: white; border: none; padding: 12px 25px; border-radius: 10px; font-weight: bold; cursor: pointer; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { color: #007bff; text-transform: uppercase; font-size: 12px; padding: 15px; text-align: left; border-bottom: 2px solid #222; }
        td { padding: 15px; border-bottom: 1px solid #1a2433; font-size: 14px; }
        .id-tec { color: #007bff; font-weight: bold; }
        
        .btn-delete { color: #ff4d4d; text-decoration: none; font-weight: bold; font-size: 12px; }
        .btn-delete:hover { text-decoration: underline; }

        .nav-link { display: block; padding: 12px; color: #ccc; text-decoration: none; border-radius: 10px; margin-bottom: 10px; }
        .nav-link:hover { background: #007bff; color: white; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2 style="color:#007bff; text-align:center;">SYSTEMS ENG.</h2>
        <a href="index.php" class="nav-link">🏠 Panel Principal</a>
        <a href="actualizar_ticket.php" class="nav-link">🛠️ Gestionar Tickets</a>
        <a href="admin_tecnicos.php" class="nav-link" style="background: rgba(0,123,255,0.2); color: white;">👨‍🔧 Directorio Técnicos</a>
        <a href="logout.php" class="nav-link" style="margin-top:40px; color:#666;">🚪 Cerrar Sesión</a>
    </div>

    <div class="main">
        <h1>👨‍🔧 Directorio de Técnicos</h1>
        
        <div class="card">
            <h3 style="margin-top:0; color: #007bff;">Registrar Nuevo Técnico</h3>
            <form action="" method="POST">
                <input type="text" name="nombre" placeholder="Nombre completo" required>
                <input type="text" name="telefono" placeholder="Teléfono" required>
                <button type="submit" name="btn_guardar" class="btn-blue">REGISTRAR</button>
            </form>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Técnico</th>
                        <th>Teléfono</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($f = mysqli_fetch_assoc($res)): ?>
                    <tr>
                        <td class="id-tec">#<?php echo $f['id_tecnico']; ?></td>
                        <td><b><?php echo htmlspecialchars($f['nombre_tecnico']); ?></b></td>
                        <td><?php echo htmlspecialchars($f['telefono']); ?></td>
                        <td>
                            <a href="admin_tecnicos.php?eliminar=<?php echo $f['id_tecnico']; ?>" 
                               class="btn-delete" 
                               onclick="return confirm('¿Seguro que quieres eliminar este técnico?')">BORRAR</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>