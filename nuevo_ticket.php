<?php
include('conexion.php');
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Servicio - Systems Engineering</title>
    <style>
        /* 1. Blindaje de fondo: Negro absoluto e infinito */
        html {
            background-color: #000812;
        }
        
        body { 
            background-color: #000812; 
            color: white; 
            font-family: 'Segoe UI', sans-serif; 
            margin: 0;
            padding: 40px 20px;
            display: block; /* Cambiamos de flex a block para evitar errores de centrado vertical */
        }

        /* 2. Caja centrada manualmente */
        .card { 
            background: #0a111a; 
            padding: 40px; 
            border-radius: 25px; 
            border: 1px solid #1a2433; 
            width: 100%;
            max-width: 600px; 
            box-shadow: 0 20px 50px rgba(0,0,0,0.9);
            margin: 0 auto; /* Centra horizontalmente */
            box-sizing: border-box;
        }

        h2 { color: #007bff; font-size: 24px; margin-bottom: 30px; text-align: center; }
        
        label { display: block; margin-bottom: 8px; color: #666; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        
        /* Filas para Teléfono y Dirección */
        .row { display: flex; gap: 15px; margin-bottom: 5px; }
        .col { flex: 1; }

        input, textarea { 
            width: 100%; 
            padding: 15px; 
            background: #1a2433; 
            border: 1px solid #333; 
            color: white; 
            border-radius: 12px; 
            font-size: 15px; 
            margin-bottom: 20px; 
            outline: none; 
            box-sizing: border-box;
        }

        input:focus, textarea:focus { border-color: #007bff; }
        
        textarea { height: 100px; resize: none; font-family: inherit; }
        
        /* Plan de diagnóstico */
        .diag-box { 
            border: 1px solid #004c99; 
            background: rgba(0, 76, 153, 0.05); 
            padding: 20px; 
            border-radius: 15px; 
            margin-bottom: 20px; 
        }

        .btn-save { 
            background: #007bff; 
            color: white; 
            border: none; 
            padding: 20px; 
            width: 100%; 
            border-radius: 15px; 
            font-weight: bold; 
            cursor: pointer; 
            font-size: 16px; 
            text-transform: uppercase;
        }
        
        .btn-back { display: block; text-align: center; margin-top: 25px; color: #444; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>

<div class="card">
    <h2>📝 Registro de Servicio</h2>
    
    <form action="guardar_ticket.php" method="POST">
        <label>Nombre del Cliente</label>
        <input type="text" name="nombre_cliente" placeholder="Nombre completo" required>

        <div class="row">
            <div class="col">
                <label>Teléfono</label>
                <input type="text" name="telefono" placeholder="81...">
            </div>
            <div class="col">
                <label>Dirección</label>
                <input type="text" name="direccion" placeholder="Colonia/Calle">
            </div>
        </div>

        <label>Equipo y Modelo</label>
        <input type="text" name="datos_equipo" placeholder="Ej: Laptop Dell, Xbox..." required>

        <div class="diag-box">
            <label style="color: #007bff;">🔬 Plan de Diagnóstico</label>
            <textarea name="diagnostico" placeholder="¿Qué pruebas vas a realizar?"></textarea>
        </div>

        <label>Falla Reportada</label>
        <textarea name="falla" placeholder="¿Qué reporta el cliente?"></textarea>

        <button type="submit" class="btn-save">REGISTRAR Y GUARDAR TICKET</button>
        
        <a href="index.php" class="btn-back">← CANCELAR Y VOLVER</a>
    </form>
</div>

</body>
</html>