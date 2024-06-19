<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("location:../includes/login.php");
    exit();
}

include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];
    $consulta_fijar = "SELECT fijado FROM posteos_user WHERE id = $post_id";
    $resultado_fijar = mysqli_query($conex, $consulta_fijar);
    $post = mysqli_fetch_assoc($resultado_fijar);
    $nuevo_estado = $post['fijado'] ? 0 : 1;

    $consulta_actualizar = "UPDATE posteos_user SET fijado = $nuevo_estado WHERE id = $post_id";
    if (mysqli_query($conex, $consulta_actualizar)) {
        echo json_encode(['exito' => true, 'fijado' => $nuevo_estado]);
    } else {
        echo json_encode(['exito' => false, 'error' => 'No se pudo actualizar el estado del post']);
    }
}
?>
