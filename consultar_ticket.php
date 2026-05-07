<?php
include('conexion.php');
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$rol = $_SESSION['rol'];
$buscar = isset($_GET['buscar']) ? mysqli_real_escape_string($conexion, $_GET['buscar']) : "";

// Lógica de Folio FIME-XXX
$id_real = $buscar;
if (strpos(strtoupper($buscar), 'FIME-') !== false) {
    $id_real = (int)str_replace('FIME-', '', strtoupper($buscar));
}

$sql = "SELECT * FROM tickets WHERE 1=1";
if ($rol == 'cliente') {
    $sql .= ($buscar != "") ? " AND id_ticket = '$id_real'" : " AND id_ticket = '0'";
} else {
    if ($buscar != "") {
        $sql .= " AND (id_ticket = '$id_real' OR nombre_cliente LIKE '%$buscar%')";
    }
}

$sql .= " ORDER BY id_ticket DESC";
$resultado = mysqli_query($conexion, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bitácora FIME</title>
    <style>
        body { background: #000812; color: white; font-family: 'Segoe UI', sans-serif; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn { text-decoration: none; padding: 12px 20px; border-radius: 10px; font-weight: bold; border: none; cursor: pointer; }
        .btn-blue { background: #007bff; color: white; }
        .btn-dark { background: #333; color: white; }
        table { width: 100%; border-collapse: collapse; background: rgba(255,255,255,0.01); border-radius: 15px; overflow: hidden; }
        th { color: #555; text-transform: uppercase; font-size: 11px; padding: 20px; text-align: left; border-bottom: 2px solid #222; }
        td { padding: 20px; border-bottom: 1px solid #111; font-size: 14px; }
        .status { color: #007bff; font-weight: bold; }
        .tecnico-label { background: rgba(0, 123, 255, 0.1); color: #007bff; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h1><?php echo ($rol == 'cliente') ? "Mi Equipo" : "Centro de Operaciones"; ?></h1>
        <a href="index.php" class="btn btn-dark">🏠 VOLVER</a>
    </div>

    <div style="margin-bottom: 30px;">
        <form action="consultar_ticket.php" method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="buscar" placeholder="Folio (FIME-001)" value="<?php echo htmlspecialchars($buscar); ?>" style="background:#1a2433; border:1px solid #333; padding:12px; border-radius:10px; color:white; width:250px;">
            <button type="submit" class="btn btn-blue">BUSCAR</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Folio</th>
                <th>Cliente</th>
                <th>Descripción / Asunto</th>
                <th>Técnico Responsable</th>
                <th>Estatus</th>
                <?php if($rol != 'cliente'): ?> <th>Acción</th> <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($resultado)): 
                $folio_fime = "FIME-" . str_pad($row['id_ticket'], 3, "0", STR_PAD_LEFT);
                // Corregimos el nombre de la columna para evitar el Warning
                // Usamos 'asunto' porque así aparece en tu Workbench
                $equipo_detalle = $row['asunto'] ?? ($row['datos_equipo'] ?? 'Sin descripción');
            ?>
                <tr>
                    <td style="font-weight: bold; color: #007bff;"><?php echo $folio_fime; ?></td>
                    <td><?php echo htmlspecialchars($row['nombre_cliente'] ?? 'Cargando...'); ?></td>
                    <td style="color: #888;"><?php echo htmlspecialchars($equipo_detalle); ?></td>
                    
                    <td><span class="tecnico-label"><?php echo htmlspecialchars($row['tecnico'] ?? 'Pendiente'); ?></span></td>
                    
                    <td class="status"><?php echo $row['estatus']; ?></td>
                    
                    <?php if($rol != 'cliente'): ?>
                    <td>
                        <a href="actualizar_ticket.php?id=<?php echo $row['id_ticket']; ?>" style="color:#007bff; text-decoration:none; font-weight:bold; margin-right:15px;">GESTIONAR</a>
                        <a href="borrar_ticket.php?id=<?php echo $row['id_ticket']; ?>" style="color:#e74c3c; text-decoration:none; font-weight:bold;" onclick="return confirm('¿Borrar?')">BORRAR</a>
                    </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>