
<?php
    session_start();

    if($_SESSION["rol"] != 1){
        header("location: ../");
    }
    
    require "../conexion.php";

    if(!empty($_POST)){
        $alert = "";
        if(empty($_POST["nombre"]) || empty($_POST["correo"]) || empty($_POST["usuario"]) || empty($_POST["clave"]) || empty($_POST["rol"])){
            $alert = "<p class='msg_error'>Todos los campos son obligatorios</p>";
        }else{
            $nombre = $_POST["nombre"];
            $correo = $_POST["correo"];
            $usuario = $_POST["usuario"];
            $clave = md5($_POST["clave"]);
            $rol = $_POST["rol"];

            $query = mysqli_query($conection, "SELECT * FROM usuario WHERE usuario = '$usuario' OR correo = '$correo';");
            
            $result = mysqli_fetch_array($query);

            if($result > 0){
                $alert = "<p class='msg_error'>El correo o el usuario ya existe</p>";
            }else{
                
                require "../conexion.php";

                $query_insert = mysqli_query($conection, "INSERT INTO usuario(nombre, correo, usuario, clave, rol) VALUES('$nombre', '$correo', '$usuario', '$clave', '$rol')");

                if($query_insert){
                    $alert = "<p class='msg_save'>Usuario creado correctamente</p>";
                 
                }else{
                    $alert = "<p class='msg_error'>Error al crear el usuario</p>";
                    
                }
            }

            mysqli_close($conection);
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
    <title>Registro Usuario</title>
</head>

<body>
    <!-- Encabezado -->
    <?php include "includes/header.php"; ?>

    <!-- Menu de navegacion -->
    <?php include "includes/nav.php"?>

    <!-- Contenido de la pagina -->
    <section class="container">
        <h1><i class="fa-solid fa-user-plus" style="color: #74C0FC;"></i> Registro Usuario</h1>
        <hr>
        <div class="alert"><?php echo(isset($alert) ? $alert : '');?></div>
        <form action="" method="post" class="registrar">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" placeholder="Nombre completo">

            <label for="correo">Correo</label>
            <input type="email" name="correo" id="correo" placeholder="Correo electrònico">

            <label for="usuario">Usuario</label>
            <input type="text" name="usuario" id="usuario" placeholder="Usuario">

            <label for="clave">Contraseña</label>
            <input type="password" name="clave" id="clave" placeholder="Clave de acceso">

          

            <label for="rol">Tipo Usuario</label>

            <?php
            
                require "../conexion.php";

                $query_rol = mysqli_query($conection, "SELECT * FROM rol");

                $result_rol = mysqli_num_rows($query_rol);

                mysqli_close($conection);
            ?>
            <select name="rol" id="rol">
                <?php 
                    if($result_rol > 0){
                        while($rol = mysqli_fetch_array($query_rol)){ 
                ?>

                <option value="<?php echo($rol['idrol']);?>"> <?php echo($rol['rol']);?> </option>

                <?php
                        }
                    }
                ?>
            </select>
            <button type="submit" class="btn_save"><i class="fa-solid fa-floppy-disk" style="color: #ffffff;"></i> Crear Usuario</button>
          
        </form>
    </section>
    <!-- Footer -->
    <?php include "includes/footer.php"?>


</body>

</html>