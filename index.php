<?php
// 1. INICIO DE SESIÓN Y CONEXIÓN (Indispensable para Railway)
session_start();
include('conexion.php');

// Si el usuario no ha entrado, lo mandamos al login para que no vea errores
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// 2. TU LÓGICA DE BLINDAJE (Tal cual me la pasaste)
$rol = isset($_SESSION['rol']) ? trim(strtolower($_SESSION['rol'])) : 'cliente';
$usuario = isset($_SESSION['usuario']) ? trim($_SESSION['usuario']) : 'Invitado';

if (strtolower($usuario) === 'tecnico') {
    $rol = 'tecnico';
} elseif (strtolower($usuario) === 'cliente') {
    $rol = 'cliente';
}

// 3. TUS CONTADORES DE TICKETS
$en_proceso = 0; $listos = 0;
if ($rol !== 'cliente') {
    $res_p = mysqli_query($conexion, "SELECT COUNT(*) as total FROM tickets WHERE estatus = 'En proceso'");
    if($res_p) { $en_proceso = mysqli_fetch_assoc($res_p)['total']; }
    $res_f = mysqli_query($conexion, "SELECT COUNT(*) as total FROM tickets WHERE estatus = 'Finalizado'");
    if($res_f) { $listos = mysqli_fetch_assoc($res_f)['total']; }
}

// 4. TU BUSCADOR DE FOLIOS
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Systems Engineering - FIME</title>
    <style>
        /* TUS ESTILOS ORIGINALES */
        body { background: #000812; color: white; font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; height: 100vh; overflow: hidden; }
        .sidebar { width: 280px; background: #0a111a; border-right: 1px solid #1a2433; padding: 25px; box-sizing: border-box; display: flex; flex-direction: column; }
        .nav-item { display: block; padding: 12px 15px; margin-bottom: 5px; color: #ccc; text-decoration: none; border-radius: 10px; font-size: 14px; transition: 0.3s; }
        .nav-item:hover { background: #007bff; color: white; }
        .main { flex: 1; padding: 40px; overflow-y: auto; background: #000812; }
        .stats-container { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat-card { background: #0a111a; padding: 25px; border-radius: 20px; border: 1px solid #1a2433; flex: 1; border-left: 4px solid #007bff; }
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
        <?php endif; ?>

        <?php if($rol === 'admin' || $rol === 'tecnico'): ?>
            <div style="color:#444; font-size:10px; font-weight:bold; margin:20px 0 10px;">OPERACIONES</div>
            <a href="consultar_ticket.php" class="nav-item">📝 Actualizar Tickets</a>
            <a href="nuevo_ticket.php" class="nav-item">➕ Nuevo Servicio</a>
        <?php endif; ?>
        
        <div style="flex-grow: 1;"></div>
        <a href="logout.php" class="nav-item" style="color:#ff4d4d;">🚪 Cerrar Sesión</a>
    </div>

    <div class="main">
        <h1>Bienvenido, <?php echo htmlspecialchars($usuario); ?></h1>

        <?php if($rol === 'cliente'): ?>
            <div class="historia-texto">
                <h3>Nuestra Historia</h3>
                <p>En <b>Systems Engineering FIME</b>, nos dedicamos a ofrecer soluciones técnicas de alta calidad.</p>
            </div>
        <?php endif; ?>
        
        <?php if($rol !== 'cliente'): ?>
            <div class="stats-container">
                <div class="stat-card"><h3>En Proceso</h3><p><?php echo $en_proceso; ?></p></div>
                <div class="stat-card"><h3>Finalizados</h3><p><?php echo $listos; ?></p></div>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h3>🔍 Consultar Equipo</h3>
            <form action="index.php" method="POST">
                <input type="text" name="folio_cliente" placeholder="Ingresa folio" style="width:100%; padding:14px; background:#161f2b; border:1px solid #333; color:white; border-radius:10px; margin-bottom:15px; box-sizing:border-box; outline:none;">
                <button type="submit" name="consultar_folio" class="btn-blue">BUSCAR</button>
            </form>

            <?php if($resultado): ?>
                <div style="background: rgba(0, 123, 255, 0.1); padding:20px; border-radius:15px; margin-top:15px; border-left:4px solid #007bff;">
                    <p><b>Estatus:</b> <?php echo $resultado['estatus']; ?></p>
                    <p><b>Técnico:</b> <?php echo htmlspecialchars($resultado['tecnico'] ?? 'Asignando...'); ?></p>
                    <p><b>Avances:</b> <?php echo nl2br(htmlspecialchars($resultado['avances'])); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
