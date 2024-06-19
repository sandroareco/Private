<?php

session_start();

if(!isset($_SESSION['usuario_id'])){
    header("location:includes/login.php");
    exit();
}

include '../config/db.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>usuarios registrados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="user_register_body">
    <div class="container-md">
        <div class="container-btn-volver">
            <a href="../index.php" class="btn-agregar-volver">inicio</a>
        </div>

        <h3>Lista de usuarios</h3>
        <div class="box-agregar-usuario">
            <a href="./add_user.php" class="btn-agregar-usuarios">agregar usuarios</a>
        </div>

        <div class="table-responsive">
            <table class="table table-dark">
            <thead>
                <tr>
                    <th scope="col">id</th>
                    <th scope="col">usuario</th>
                    <th scope="col">password</th>
                    <th scope="col">cargo</th>
                    <th scope="col">editar</th>
                    <th scope="col">eliminar</th>
                </tr>
            </thead>
            <tbody>

            <?php 
            $consulta = "SELECT registro.id AS registro_id,usuario,password,cargo.descripcion AS cargo FROM registro INNER JOIN cargo ON registro.id_cargo = cargo.id";
            $dato = mysqli_query($conex,$consulta);


            if($dato -> num_rows > 0){
                while($fila=mysqli_fetch_array($dato)){?>
                <tr>
                    <th scope="row"><?php echo $fila['registro_id'];?></th>
                    <td><?php echo $fila['usuario'];?></td>
                    <td><?php echo $fila['password'];?></td>
                    <td><?php echo $fila['cargo'];?></td>
                    <td>
                        <a class="btn btn-primary" href="edit.php?id=<?php echo $fila['registro_id'];?>">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                    </td>
                    <td>
                        <button class="btn btn-danger delete-btn" data-id="<?php echo $fila['registro_id']; ?>">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                Swal.fire({
                    title: "¿Estás seguro?",
                    text: "¡No podrás revertir esto!",
                    icon: "error",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Sí, elimínalo!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `delete_user.php?id=${userId}`;
                    }
                });
            });
        });
    </script>
</body>
</html>