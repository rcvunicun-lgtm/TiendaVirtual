
<?php
    session_start();

    // if($_SESSION["rol"] != 1){
    //     header("location: ../");
    // }
    
    require "../conexion.php";

    if(!empty($_POST)){
        $alert = "";
        if(empty($_POST["nombre"]) || empty($_POST["telefono"]) || empty($_POST["direccion"])){
            $alert = "<p class='msg_error'>Todos los campos son obligatorios</p>";
        }else{

            $nit = $_POST["nit"];
            $nombre = $_POST["nombre"];
            $telefono = $_POST["telefono"];
            $direccion = $_POST["direccion"];
            $usuario_id = $_SESSION["idUser"];
            
            $result = 0;

            if(is_numeric($nit) && $nit != 0){
                $query = mysqli_query($conection, "SELECT * FROM cliente WHERE nit = '$nit';");
                $result = mysqli_fetch_array($query);
            }

            if($result > 0){
                $alert = "<p class='msg_error'>El Numero de NIT ya existe.</p>";
            }else{
                $query_insert = mysqli_query($conection, "INSERT INTO cliente(nit, nombre, telefono, direccion, usuario_id) VALUES('$nit', '$nombre', $telefono, '$direccion', $usuario_id)");
                
                if($query_insert){
                    $alert = "<p class='msg_save'>Cliente guardado correctamente</p>";
                 
                }else{
                    $alert = "<p class='msg_error'>Error al guardar el cliente</p>";
                    
                }
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
    <title>Registro Clientes</title>
</head>

<body>
    <!-- Encabezado -->
    <?php include "includes/header.php"; ?>

    <!-- Menu de navegacion -->
    <?php include "includes/nav.php"?>

    <!-- Contenido de la pagina -->
    <section class="container">
        <h1><i class="fa-solid fa-user-plus" style="color: #74C0FC;"></i> Registro Cliente</h1>
        <hr>
        <div class="alert"><?php echo(isset($alert) ? $alert : '');?></div>
        <form action="" method="post" class="registrar">

            <label for="nit">Nit</label>
            <input type="number" name="nit" id="nit" placeholder="numero de NIT">

            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" placeholder="Nombre completo">


            <label for="telefono">Teléfono</label>
            <input type="number" name="telefono" id="telefono" placeholder="Teléfono">

            <label for="clave">Dirección</label>
            <input type="text" name="direccion" id="direccion" placeholder="Dirección completa">

            
            <button type="submit" class="btn_save"><i class="fa-solid fa-floppy-disk" style="color: #ffffff;"></i> Guardar Cliente</button>
        </form>
    </section>
    <!-- Footer -->
    <?php include "includes/footer.php"?>


</body>

</html>