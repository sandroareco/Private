<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("location:../includes/login.php");
    exit();
}

include '../config/db.php';

// Obtener el id del post a eliminar
$post_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($post_id === null) {
    echo json_encode(['error' => 'No se proporcionó el ID del post.']);
    exit();
}

// Obtener el usuario actual de la sesión y su cargo
$usuario_id_actual = $_SESSION['usuario_id'];
$id_cargo_actual = $_SESSION['usuario_cargo'];

// Consulta para obtener el usuario que creó el post y su cargo
$consulta_post = "SELECT usuario, id_cargo FROM posteos_user WHERE id = ?";

$stmt = $conex->prepare($consulta_post);
$stmt->bind_param('i', $post_id);
$stmt->execute();
$stmt->bind_result($post_usuario, $post_cargo);
$stmt->fetch();
$stmt->close();

// Verificar si el usuario actual tiene permisos para eliminar el post
if ($id_cargo_actual == 1 || ($id_cargo_actual == 2 && $post_usuario == $usuario_id_actual)) {
    // Eliminar el post de la base de datos
    $consulta_eliminar = "DELETE FROM posteos_user WHERE id = ?";

    $stmt = $conex->prepare($consulta_eliminar);
    $stmt->bind_param('i', $post_id);
    $stmt->execute();
    $stmt->close();

    header("Location: ../index.php");
    exit();
} else {
    echo json_encode(['error' => 'No tienes permisos para eliminar este post.']);
}

$conex->close();
?>
