<?php

session_start();

if(!isset($_SESSION['usuario_id'])){
    header("location:includes/login.php");
    exit();
}

$failed = false;
$usuario = ['id' => '', 'usuario' => '', 'password' => '', 'id_cargo' => '']; // Inicializar con valores predeterminados

include '../config/db.php';

if(isset($_POST['enviar'])){

    $id= $_POST['id'];
    $username = $_POST['Username'];
    $password = $_POST['password'];
    $id_cargo = $_POST['cargo'];

    $queryUser = "SELECT id FROM registro WHERE usuario = ? AND id != ?";
    $sentenciaQuery = $conex->prepare($queryUser);
    $sentenciaQuery->bind_param('si',$username,$id);
    $sentenciaQuery->execute();
    $sentenciaQuery->store_result();

    if($sentenciaQuery->num_rows > 0){
        $failed = true;
        $sentenciaQuery->close();

        // Mantener los valores del formulario
        $usuario = ['id' => $id, 'usuario' => $username, 'password' => $password, 'id_cargo' => $id_cargo];
    }else{
        $query = "UPDATE `registro` SET `usuario`=?, `password`=?, `id_cargo`=? WHERE id=?";
        $sentencia = $conex->prepare($query);
        $sentencia->bind_param('sssi', $username, $password, $id_cargo, $id);

        if($sentencia->execute()){
            echo "<script>
            alert('los datos se actualizaron correctamente');
            location.assign('./user_register.php');
            </script>";
        }else{
            echo "<script>
            alert('los datos NO se actualizaron');
            location.assign('./user_register.php');
            </script>";
        }

        $sentencia->close();
        $conex->close();
    }
}else{
    $id = $_GET['id'];
    $consulta = "SELECT id, usuario, password, id_cargo FROM registro WHERE id = ?";
    $sentencia = $conex->prepare($consulta);
    $sentencia->bind_param('i', $id);
    $sentencia->execute();
    $resultado = $sentencia->get_result();
    $usuario = $resultado->fetch_assoc();

    $sentencia->close();
    $conex->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container-btn-volver">
        <a href="../index.php" class="btn-agregar-volver">inicio</a>
        <a href="./user_register.php" class="btn-usuarios-edit">usuarios</a>
    </div>

    <main class="container-agregar-usuario">
        <div class="wrapper">
            <form action="edit.php" method="post">
                <h2 class="title-edit">Editar usuarios</h2>
                <input type="hidden" placeholder="id" name="id" value="<?php echo $usuario['id'];?>">
                <div class="input-box">
                    <input type="text" placeholder="Username" name="Username" value="<?php echo $usuario['usuario'];?>" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="password" placeholder="contraseña" name="password" value="<?php echo $usuario['password'];?>" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <div class="input-box">
                    <input type="number" placeholder="1=administrador / 2=usuario" name="cargo" min="1" max="2" value="<?php echo $usuario['id_cargo'];?>" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <button type="submit" name="enviar" class="btn">Editar</button>
            </form>
        </div>

        <?php if($failed){?>
            <div class="box-alert-failed">
                <p>El usuario ya existe, ingrese otro usuario</p>
            </div>
        <?php } ?>

    </main>
</body>
</html>