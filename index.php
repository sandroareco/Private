<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("location:includes/login.php");
    exit();
}

$usuario_nombre = $_SESSION['usuario_nombre'];
$usuario_id = $_SESSION['usuario_id'];
$usuario_cargo = $_SESSION['usuario_cargo'];

include 'config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errores = array();
    $posteo = isset($_POST['posteo']) ? $_POST['posteo'] : null;

    if (empty($posteo)) {
        $errores['posteo'] = "El posteo no se puede enviar vacío";
    }
    
    if (empty($errores)) {
        $consulta = "INSERT INTO posteos_user(`usuario`, `posteos`, `id_cargo`) VALUES ('$usuario_id', '$posteo', '$usuario_cargo')";
        $resultado = mysqli_query($conex, $consulta);
    }
}

// Recuperar los posts que el usuario ha dado "Me gusta"
$consulta_likes = "SELECT posteo_id FROM likes WHERE usuario_id = '$usuario_id'";
$resultado_likes = mysqli_query($conex, $consulta_likes);
$liked_posts = array();

while ($row = mysqli_fetch_assoc($resultado_likes)) {
    $liked_posts[] = $row['posteo_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>inicio</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container-header">
            <h2 class="title-user">@<?php echo htmlspecialchars($usuario_nombre); ?></h2>
            <div class="wrapper-btn-cerrar-sesion">
            <?php if ($usuario_cargo == 1) { ?>
                <a href="includes/user_register.php" class="btn-usuarios">Usuarios</a>
            <?php } ?>
                <a href="includes/logout.php" class="btn-cerrar-sesion">Cerrar sesión</a>
            </div>
        </div>
    </header>
    <main>
        <form action="index.php" method="post">
            <div class="wrapper-input-post">
                <div class="input-post-box">
                    <input type="text" placeholder="Ingresa tu idea o tarea..." name="posteo" minlength="1" maxlength="255" required>
                    <input type="hidden" name="post_id" id="post_id">
                <div class="container-btn-postear">
                    <button type="submit" class="btn-postear">Postear</button>
                </div>
            </div>
        </form>


        <!-- Posteos -->
        <?php
            $consulta_post = "SELECT posteos_user.id, 
            registro.usuario, 
            posteos_user.posteos, 
            posteos_user.created_at,
            posteos_user.usuario as post_usuario,
            posteos_user.likes,
            posteos_user.fijado
            FROM posteos_user
            INNER JOIN registro ON posteos_user.usuario = registro.id
            ORDER BY posteos_user.fijado DESC, posteos_user.created_at DESC"; // Ordenar por fecha de posteo descendente

            $dato = mysqli_query($conex, $consulta_post);

            if ($dato && $dato->num_rows > 0) {
                while ($fila = mysqli_fetch_array($dato)) {
                    $isLiked = in_array($fila['id'], $liked_posts); 
                    ?>
                    <div class="container-post">
                        <div class="container-post-info">
                            <div class="container-header-post">
                                <p class="post_user">@<?php echo htmlspecialchars($fila['usuario']); ?></p>
                                <p class="post_date"><?php echo htmlspecialchars($fila['created_at']); ?></p>
                            </div>
                            <p class="post_text"><?php echo htmlspecialchars($fila['posteos']); ?></p>
                            <!-- Dentro del bucle de publicaciones -->
                            <div class="container-post-action">
                                <a href="includes/response.php?id=<?php echo htmlspecialchars($fila['id']); ?>"><i class="bi bi-chat"></i></a>
                                <a href="#" class="like_icon" onclick="likePost(<?php echo $fila['id']; ?>, this.querySelector('i')); return false;">
                                    <i class="bi bi-heart-fill <?php echo $isLiked ? 'liked' : ''; ?>"></i>
                                    <span class="cont_like <?php echo ($fila['likes'] > 0) ? 'visible' : ''; ?>" id="likes_count_<?php echo $fila['id']; ?>"><?php echo isset($fila['likes']) ? $fila['likes'] : 0; ?></span>
                                </a>
                                <?php if ($usuario_cargo == 2 && $fila['fijado'] == 1) { ?>
                                    <span class="fijado-text">fijado</span>
                                <?php } ?>
                                <?php if ($usuario_cargo == 1 || $usuario_id == $fila['post_usuario']) { ?>
                                    <button type="button" class="btn-eliminar-post delete-btn" data-id="<?php echo htmlspecialchars($fila['id']); ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <?php if ($usuario_cargo == 1) { ?>
                                    <button type="button" class="btn-fijar-post" onclick="fijarPost(<?php echo htmlspecialchars($fila['id']); ?>, this)">
                                        <i class="bi bi-pin <?php echo $fila['fijado'] ? 'pinned' : ''; ?>"></i>
                                    </button>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                        
                        <!-- Respuestas -->
                        <?php
                        $post_id = $fila['id'];
                        $consulta_respuesta = "
                            SELECT respuestas_post_user.id AS respuesta_id, registro.usuario AS usuario, respuestas_post_user.respuesta AS respuesta, respuestas_post_user.usuario_id AS respuesta_usuario
                            FROM respuestas_post_user
                            INNER JOIN registro ON respuestas_post_user.usuario_id = registro.id
                            WHERE respuestas_post_user.posteo_id = '$post_id' AND respuestas_post_user.parent_id IS NULL";

                        $dato_respuesta = mysqli_query($conex, $consulta_respuesta);

                        if ($dato_respuesta && $dato_respuesta->num_rows > 0) {
                            while ($fila_resp = mysqli_fetch_array($dato_respuesta)) { ?>
                                <div class="container-resp-info">
                                <?php
                                    $respuesta_id = $fila_resp['respuesta_id'];
                                    $consulta_respuesta_anidada = "
                                        SELECT COUNT(*) AS total_anidadas
                                        FROM respuestas_post_user
                                        WHERE parent_id = '$respuesta_id'";
                                    $resultado_anidado = mysqli_query($conex, $consulta_respuesta_anidada);
                                    $anidadas = mysqli_fetch_array($resultado_anidado);
                                    if ($anidadas['total_anidadas'] > 0) { ?>
                                        <div class="linea"></div>
                                    <?php } ?>

                                    <p class="post_user">@<?php echo htmlspecialchars($fila_resp['usuario']); ?></p>
                                    <p class="post_text"><?php echo htmlspecialchars($fila_resp['respuesta']); ?></p>
                                    <div class="container-post-action">
                                        <a href="includes/response.php?id=<?php echo htmlspecialchars($post_id); ?>&parent_id=<?php echo htmlspecialchars($fila_resp['respuesta_id']); ?>"><i class="bi bi-chat"></i></a>
                                        <?php if ($usuario_cargo == 1 || $usuario_id == $fila_resp['respuesta_usuario']) { ?>
                                            <button type="button" class="btn-eliminar-post delete-btn-response" data-id="<?php echo htmlspecialchars($fila_resp['respuesta_id']); ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php } ?>
                                    </div>
                                </div>
                                
                                <!-- Respuestas a respuestas -->
                                <?php
                                $respuesta_id = $fila_resp['respuesta_id'];
                                $consulta_respuesta_anidada = "
                                    SELECT respuestas_post_user.id AS respuesta_id, registro.usuario AS usuario, respuestas_post_user.respuesta AS respuesta, respuestas_post_user.usuario_id AS respuesta_usuario
                                    FROM respuestas_post_user
                                    INNER JOIN registro ON respuestas_post_user.usuario_id = registro.id
                                    WHERE respuestas_post_user.parent_id = '$respuesta_id'";

                                $dato_respuesta_anidada = mysqli_query($conex, $consulta_respuesta_anidada);

                                if ($dato_respuesta_anidada && $dato_respuesta_anidada->num_rows > 0) {
                                    while ($fila_resp_anidada = mysqli_fetch_array($dato_respuesta_anidada)) { ?>
                                        <div class="container-resp-info-nested">
                                            <p class="post_user">@<?php echo htmlspecialchars($fila_resp_anidada['usuario']); ?></p>
                                            <p class="post_text"><?php echo htmlspecialchars($fila_resp_anidada['respuesta']); ?></p>
                                            <div class="container-post-action">
                                                <?php if ($usuario_cargo == 1 || $usuario_id == $fila_resp_anidada['respuesta_usuario']) { ?>
                                                    <button type="button" class="btn-eliminar-post delete-btn-nested" data-id="<?php echo htmlspecialchars($fila_resp_anidada['respuesta_id']); ?>">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php }
                                }
                                ?>
                            <?php }
                        }
                        ?>
                    </div>
                <?php }
            }
        ?>
    </main>

    <?php
    include_once 'includes/footer.php';
    ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/delete_alert.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Inicializar la visibilidad de los contadores de "likes"
        document.querySelectorAll('.cont_like').forEach(span => {
            if (parseInt(span.textContent) > 0) {
                span.classList.add('visible');
            }
        });

        // Mover posts fijados a la posición correcta dentro del wrapper-input-post
        const wrapperInputPost = document.querySelector('.wrapper-input-post');
        const inputPostBox = document.querySelector('.input-post-box');
        document.querySelectorAll('.container-post.fijado').forEach(post => {
            wrapperInputPost.insertBefore(post, inputPostBox.nextSibling);
        });
    });

    function fijarPost(postId, button) {
        fetch('includes/fijar_post.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'post_id=' + postId
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al fijar el post');
            }
            return response.json();
        })
        .then(data => {
            if (data.exito) {
                const postContainer = button.closest('.container-post');
                const wrapperInputPost = document.querySelector('.wrapper-input-post');
                const inputPostBox = document.querySelector('.input-post-box');
                const allPosts = wrapperInputPost.querySelectorAll('.container-post');

                if (data.fijado) {
                    // Guardar la posición original del post
                    postContainer.setAttribute('data-original-position', Array.from(allPosts).indexOf(postContainer));

                    // Insertar el post fijado justo después de input-post-box
                    wrapperInputPost.insertBefore(postContainer, inputPostBox.nextSibling);
                    button.querySelector('i').classList.add('pinned');
                    postContainer.classList.add('fijado');
                } else {
                    // Quitar la clase fijado y restaurar la posición original
                    button.querySelector('i').classList.remove('pinned');
                    postContainer.classList.remove('fijado');

                    // Recuperar la posición original del post según el orden de la consulta SQL
                    const originalPosition = postContainer.getAttribute('data-original-position');
                    if (originalPosition !== null) {
                        const targetIndex = parseInt(originalPosition);
                        const referencePost = allPosts[targetIndex];

                        // Insertar en su posición original dentro del wrapper-input-post
                        if (referencePost && wrapperInputPost.contains(referencePost)) {
                            wrapperInputPost.insertBefore(postContainer, referencePost.nextSibling);
                        } else {
                            wrapperInputPost.appendChild(postContainer);
                        }
                    } else {
                        // Si no hay posición original guardada, insertar al final del contenedor
                        wrapperInputPost.appendChild(postContainer);
                    }
                }
            } else {
                throw new Error('Error al fijar el post: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message);
        });
    }

    function likePost(postId, icon) {
        fetch('includes/like.php?id=' + postId)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al dar Me gusta');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Actualizar el contador de Me gusta
                    let likesCount = document.getElementById('likes_count_' + postId);
                    likesCount.textContent = data.likes;

                    // Toggle de la clase liked en el ícono
                    if (data.isLiked) {
                        icon.classList.add('liked');
                    } else {
                        icon.classList.remove('liked');
                    }

                    // Mostrar u ocultar el contador basado en el valor
                    if (data.likes > 0) {
                        likesCount.classList.add('visible');
                    } else {
                        likesCount.classList.remove('visible');
                    }
                } else {
                    throw new Error('Error al dar Me gusta: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message);
            });
        }


</script>

</body>
</html>
