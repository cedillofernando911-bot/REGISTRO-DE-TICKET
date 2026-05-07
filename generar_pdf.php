<?php
include('conexion.php');
session_start();

// 1. Validar que el usuario esté logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// 2. Obtener el ID y protegerlo
$id = isset($_GET['id']) ? mysqli_real_escape_string($conexion, $_GET['id']) : "";

if ($id == "") {
    die("Error: No se proporcionó un ID de folio.");
}

// 3. Consultar los datos (Usamos ?? para evitar errores de valores nulos)
$consulta = mysqli_query($conexion, "SELECT * FROM tickets WHERE id_ticket = '$id'");
$ticket = mysqli_fetch_assoc($consulta);

if (!$ticket) {
    die("Error: El folio FIME-$id no existe en la base de datos.");
}

// Formatear Folio
$folio_fime = "FIME-" . str_pad($ticket['id_ticket'], 3, "0", STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante <?php echo $folio_fime; ?></title>
    <style>
        body { font-family: 'Arial', sans-serif; background: white; color: #333; padding: 40px; margin: 0; }
        .ticket-container { border: 2px solid #007bff; padding: 30px; border-radius: 15px; max-width: 800px; margin: auto; }
        .header { border-bottom: 2px solid #007bff; padding-bottom: 15px; margin-bottom: 20px; text-align: center; }
        .folio { float: right; color: #007bff; font-weight: bold; font-size: 20px; }
        h1 { margin: 0; color: #007bff; font-size: 28px; }
        .label { font-weight: bold; color: #666; font-size: 12px; text-transform: uppercase; margin-top: 15px; }
        .value { font-size: 18px; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .box { background: #f8f9fa; border: 1px solid #ddd; padding: 15px; border-radius: 8px; margin-top: 5px; min-height: 60px; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; padding-top: 15px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="background: #007bff; color: white; border: none; padding: 10px 25px; border-radius: 8px; cursor: pointer; font-weight: bold;">
            🖨️ IMPRIMIR COMPROBANTE
        </button>
        <a href="index.php" style="margin-left: 15px; color: #666; text-decoration: none;">Ir al Inicio</a>
    </div>

    <div class="ticket-container">
        <div class="header">
            <span class="folio"><?php echo $folio_fime; ?></span>
            <h1>SYSTEMS ENGINEERING</h1>
            <p style="margin: 5px 0;">Soporte Técnico Especializado - FIME</p>
        </div>

        <div class="label">Cliente:</div>
        <div class="value"><?php echo htmlspecialchars($ticket['nombre_cliente'] ?? 'N/A'); ?></div>

        <div class="label">Equipo / Modelo:</div>
        <div class="value"><?php echo htmlspecialchars($ticket['datos_equipo'] ?? 'N/A'); ?></div>

        <div class="label">Estatus del Servicio:</div>
        <div class="value" style="color: #007bff;"><?php echo htmlspecialchars($ticket['estatus'] ?? 'Recibido'); ?></div>

        <div class