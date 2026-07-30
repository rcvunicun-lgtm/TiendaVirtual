
<?php
    session_start();

    if($_SESSION["rol"] != 1 and $_SESSION["rol"] != 2){
        header("location: ../");
    }

?>
<?php

    require "../conexion.php";

    if(!empty($_POST))
    {
        $alert = "";
        if(empty($_POST["proveedor"]) || empty($_POST["contacto"]) || empty($_POST["telefono"]) || empty($_POST["direccion"])){
            $alert = "<p class='msg_error'>Todos los campos son obligatorios</p>";
        }else{

                $id_proveedor= $_POST["id"];
                $proveedor= $_POST["proveedor"];
                $contacto = $_POST["contacto"];
                $telefono = $_POST["telefono"];
                $direccion = $_POST["direccion"];

                $sql_update = mysqli_query($conection,"UPDATE proveedor SET proveedor = '$proveedor', contacto = '$contacto', telefono = '$telefono', direccion = '$direccion' WHERE codproveedor = $id_proveedor;");
                                
                    if($sql_update){
                        $alert = "<p class='msg_save'>Proveedor actualizado correctamente</p>";
                    }else{
                        $alert = "<p class='msg_error'>Error al actualizar el proveedor</p>";
                    }
            }
    }
   
    // Mostrar datos
    if(empty($_REQUEST['id'])){
        header('Location: lista_proveedores.php');
        mysqli_close($conection);
    }

    require "../conexion.php";
    $id_proveedor = $_REQUEST['id'];
    $query = mysqli_query($conection, "SELECT * FROM proveedor  WHERE proveedor.codproveedor = $id_proveedor and estatus = 1;");
    $result = mysqli_num_rows($query);
    mysqli_close($conection);


    if($result == 0){
        header('Location: lista_proveedores.php');  
    }else{

        while($data = mysqli_fetch_array($query)){
            $codProveedor = $data["codproveedor"];
            $proveedor = $data["proveedor"];
            $contacto = $data["contacto"];
            $telefono = $data["telefono"];
            $direccion = $data["direccion"];
              
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
    <title>Actualizar Proveedor</title>
</head>

<body>
    <!-- Encabezado -->
    <?php include "includes/header.php"; ?>

    <!-- Menu de navegacion -->
    <?php include "includes/nav.php"?>

    <!-- Contenido de la pagina -->
    <section class="container">
        <h1><i class="fa-solid fa-user-pen"></i> Editar Proveedor</h1>
        <hr>
        <div class="alert"><?php echo(isset($alert) ? $alert : '');?></div>
        <form action="" method="post">

            <input type="hidden" name="id" value="<?php echo($codProveedor); ?>">

            <label for="proveedor">Proveedor</label>
            <input type="text" name="proveedor" id="proveedor" placeholder="Nombre del proveedor" value="<?php echo($proveedor); ?>">

            <label for="contacto">Contacto</label>
            <input type="text" name="contacto" id="contacto" placeholder="Nombre completo el contacto" value="<?php echo($contacto); ?>">


            <label for="telefono">Teléfono</label>
            <input type="number" name="telefono" id="telefono" placeholder="Teléfono" value="<?php echo($telefono); ?>">

            <label for="clave">Dirección</label>
            <input type="text" name="direccion" id="direccion" placeholder="Dirección completa" value="<?php echo($direccion); ?>">


            <button type="submit" class="btn_save"><i class="fa-solid fa-floppy-disk" style="color: #ffffff;"></i> Actualizar Proveedor</button>
        </form>
    </section>
    <!-- Footer -->
    <?php include "includes/footer.php"?>


</body>

</html>