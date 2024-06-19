<?php

session_start();
$failed = false;

if($_SERVER["REQUEST_METHOD"]=="POST"){

    include '../config/db.php';

    $errores=array();

    $username = (isset($_POST['Username']))?$_POST['Username']:null;
    $password = (isset($_POST['password']))?$_POST['password']:null;

    if(empty($username)){
        $errores['Username']="el username es obligatorio";
    }

    if(empty($password)){
        $errores['password']="el password es obligatorio";
    }

    if(empty($errores)){

        $consulta = 'SELECT id,usuario,password,id_cargo FROM registro WHERE usuario = ? AND password = ?';
        $sentencia = $conex->prepare($consulta);
        $sentencia->bind_param('ss',$username,$password);

        $sentencia->execute();
        $sentencia->bind_result($db_id, $db_username, $db_password, $db_id_cargo);
        $login = false;

        while($sentencia->fetch()){
            if($password == $db_password) {
                $_SESSION['usuario_id']=$db_id;
                $_SESSION['usuario_nombre']=$db_username;
                $_SESSION['usuario_cargo'] = $db_id_cargo;
                $login=true;
            }
        }

        $sentencia->close();

        if($login){
            header('location:../index.php');
        }else{
            $failed=true;
        }
    }else{
        $failed = true;
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <main class="container-login">
        <a class="icon" tabindex="0" id="popoverButton" role="button" data-bs-toggle="popover" 
        data-bs-trigger="focus" data-bs-title="Usuarios" 
        data-bs-content="Administrador:<br>username: admin<br>contraseña: 123<br>Usuario:<br>username: user<br>contraseña: 123">
            Consultar cuentas activas  <i class="bi bi-exclamation-circle icon-warning"></i>
        </a>
        <div class="wrapper">
            <form action="login.php" method="post">
                <div class="container-logo-login">
                    <img src="../assets/private_logo.png" alt="logo">
                </div>
                <h2 class="title-login">Inicia Sesión en Private</h2>
                <div class="input-box">
                    <input type="text" placeholder="Username" name="Username" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="password" placeholder="contraseña" name="password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <button type="submit" class="btn">Iniciar sesión</button>
            </form>
        </div>
        <?php if($failed){?>
        <div class="box-alert">
            <p>lo sentimos, no pudimos encontrar tu cuenta</p>
        </div>
        <?php } ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const popoverTrigger = document.querySelector('[data-bs-toggle="popover"]');
        const popover = new bootstrap.Popover(popoverTrigger, {
            html: true,
            template: '<div class="popover custom-popover" role="tooltip"><div class="popover-arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>'
        });
    });
    </script>
</body>
</html>