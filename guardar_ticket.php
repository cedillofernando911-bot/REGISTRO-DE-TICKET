<?php
include('conexion.php');
session_start();

// Detectamos si es una actualización de un ticket existente
if (isset($_POST['accion']) && $_POST['accion'] == 'actualizar') {
    $id = mysqli_real_escape_string($conexion, $_POST['id_ticket']);
    $estatus = mysqli_real_escape_string($conexion, $_POST['nuevo_estatus']);
    $notas = mysqli_real_escape_string($conexion, $_POST['descripcion_avance']);

    $sql = "UPDATE tickets SET estatus = '$estatus', diagnostico = '$notas' WHERE id_ticket = '$id'";

    if (mysqli_query($conexion, $sql)) {
        echo "<script>alert('¡Cambios guardados!'); window.location.href='consultar_ticket.php';</script>";
    } else {
        echo "Error al actualizar: " . mysqli_error($conexion);
    }
} else {
    // Si no es actualización, es un registro nuevo (tu código de antes)
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre_cliente']);
    $equipo = mysqli_real_escape_string($conexion, $_POST['datos_equipo']);
    $tel = isset($_POST['telefono']) ? $_POST['telefono'] : '';
    $falla = isset($_POST['falla']) ? $_POST['falla'] : '';
    
    $detalles = "Equipo: $equipo | Tel: $tel | Falla: $falla";
    $asunto = "Servicio FIME: $equipo";

    $sql = "INSERT INTO tickets (nombre_cliente, datos_equipo, estatus, asunto) 
            VALUES ('$nombre', '$detalles', 'Recibido', '$asunto')";

    if (mysqli_query($conexion, $sql)) {
        $id_nuevo = mysqli_insert_id($conexion);
        echo "<script>alert('¡Registro Exitoso!'); window.location.href='generar_pdf.php?id=$id_nuevo';</script>";
    } else {
        echo "Error al registrar: " . mysqli_error($conexion);
    }
}
?>