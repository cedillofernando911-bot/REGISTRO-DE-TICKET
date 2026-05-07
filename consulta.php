<?php
include('conexion.php');
$resultado = null;

if (isset($_POST['folio']) && isset($_POST['telefono'])) {
    // Extraemos solo el número por si el cliente escribe letras
    $folio = (int)filter_var($_POST['folio'], FILTER_SANITIZE_NUMBER_INT);
    $tel = mysqli_real_escape_string($conexion, $_POST['telefono']);

    // Buscamos el ticket y su diagnóstico en la tabla de seguimiento
    $sql = "SELECT t.*, s.comentario as diagnostico 
            FROM tickets t 
            LEFT JOIN seguimiento s ON t.id_ticket = s.id_ticket 
            WHERE t.id_ticket = '$folio' AND t.telefono = '$tel' 
            LIMIT 1";
    $resultado = mysqli_query($conexion, $sql);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta tu Equipo | FIME</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 24px; box-shadow: 0 20px 25px rgba(0,0,0,0.1); width: 100%; max-width: 420px; text-align: center; }
        h2 { color: #004d40; margin-bottom: 25px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 10px; box-sizing: border-box; }
        button { background: #b8860b; color: white; border: none; padding: 15px; width: 100%; border-radius: 10px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        button:hover { background: #966d08; }
        .status-tag { background: #fff3e0; color: #e65100; padding: 8px 15px; border-radius: 15px; display: inline-block; margin-top: 15px; font-weight: bold; font-size: 14px; }
        .diag-box { background: #e8f5e9; padding: 20px; border-radius: 12px; text-align: left; margin-top: 25px; border-left: 5px solid #004d40; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Consulta de Equipo</h2>
        <form method="POST">
            <input type="text" name="folio" placeholder="Número de Folio (Ej: 0007)" required>
            <input type="text" name="telefono" placeholder="Teléfono registrado" required>
            <button type="submit">VER DIAGNÓSTICO</button>
        </form>

        <?php if ($resultado && mysqli_num_rows($resultado) > 0): 
            $ticket = mysqli_fetch_array($resultado); ?>
            <div class="status-tag">Estatus: EN REVISIÓN</div>
            <div style="text-align: left; margin-top: 20px; font-size: 15px;">
                <p><strong>Folio:</strong> FIME-2026-<?php echo str_pad($ticket['id_ticket'], 4, "0", STR_PAD_LEFT); ?></p>
                <p><strong>Equipo:</strong> <?php echo $ticket['datos_equipo']; ?></p>
            </div>
            <div class="diag-box">
                <strong style="color: #004d40;">Diagnóstico Técnico:</strong><br>
                <p style="margin: 5px 0 0 0;"><?php echo $ticket['diagnostico'] ? $ticket['diagnostico'] : "El técnico está evaluando su equipo actualmente."; ?></p>
            </div>
        <?php elseif (isset($_POST['folio'])): ?>
            <p style="color: #d32f2f; margin-top: 25px; font-weight: bold;">Datos no encontrados. Verifique su folio y teléfono.</p>
        <?php endif; ?>
    </div>
</body>
</html>