
<?php
    session_start();

    if($_SESSION["rol"] != 1 && $_SESSION["rol"] != 2){
        header("location: ../");
    }
?>

<?php
    include "../conexion.php";
    
    $msg_error = "";
    if(!empty($_POST)){

        if(empty($_POST['idproveedor'])){
            header("location: lista_proveedores.php");
            mysqli_close($conection);
            exit;
        }

        $idproveedor = $_POST['idproveedor'];

        $query_delete = mysqli_query($conection,"UPDATE proveedor SET estatus = 0 WHERE codproveedor = $idproveedor;");
        mysqli_close($conection);

        if($query_delete){
            header("location: lista_proveedores.php");
            }else{
            $msg_error = "!Error al eliminar el proveedor!";
        }

    }

    if(empty($_REQUEST['id']))
    {
        header("location: lista_proveedores.php");
        mysqli_close($conection);
    }else{

        $idproveedor = $_REQUEST['id'];

        $query = mysqli_query($conection,"SELECT * FROM proveedor WHERE proveedor.codproveedor = $idproveedor;");

        mysqli_close($conection);

        $result = mysqli_num_rows($query);

        if($result > 0){
          while($data = mysqli_fetch_array($query)){
          
            $proveedor = $data["proveedor"];
            $contacto= $data["contacto"];
          }
        }else{
            header("location: lista_proveedores.php"); 
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
    <title>Eliminar Proveedor</title>
</head>

<body>
    <!-- Encabezado -->
    <?php include "includes/header.php"; ?>

    <!-- Menu de navegacion -->
    <?php include "includes/nav.php"?>

    <!-- Contenido de la pagina -->
    <h1 class="titulo_eliminar"> <i class="fa-solid fa-user-xmark"></i> Eliminar proveedor</h1>
    <section class="cont_eliminar_user">
        <h1>¿Estas seguro de eliminar este proveedor?</h1>
        <div class = "cont_info_user">
             <span class="clave">Proveedor: </span> <span class="valor"><?php echo($proveedor);?></span>
            <br>
            <span class="clave">Contacto: </span> <span class="valor"><?php echo($contacto);?></span>
        </div>

        <form method ="post" action="" class="form_eliminar">
            <input type="hidden" name="idproveedor" value="<?php echo($idproveedor);?>"> 
            <a class="elim_bot" href="lista_proveedores.php"><i class="fa-solid fa-hand-point-left"></i> Cancelar</a>
            <button type="submit" class="elim_bot"><i class="fa-solid fa-user-slash"></i> Eliminar</button>
        </form>
        <p class="elim_msg_err"><?php echo($msg_error);?></p>
    </section>

    <!-- Footer -->
    <?php include "includes/footer.php"?>


</body>

</html>