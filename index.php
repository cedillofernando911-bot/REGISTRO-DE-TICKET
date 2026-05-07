<?php
include('conexion.php');
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// BLINDAJE ESTRICTO: Limpieza y normalización
$rol = trim(strtolower($_SESSION['rol']));
$usuario = trim($_SESSION['usuario']);

/** * 🚨 PARCHE DE SEGURIDAD PARA EL EQUIPO:
 * Si el nombre de usuario es "tecnico", forzamos el rol a 'tecnico' 
 * para ocultar las funciones de administrador, sin importar lo que diga la DB.
 */
if (strtolower($usuario) === 'tecnico') {
    $rol = 'tecnico';
} elseif (strtolower($usuario) === 'cliente') {
    $rol = 'cliente';
}

// --- CONTADORES (ADMIN/TECNICO) ---
$en_proceso = 0; $listos = 0;
if ($rol !== 'cliente') {
    $res_p = mysqli_query($conexion, "SELECT COUNT(*) as total FROM tickets WHERE estatus = 'En proceso'");
    if($res_p) { $en_proceso = mysqli_fetch_assoc($res_p)['total']; }
    $res_f = mysqli_query($conexion, "SELECT COUNT(*) as total FROM tickets WHERE estatus = 'Finalizado'");
    if($res_f) { $listos = mysqli_fetch_assoc($res_f)['total']; }
}

// --- BUSCADOR DE FOLIOS ---
$resultado = null;
if (isset($_POST['consultar_folio'])) {
    $folio = mysqli_real_escape_string($conexion, $_POST['folio_cliente']);
    $id_limpio = filter_var($folio, FILTER_SANITIZE_NUMBER_INT);
    if ($id_limpio) {
        $res = mysqli_query($conexion, "SELECT * FROM tickets WHERE id_ticket = '$id_limpio'");
        if ($res && mysqli_num_rows($res) > 0) { $resultado = mysqli_fetch_assoc($res); }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Systems Engineering - FIME</title>
    <style>
        body { background: #000812; color: white; font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; height: 100vh; }
        .sidebar { width: 280px; background: #0a111a; border-right: 1px solid #1a2433; padding: 25px; box-sizing: border-box; }
        .nav-item { display: block; padding: 12px 15px; margin-bottom: 5px; color: #ccc; text-decoration: none; border-radius: 10px; font-size: 14px; transition: 0.3s; }
        .nav-item:hover { background: #007bff; color: white; }
        .main { flex: 1; padding: 40px; overflow-y: auto; background: #000812; }
        .stats-container { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat-card { background: #0a111a; padding: 25px; border-radius: 20px; border: 1px solid #1a2433; flex: 1; border-left: 4px solid #007bff; }
        .flex-row { display: flex; gap: 25px; flex-wrap: wrap; }
        .card { background: #0a111a; padding: 25px; border-radius: 20px; border: 1px solid #1a2433; flex: 1; min-width: 320px; }
        .btn-blue { background: #007bff; color: white; border: none; padding: 15px; width: 100%; border-radius: 12px; font-weight: bold; cursor: pointer; }
        .historia-texto { color: rgba(255,255,255,0.6); font-size: 14px; line-height: 1.6; margin-bottom: 30px; border-left: 2px solid #007bff; padding-left: 20px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2 style="color:#007bff; text-align:center; font-size:20px; margin-bottom:30px;">SYSTEMS ENG.</h2>
        <a href="index.php" class="nav-item">🏠 Inicio / Consultar</a>
        
        <?php if($rol === 'admin'): ?>
            <div style="color:#444; font-size:10px; font-weight:bold; margin:20px 0 10px;">ADMINISTRADOR</div>
            <a href="admin_usuarios.php" class="nav-item">👤 Gestionar Usuarios</a>
            <a href="actualizar_ticket.php" class="nav-item">🛠️ Asignar Técnicos</a>
            <a href="admin_tecnicos.php" class="nav-item">👨‍🔧 Directorio Técnicos</a>
        <?php endif; ?>

        <?php if($rol === 'admin' || $rol === 'tecnico'): ?>
            <div style="color:#444; font-size:10px; font-weight:bold; margin:20px 0 10px;">OPERACIONES</div>
            <a href="consultar_ticket.php" class="nav-item">📝 Actualizar Tickets</a>
            <a href="nuevo_ticket.php" class="nav-item">➕ Nuevo Servicio</a>
        <?php endif; ?>
        
        <a href="logout.php" class="nav-item" style="margin-top:50px; color:#666;">🚪 Cerrar Sesión</a>
    </div>

    <div class="main">
        <h1>Bienvenido, <?php echo htmlspecialchars($usuario); ?></h1>

        <?php if($rol === 'cliente'): ?>
            <div class="historia-texto">
                <h3 style="color: #fff; margin-top: 0;">Nuestra Historia</h3>
                <p>En <b>Systems Engineering FIME</b>, nos dedicamos a ofrecer soluciones técnicas de alta calidad. 
                Nacimos con el objetivo de facilitar el soporte técnico a la comunidad universitaria, 
                brindando transparencia y rapidez en cada servicio. Tu equipo está en las mejores manos.</p>
            </div>
        <?php endif; ?>
        
        <?php if($rol !== 'cliente'): ?>
            <div class="stats-container">
                <div class="stat-card"><h3>En Proceso</h3><p><?php echo $en_proceso; ?></p></div>
                <div class="stat-card"><h3>Listos</h3><p><?php echo $listos; ?></p></div>
            </div>
        <?php endif; ?>
        
        <div class="flex-row">
            <div class="card">
                <h3 style="color:#007bff; margin-top:0;">🔍 Consultar Mi Equipo</h3>
                <form action="index.php" method="POST">
                    <input type="text" name="folio_cliente" placeholder="Ej: 10" style="width:100%; padding:14px; background:#161f2b; border:1px solid #333; color:white; border-radius:10px; margin-bottom:15px; box-sizing:border-box; outline:none;">
                    <button type="submit" name="consultar_folio" class="btn-blue">BUSCAR AVANCES</button>
                </form>

                <?php if($resultado): ?>
                    <div style="background: rgba(0, 123, 255, 0.1); padding:20px; border-radius:15px; margin-top:15px; border-left:4px solid #007bff;">
                        <p style="margin: 5px 0;"><b>Estatus:</b> <span style="color:#007bff;"><?php echo $resultado['estatus']; ?></span></p>
                        <p style="margin: 5px 0;"><b>Técnico responsable:</b> <?php echo htmlspecialchars($resultado['tecnico'] ?? 'Asignando técnico...'); ?></p>
                        
                        <p style="margin: 15px 0 5px;"><b>Avances de tu equipo:</b></p>
                        <div style="background: #161f2b; padding:15px; border-radius:10px; font-size:14px; color:#fff; border: 1px solid #28a745; line-height: 1.5;">
                            <?php 
                                $mensaje_para_mostrar = ($resultado['avances'] != "") 
                                    ? $resultado['avances'] 
                                    : "Tu equipo está en revisión técnica. Pronto actualizaremos los avances aquí.";
                                
                                echo nl2br(htmlspecialchars($mensaje_para_mostrar)); 
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>