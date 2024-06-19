<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

include '../config/db.php';

$usuario_id = $_SESSION['usuario_id'];
$post_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$post_id) {
    echo json_encode(['success' => false, 'message' => 'ID de publicación no válido']);
    exit;
}

// Verificar si el usuario ya dio Me gusta
$consulta = "SELECT COUNT(*) AS count FROM likes WHERE usuario_id = '$usuario_id' AND posteo_id = '$post_id'";
$resultado = mysqli_query($conex, $consulta);
$row = mysqli_fetch_assoc($resultado);
$count = $row['count'];

if ($count > 0) {
    // Ya dio Me gusta, eliminar el Me gusta
    $consulta = "DELETE FROM likes WHERE usuario_id = '$usuario_id' AND posteo_id = '$post_id'";
    mysqli_query($conex, $consulta);

    $isLiked = false;
} else {
    // No ha dado Me gusta, agregar el Me gusta
    $consulta = "INSERT INTO likes (usuario_id, posteo_id) VALUES ('$usuario_id', '$post_id')";
    mysqli_query($conex, $consulta);

    $isLiked = true;
}

// Actualizar el contador de Me gusta en posteos_user
$consulta_likes = "SELECT likes FROM posteos_user WHERE id = '$post_id'";
$resultado_likes = mysqli_query($conex, $consulta_likes);
$row_likes = mysqli_fetch_assoc($resultado_likes);
$likes = $row_likes['likes'];

if ($isLiked) {
    // Incrementar el contador de likes
    $likes++;
} else {
    // Decrementar el contador de likes
    $likes--;
}

// Actualizar el contador en la tabla posteos_user
$consulta_update = "UPDATE posteos_user SET likes = '$likes' WHERE id = '$post_id'";
mysqli_query($conex, $consulta_update);

echo json_encode(['success' => true, 'likes' => $likes, 'isLiked' => $isLiked]);
?>
