<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_cargo'] != 1) {
    // Solo los administradores pueden eliminar usuarios
    header("location:../index.php");
    exit();
}

include '../config/db.php';

if (isset($_GET['id'])) {
    $userId = intval($_GET['id']);

    // Asegurarse de que el ID del usuario no es el mismo que el ID del administrador actual
    if ($userId == $_SESSION['usuario_id']) {
        echo "No puedes eliminar tu propia cuenta.";
        exit();
    }

    // Eliminar el usuario de la base de datos
    $consulta = "DELETE FROM registro WHERE id = ?";
    $stmt = $conex->prepare($consulta);
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        header("location:user_register.php?msg=Usuario eliminado correctamente");
    } else {
        echo "Error al eliminar el usuario.";
    }

    $stmt->close();
} else {
    echo "ID de usuario no especificado.";
}
?>
