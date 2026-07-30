
<?php
    session_start();

    if($_SESSION["rol"] != 1 and $_SESSION["rol"] != 2){
        header("location: ../");
    }
    
    require "../conexion.php";

    if(!empty($_POST)){
        $alert = "";
        if(empty($_POST["proveedor"]) || empty($_POST["contacto"]) || empty($_POST["telefono"]) || empty($_POST["direccion"])){
            $alert = "<p class='msg_error'>Todos los campos son obligatorios</p>";
        }else{

            $proveedor = $_POST["proveedor"];
            $contacto = $_POST["contacto"];
            $telefono = $_POST["telefono"];
            $direccion = $_POST["direccion"];
            $usuario_id = $_SESSION["idUser"];
            
         
            $query_insert = mysqli_query($conection, "INSERT INTO proveedor(proveedor, contacto, telefono, direccion, usuario_id) VALUES('$proveedor', '$contacto', $telefono, '$direccion', $usuario_id)");
                
            if($query_insert)
                {
                    $alert = "<p class='msg_save'>Proveedor guardado correctamente</p>";
                 
                }else{
                    $alert = "<p class='msg_error'>Error al guardar el proveedor</p>";
                    
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
    <title>Registro Proveedores</title>
</head>

<body>
    <!-- Encabezado -->
    <?php include "includes/header.php"; ?>

    <!-- Menu de navegacion -->
    <?php include "includes/nav.php"?>

    <!-- Contenido de la pagina -->
    <section class="container">
        <h1><i class="fa-solid fa-cart-plus" style="color: #74C0FC;"></i> Registro Proveedor</h1>
        <hr>
        <div class="alert"><?php echo(isset($alert) ? $alert : '');?></div>
        <form action="" method="post" class="registrar">

            <label for="proveedor">Proveedor</label>
            <input type="text" name="proveedor" id="proveedor" placeholder="Nombre del proveedor">

            <label for="contacto">Contacto</label>
            <input type="text" name="contacto" id="contacto" placeholder="Nombre completo el contacto">


            <label for="telefono">Teléfono</label>
            <input type="number" name="telefono" id="telefono" placeholder="Teléfono">

            <label for="clave">Dirección</label>
            <input type="text" name="direccion" id="direccion" placeholder="Dirección completa">

            
            <button type="submit" class="btn_save"><i class="fa-solid fa-floppy-disk" style="color: #ffffff;"></i> Guardar Proveedor</button>
        </form>
    </section>
    <!-- Footer -->
    <?php include "includes/footer.php"?>


</body>

</html>