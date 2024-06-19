<?php

session_start();

if(!isset($_SESSION['usuario_id'])){
    header("location:includes/login.php");
    exit();
}

$insert = false;
$failed = false;

if($_SERVER["REQUEST_METHOD"]=="POST"){
    include '../config/db.php';

    $errores=array();

    $username = (isset($_POST['Username']))?$_POST['Username']:null;
    $password = (isset($_POST['password']))?$_POST['password']:null;
    $cargo = (isset($_POST['cargo']))?$_POST['cargo']:null;

    if(empty($username)){
        $errores['Username']="el username es obligatorio";
    }

    if(empty($password)){
        $errores['password']="el password es obligatorio";
    }

    if(empty($cargo)){
        $errores['cargo']="el cargo es obligatorio";
    }

    if(empty($errores)){

        $queryUser = "SELECT usuario FROM registro WHERE usuario = ?";
        $sentenciaQuery = $conex->prepare($queryUser);
        $sentenciaQuery->bind_param('s',$username);
        $sentenciaQuery->execute();
        $sentenciaQuery->store_result();

        if($sentenciaQuery->num_rows > 0){
            $failed = true;
            $sentenciaQuery->close();
        }else{
            $consulta = "INSERT INTO `registro`(`usuario`, `password`, `id_cargo`) VALUES (?,?,?)";
            $sentencia = $conex->prepare($consulta);
            $sentencia->bind_param('sss',$username,$password,$cargo);

            $sentencia->execute();

            $insert = true;
            $sentencia->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>agregar usuarios</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container-btn-volver">
        <a href="../index.php" class="btn-agregar-volver">inicio</a>
        <a href="./user_register.php" class="btn-agregar-volver">usuarios</a>
    </div>

    <main class="container-agregar-usuario">
        <div class="wrapper">
            <form action="add_user.php" method="post">
                <h2 class="title-registrar">Registrar usuarios</h2>
                <div class="input-box">
                    <input type="text" placeholder="Username" name="Username" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="password" placeholder="contraseña" name="password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <div class="input-box">
                    <input type="number" placeholder="1=administrador / 2=usuario" name="cargo" min="1" max="2" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <button type="submit" class="btn">Registrar</button>
            </form>
        </div>
        <?php if($failed){?>
            <div class="box-alert-failed">
                <p>El usuario ya existe, ingrese otro usuario</p>
            </div>
        <?php } ?>

        <?php if($insert){?>
            <div class="box-alert-success">
                <p>usuario registrado con exito</p>
            </div>
        <?php } ?>
    </main>
    
    <script src="../js/add_user.js"></script>
</body>
</html>