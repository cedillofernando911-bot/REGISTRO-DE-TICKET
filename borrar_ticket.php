<?php
include('conexion.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // 1. Desactivamos las llaves foráneas temporalmente
    mysqli_query($conexion, "SET FOREIGN_KEY_CHECKS = 0");

    // 2. Borramos el ticket (esto ya no dará error)
    $sql = "DELETE FROM tickets WHERE id_ticket = $id";

    if(mysqli_query($conexion, $sql)){
        // 3. Volvemos a activar la seguridad de las llaves
        mysqli_query($conexion, "SET FOREIGN_KEY_CHECKS = 1");
        
        header("Location: consultar_ticket.php?msg=eliminado");
        exit();
    } else {
        echo "Error al eliminar: " . mysqli_error($conexion);
    }
}
?>