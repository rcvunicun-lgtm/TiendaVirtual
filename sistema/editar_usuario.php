
<?php
    session_start();

    if($_SESSION["rol"] != 1){
        header("location: ../");
    }

?>
<?php

    require "../conexion.php";

    if(!empty($_POST)){
        $alert = "";
        if(empty($_POST["nombre"]) || empty($_POST["correo"]) || empty($_POST["usuario"]) || empty($_POST["rol"])){
            $alert = "<p class='msg_error'>Todos los campos son obligatorios</p>";
        }else{

            $idUsuario = $_POST["id"];
            $nombre = $_POST["nombre"];
            $correo = $_POST["correo"];
            $usuario = $_POST["usuario"];
            $clave = md5($_POST["clave"]);
            $rol = $_POST["rol"];

            $query = mysqli_query($conection, "SELECT * FROM usuario WHERE (usuario = '$usuario' AND idUsuario != $idUsuario) OR (correo = '$correo' AND idUsuario != $idUsuario);");
            $result = mysqli_fetch_array($query);
            // $result = count($result); //En el curso de PHP si funciona per aca aparece un error.

            if($result > 0){
                $alert = "<p class='msg_error'>El correo o el usuario ya existe</p>";
            }else{

                if(empty($_POST["clave"])){
                    $sql_update = mysqli_query($conection,"UPDATE usuario SET nombre = '$nombre', correo = '$correo', usuario = '$usuario', rol = '$rol' WHERE idUsuario = $idUsuario;");
                }else{
                    $sql_update = mysqli_query($conection,"UPDATE usuario SET nombre = '$nombre', correo = '$correo', usuario = '$usuario', clave = '$clave', rol = '$rol' WHERE idUsuario = $idUsuario;");
                }
               
                if($sql_update){
                    $alert = "<p class='msg_save'>Usuario actualizado correctamente</p>";
                }else{
                    $alert = "<p class='msg_error'>Error al actualizar el usuario</p>";
                }
            }
        
        }
   
    }

    // Mostrar datos
    if(empty($_REQUEST['id'])){
        header('Location: lista_usuarios.php');
    }

    require "../conexion.php";
    $id_user = $_REQUEST['id'];
    $query = mysqli_query($conection, "SELECT usuario.idusuario, usuario.nombre, usuario.correo, usuario.usuario, usuario.rol, rol.rol as idRol FROM usuario INNER JOIN rol ON usuario.rol = rol.idrol WHERE usuario.idusuario = $id_user and estatus = 1;");
    $result = mysqli_num_rows($query);
    mysqli_close($conection);


    if($result == 0){
        header('Location: lista_usuarios.php');  
    }else{

        $option = '';

        while($data = mysqli_fetch_array($query)){
            $id_user = $data["idusuario"];
            $nombre = $data["nombre"];
            $correo = $data["correo"];
            $usuario = $data["usuario"];
            $id_rol = $data["rol"];
            $rol = $data["idRol"];
          

            if($id_rol == 1){
                $option = '<option value="'.$id_rol.'" select>'.$rol.'</option>';
            
            }else if($id_rol == 2){
                $option = '<option value="'.$id_rol.'" select>'.$rol.'</option>';
            }else if($id_rol == 3){
                $option = '<option value="'. $id_rol. '" select>'. $rol .'</option>';
               
            }
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
    <title>Editar Usuario</title>
</head>

<body>
    <!-- Encabezado -->
    <?php include "includes/header.php"; ?>

    <!-- Menu de navegacion -->
    <?php include "includes/nav.php"?>

    <!-- Contenido de la pagina -->
    <section class="container">
        <h1><i class="fa-solid fa-user-pen"></i> Editar Usuario</h1>
        <hr>
        <div class="alert"><?php echo(isset($alert) ? $alert : '');?></div>
        <form action="" method="post">

            <input type="hidden" name="id" value="<?php echo($id_user); ?>">

            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" placeholder="Nombre completo" value="<?php echo($nombre);?>">

            <label for="correo">Correo</label>
            <input type="email" name="correo" id="correo" placeholder="Correo electrònico" value="<?php echo($correo);?>">

            <label for="usuario">Usuario</label>
            <input type="text" name="usuario" id="usuario" placeholder="Usuario" value="<?php echo($usuario);?>">

            <label for="clave">Contraseña</label>
            <input type="password" name="clave" id="clave" placeholder="Clave de acceso">

          

            <label for="rol">Tipo Usuario</label>

            <?php
                require "../conexion.php";

                $query_rol = mysqli_query($conection, "SELECT * FROM rol");
           
                $result_rol = mysqli_num_rows($query_rol);

                mysqli_close($conection);
            ?>

            <!-- En css Evitamos mostrar el rol repetido con ayuda de la clase notItemOne -->
            <select name="rol" id="rol" class="notItemOne">
                <?php

                    echo ($option);
                    if($result_rol > 0){
                        
                        while($mirol = mysqli_fetch_array($query_rol)){ 
                            
                            // Evitamos mostrar el rol repetido
                            // if($mirol['rol'] == $rol ){
                            //     continue;
                            // }
                ?>

                <option value="<?php echo($mirol['idrol']);?>"> <?php echo($mirol['rol']);?> </option>

                <?php
                        }
                    }
                   
                ?>
            </select>

            <button type="submit" class="btn_save"><i class="fa-solid fa-floppy-disk" style="color: #ffffff;"></i> Editar Usuario</button>

        </form>
    </section>
    <!-- Footer -->
    <?php include "includes/footer.php"?>


</body>

</html>