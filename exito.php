<?php 
session_start();
if (!isset($_GET['id'])) { header("Location: consultar_ticket.php"); exit(); }
$id = $_GET['id']; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro Exitoso | FIME</title>
    <style>
        body { background: #000812; color: white; font-family: sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .card { text-align: center; background: rgba(255,255,255,0.05); padding: 50px; border-radius: 25px; border: 1px solid #007bff; backdrop-filter: blur(10px); }
        .btn-pdf { background: #e74c3c; color: white; padding: 15px 35px; text-decoration: none; border-radius: 12px; font-weight: bold; display: inline-block; margin-top: 25px; }
        .btn-pdf:hover { background: #c0392b; transform: scale(1.05); transition: 0.3s; }
    </style>
</head>
<body>
    <div class="card">
        <h1 style="color: #007bff;">✅ TICKET GUARDADO</h1>
        <p>Se ha generado el folio <strong>#00<?php echo $id; ?></strong> correctamente.</p>
        <a href="generar_pdf.php?id=<?php echo $id; ?>" class="btn-pdf" target="_blank">📄 DESCARGAR COMPROBANTE PDF</a>
        <br><br>
        <a href="consultar_ticket.php" style="color: #666; text-decoration: none;">Ir a la Bitácora General</a>
    </div>
</body>
</html>