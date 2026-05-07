<?php
// Configuración de zona horaria para FIME (Monterrey)
date_default_timezone_set('America/Monterrey'); 

include('conexion.php');
session_start();

// 1. SEGURIDAD
if (!isset($_SESSION['usuario'])) { header("Location: login.php"); exit(); }

$ticket = null;
$id = "";

// 2. BUSCADOR MANUAL
if (isset($_POST['buscar_folio']) || isset($_GET['id'])) {
    $id_buscado = isset($_POST['folio_input']) ? $_POST['folio_input'] : $_GET['id'];
    $id_limpio = filter_var($id_buscado, FILTER_SANITIZE_NUMBER_INT);
    
    $consulta = mysqli_query($conexion, "SELECT * FROM tickets WHERE id_ticket = '$id_limpio'");
    if ($consulta && mysqli_num_rows($consulta) > 0) {
        $ticket = mysqli_fetch_assoc($consulta);
        $id = $ticket['id_ticket'];
    } else if(isset($_POST['buscar_folio'])) {
        echo "<script>alert('No se encontró el folio: $id_buscado');</script>";
    }
}

// 3. GUARDAR CAMBIOS (Lógica de Historial Acumulativo)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_ticket_update'])) {
    $id_update = mysqli_real_escape_string($conexion, $_POST['id_ticket_update']);
    $tecnico   = mysqli_real_escape_string($conexion, $_POST['tecnico_asignado']);
    $estatus   = mysqli_real_escape_string($conexion, $_POST['nuevo_estatus']);
    $notas     = mysqli_real_escape_string($conexion, $_POST['descripcion_avance']);
    
    // Capturamos el nuevo avance que escribió el técnico
    $nuevo_comentario = mysqli_real_escape_string($conexion, $_POST['avances_cliente']);

    // Traemos lo que ya estaba guardado antes para no borrarlo
    $query_old = mysqli_query($conexion, "SELECT avances FROM tickets WHERE id_ticket = '$id_update'");
    $row_old = mysqli_fetch_assoc($query_old);
    $historial_viejo = $row_old['avances'];

    // Si el técnico escribió algo, le ponemos fecha y lo acumulamos arriba
    if (!empty(trim($nuevo_comentario))) {
        $fecha_hora = date("d/m/Y H:i");
        $avances_finales = "[$fecha_hora]: $nuevo_comentario\n--------------------------\n" . $historial_viejo;
    } else {
        $avances_finales = $historial_viejo;
    }

    $sql_update = "UPDATE tickets SET 
                    estatus = '$estatus', 
                    diagnostico = '$notas', 
                    tecnico = '$tecnico', 
                    avances = '$avances_finales' 
                   WHERE id_ticket = '$id_update'";
    
    if (mysqli_query($conexion, $sql_update)) {
        // Recargar datos para mostrar actualización inmediata
        $consulta = mysqli_query($conexion, "SELECT * FROM tickets WHERE id_ticket = '$id_update'");
        $ticket = mysqli_fetch_assoc($consulta);
        $id = $id_update;
        echo "<script>alert('¡Historial actualizado con éxito!');</script>";
    } else {
        echo "Error: " . mysqli_error($conexion);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Ticket - FIME</title>
    <style>
        body { background: #000812; color: white; font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: #0a111a; padding: 40px; border-radius: 25px; border: 1px solid #1a2433; width: 450px; box-shadow: 0 15px 35px rgba(0,0,0,0.7); }
        h2 { color: #007bff; text-align: center; margin-bottom: 25px; text-transform: uppercase; letter-spacing: 1px; }
        .info-box { background: rgba(0, 123, 255, 0.05); border-left: 4px solid #007bff; padding: 15px; border-radius: 10px; margin-bottom: 25px; font-size: 14px; }
        label { display: block; margin-bottom: 8px; color: #666; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        input[type="text"], select, textarea { width: 100%; padding: 15px; background: #1a2433; border: 1px solid #333; color: white; border-radius: 12px; font-size: 15px; margin-bottom: 20px; outline: none; box-sizing: border-box; }
        textarea { height: 100px; resize: none; }
        .btn-save { background: #007bff; color: white; border: none; padding: 20px; width: 100%; border-radius: 15px; font-weight: bold; cursor: pointer; font-size: 16px; transition: 0.3s; }
        .btn-save:hover { background: #0056b3; transform: scale(1.02); }
        .back-link { display: block; text-align: center; margin-top: 25px; color: #444; text-decoration: none; font-size: 13px; }
        .label-cliente { color: #28a745; border-bottom: 1px solid #28a745; display: inline-block; margin-bottom: 10px; }
        /* Estilo para el historial previo */
        .historial-previa { 
            background: #050a10; padding: 15px; border-radius: 10px; font-size: 12px; 
            color: #888; margin-bottom: 20px; max-height: 120px; overflow-y: auto; 
            white-space: pre-wrap; border: 1px solid #1a2433; line-height: 1.4;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>GESTIONAR TICKET</h2>
    
    <?php if (!$ticket): ?>
    <form action="" method="POST">
        <label>Escribe el Folio del equipo</label>
        <input type="text" name="folio_input" placeholder="Ej: 5" required autofocus>
        <button type="submit" name="buscar_folio" class="btn-save">BUSCAR TICKET</button>
    </form>
    <?php else: ?>

    <div class="info-box">
        Folio: <b>FIME-<?php echo str_pad($id, 3, "0", STR_PAD_LEFT); ?></b><br>
        Cliente: <b><?php echo htmlspecialchars($ticket['nombre_cliente'] ?? 'Cargando...'); ?></b>
    </div>

    <form action="" method="POST">
        <input type="hidden" name="id_ticket_update" value="<?php echo $id; ?>">
        
        <label>Asignar Técnico Responsable</label>
        <select name="tecnico_asignado" required>
            <option value="">-- Selecciona un técnico --</option>
            <?php
            // Lista de técnicos desde la tabla de MySQL
            $query_tecnicos = mysqli_query($conexion, "SELECT nombre_tecnico FROM tecnicos ORDER BY nombre_tecnico ASC");
            while($tec = mysqli_fetch_assoc($query_tecnicos)) {
                $selected = ($ticket['tecnico'] == $tec['nombre_tecnico']) ? 'selected' : '';
                echo "<option value='".htmlspecialchars($tec['nombre_tecnico'])."' $selected>".htmlspecialchars($tec['nombre_tecnico'])."</option>";
            }
            ?>
        </select>

        <label>Estatus del Servicio</label>
        <select name="nuevo_estatus">
            <option value="Recibido" <?php if(($ticket['estatus']??"")=='Recibido') echo 'selected'; ?>>Recibido</option>
            <option value="En proceso" <?php if(($ticket['estatus']??"")=='En proceso') echo 'selected'; ?>>En proceso</option>
            <option value="Finalizado" <?php if(($ticket['estatus']??"")=='Finalizado') echo 'selected'; ?>>Finalizado</option>
        </select>

        <label>Notas Internas (Solo Técnicos)</label>
        <textarea name="descripcion_avance" placeholder="Ej: Falla en soldadura..."><?php echo htmlspecialchars($ticket['diagnostico'] ?? ''); ?></textarea>

        <label class="label-cliente">Añadir Nuevo Avance (Para el Cliente)</label>
        <textarea name="avances_cliente" placeholder="Escribe aquí el avance de hoy..."></textarea>
        
        <label style="color:#444;">Historial de seguimiento:</label>
        <div class="historial-previa"><?php echo htmlspecialchars($ticket['avances'] ?? 'Sin historial registrado.'); ?></div>

        <button type="submit" class="btn-save">GUARDAR CAMBIOS</button>
    </form>
    <a href="actualizar_ticket.php" style="color:#666; font-size:12px; display:block; text-align:center; margin-top:10px; text-decoration:none;">Buscar otro folio</a>
    <?php endif; ?>

    <a href="index.php" class="back-link">← VOLVER AL PANEL PRINCIPAL</a>
</div>

</body>
</html>