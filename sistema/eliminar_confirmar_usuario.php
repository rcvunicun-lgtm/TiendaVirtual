
<?php
    session_start();

    if($_SESSION["rol"] != 1){
        header("location: ../");
    }
?>

<?php
    include "../conexion.php";
    
    $msg_error = "";
    if(!empty($_POST)){

        if($_POST['idusuario'] == 1){
            header("location: lista_usuarios.php");
            mysqli_close($conection);
            exit;
        }

        $id_usuario = $_POST['idusuario'];

        // $query_delete = mysqli_query($conection,"DELETE FROM usuario WHERE idusuario = $id_usuario;");

        $query_delete = mysqli_query($conection,"UPDATE usuario SET estatus = 0 WHERE idusuario = $id_usuario;");
        mysqli_close($conection);

        if($query_delete){
            header("location: lista_usuarios.php");
        }else{
            $msg_error = "!Error al eliminar el usuario!";
        }

    }

    if(empty($_REQUEST['id']))
    {
        header("location: lista_usuarios.php");
        mysqli_close($conection);
    }
    else if($_REQUEST['id'] == 1){
        header("location: lista_usuarios.php");
        mysqli_close($conection);
    }else{
      
        $id_usuario = $_REQUEST['id'];

        $query = mysqli_query($conection,"SELECT usuario.nombre, usuario.usuario, rol.rol FROM usuario INNER JOIN rol on usuario.rol = rol.idrol WHERE usuario.idusuario = $id_usuario;");

        mysqli_close($conection);

        $result = mysqli_num_rows($query);

        if($result > 0){
          while($data = mysqli_fetch_array($query)){
            $nombre = $data["nombre"];
            $usuario = $data["usuario"];
            $rol = $data["rol"];
          }
        }else{
            header("location: lista_usuarios.php"); 
        }
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <!-- Incrustamos el archivo scripts.php -->
    <?php include "includes/scripts.php"?>
    <title>Eliminar Usuario</title>
</head>

<body>
    <!-- Encabezado -->
    <?php include "includes/header.php"; ?>

    <!-- Menu de navegacion -->
    <?php include "includes/nav.php"?>

    <!-- Contenido de la pagina -->
    <h1 class="titulo_eliminar"><i class="fa-solid fa-user-xmark"></i> Eliminar Usuario</h1>
    <section class="cont_eliminar_user">
        <h1>¿Estas seguro de eliminar este usuario?</h1>
        <div class = "cont_info_user">
            <span class="clave">Nombre: </span> <span class="valor"><?php echo($nombre);?></span>
            <br>
            <span class="clave">Usuario: </span> <span class="valor"><?php echo($usuario);?></span>
            <br>
            <span class="clave">Rol: </span> <span class="valor"><?php echo($rol);?></span>
        </div>

        <form method = "post" action="" class="form_eliminar">
            <a class="elim_bot" href="lista_usuarios.php"><i class="fa-solid fa-hand-point-left"></i>  Cancelar</a>
            <input type="hidden" name="idusuario" value="<?php echo($id_usuario);?>">
            <button type="submit" class="elim_bot"><i class="fa-solid fa-user-slash"></i> Eliminar</button>
        </form>
        <p class="elim_msg_err"><?php echo($msg_error);?></p>
    </section>

    <!-- Footer -->
    <?php include "includes/footer.php"?>


</body>

</html>