<?php
session_start();
include '../config/db.php';

if (isset($_SESSION['usuario_id']) && isset($_GET['id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $nested_response_id = $_GET['id'];

    // Verificar si el usuario tiene permiso para eliminar la respuesta anidada
    $consulta = "SELECT usuario_id FROM respuestas_post_user WHERE id = '$nested_response_id'";
    $resultado = mysqli_query($conex, $consulta);
    $fila = mysqli_fetch_array($resultado);

    if ($_SESSION['usuario_cargo'] == 1 || $fila['usuario_id'] == $usuario_id) {
        $consulta_delete = "DELETE FROM respuestas_post_user WHERE id = '$nested_response_id'";
        mysqli_query($conex, $consulta_delete);
    }
}

header("Location: ../index.php");
exit();
?>
