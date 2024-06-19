<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("location:../includes/login.php");
    exit();
}

$usuario_nombre = $_SESSION['usuario_nombre'];
$usuario_id = $_SESSION['usuario_id'];
$usuario_cargo = $_SESSION['usuario_cargo'];

$success = false;
$failed = false;

include '../config/db.php';

if (!isset($_GET['id'])) {
    echo "ID del posteo no especificado.";
    exit();
}

$posteo_id = intval($_GET['id']);
$parent_id = isset($_GET['parent_id']) ? intval($_GET['parent_id']) : null;

// Verificar que el posteo_id exista
$verificar_posteo = "SELECT id FROM posteos_user WHERE id = ?";
$stmt_verificar = $conex->prepare($verificar_posteo);
$stmt_verificar->bind_param("i", $posteo_id);
$stmt_verificar->execute();
$resultado_verificar = $stmt_verificar->get_result();

if ($resultado_verificar->num_rows == 0) {
    echo "El posteo no existe.";
    exit();
}
$stmt_verificar->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errores = array();

    $respuesta = isset($_POST['respuesta']) ? $_POST['respuesta'] : null;
    $posteo_id = isset($_POST['posteo_id']) ? intval($_POST['posteo_id']) : null;
    $parent_id = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : null;

    if (empty($respuesta)) {
        $errores['respuesta'] = "La respuesta no se puede enviar vacía";
    }

    if (empty($errores)) {
        $consulta = "INSERT INTO respuestas_post_user(`respuesta`, `id_cargo`, `posteo_id`, `usuario_id`, `parent_id`) VALUES (?, ?, ?, ?, ?)";
        $sentencia = $conex->prepare($consulta);
        
        // Manejar el caso de NULL en parent_id
        if ($parent_id === 0) {
            $parent_id = null;
        }
        
        $sentencia->bind_param("siiii", $respuesta, $usuario_cargo, $posteo_id, $usuario_id, $parent_id);
        $sentencia->execute();

        if ($sentencia->affected_rows > 0) {
            $success = true;
        } else {
            $failed = true;
        }

        $sentencia->close();
    } else {
        $failed = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respuestas</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<main class="container-login">

    <div class="container-btn-volver">
        <a href="../index.php" class="btn-agregar-volver">inicio</a>
    </div>

    <div class="wrapper">
        <form action="response.php?id=<?php echo $posteo_id; ?>&parent_id=<?php echo $parent_id; ?>" method="post">
            <h4 class="title-login">Responder a posteo</h4>
            <input type="hidden" name="posteo_id" value="<?php echo $posteo_id; ?>">
            <input type="hidden" name="parent_id" value="<?php echo $parent_id; ?>">
            <div class="input-box">
                <input type="text" name="respuesta" placeholder="Escribe tu respuesta" minlength="1" maxlength="255" required>
            </div>
            <button type="submit" class="btn">Responder</button>
        </form>
    </div>

    <?php if ($success) { ?>
        <div class="box-alert">
            <p class="success">Respuesta publicada con éxito.</p>
        </div>
    <?php } elseif ($failed) { ?>
        <div class="box-alert">
            <p class="error">Error al publicar la respuesta. Por favor, inténtalo de nuevo.</p>
        </div>
    <?php } ?>
</main>
</body>
</html>
