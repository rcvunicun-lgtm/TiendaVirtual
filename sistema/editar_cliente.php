
<?php
    session_start();

    // if($_SESSION["rol"] != 1 || $_SESSION["rol"] != 2 ){
    // if($_SESSION["rol"] == 3){
    //     header("location: ../");
    // }

?>
<?php

    require "../conexion.php";

    if(!empty($_POST)){
        $alert = "";
        if(empty($_POST["nombre"]) || empty($_POST["telefono"]) || empty($_POST["direccion"])){
            $alert = "<p class='msg_error'>Todos los campos son obligatorios</p>";
        }else{

            $idCliente= $_POST["id"];
            $nit = $_POST["nit"];
            $nombre = $_POST["nombre"];
            $telefono = $_POST["telefono"];
            $direccion = $_POST["direccion"];

            $result = 0;

            if(is_numeric($nit) and $nit != 0){

                $query = mysqli_query($conection, "SELECT * FROM cliente WHERE (nit = '$nit' AND idCliente != $idCliente);");
                $result = mysqli_fetch_array($query);
                // $result = count($result); //En el curso de PHP si funciona per aca aparece un error.
            }

            if($result > 0){
                $alert = "<p class='msg_error'>El nit ya existe, Ingrese otro</p>";
            }else{

                if($nit == ''){
                    $nit = 0;
                }

                    $sql_update = mysqli_query($conection,"UPDATE cliente SET nit = '$nit', nombre = '$nombre', telefono = '$telefono', direccion = '$direccion' WHERE idcliente = $idCliente;");
                             
                if($sql_update){
                    $alert = "<p class='msg_save'>Cliente actualizado correctamente</p>";
                }else{
                    $alert = "<p class='msg_error'>Error al actualizar el cliente</p>";
                }
            }
        
        }
   
    }

    // Mostrar datos
    if(empty($_REQUEST['id'])){
        header('Location: lista_clientes.php');
        mysqli_close($conection);
    }

    require "../conexion.php";
    $id_cliente = $_REQUEST['id'];
    $query = mysqli_query($conection, "SELECT * FROM cliente  WHERE cliente.idcliente = $id_cliente and estatus = 1;");
    $result = mysqli_num_rows($query);
    mysqli_close($conection);


    if($result == 0){
        header('Location: lista_clientes.php');  
    }else{

        while($data = mysqli_fetch_array($query)){
            $idcliente = $data["idcliente"];
            $nit = $data["nit"];
            $nombre = $data["nombre"];
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
    <title>Actualizar Cliente</title>
</head>

<body>
    <!-- Encabezado -->
    <?php include "includes/header.php"; ?>

    <!-- Menu de navegacion -->
    <?php include "includes/nav.php"?>

    <!-- Contenido de la pagina -->
    <section class="container">
        <h1><i class="fa-solid fa-user-pen"></i> Editar Cliente</h1>
        <hr>
        <div class="alert"><?php echo(isset($alert) ? $alert : '');?></div>
        <form action="" method="post">

            <input type="hidden" name = "id"  value="<?php echo($idcliente)?>">

            <label for="nit">Nit</label>
            <input type="number" name="nit" id="nit" placeholder="numero de NIT" value="<?php echo($nit)?>">

            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" placeholder="Nombre completo" value="<?php echo($nombre)?>">


            <label for="telefono">Teléfono</label>
            <input type="number" name="telefono" id="telefono" placeholder="Teléfono"  value="<?php echo($telefono)?>">

            <label for="clave">Dirección</label>
            <input type="text" name="direccion" id="direccion" placeholder="Dirección completa"  value="<?php echo($direccion)?>">

            <button type="submit" class="btn_save"><i class="fa-solid fa-floppy-disk" style="color: #ffffff;"></i> Actualizar cliente</button>
        </form>
    </section>
    <!-- Footer -->
    <?php include "includes/footer.php"?>


</body>

</html>