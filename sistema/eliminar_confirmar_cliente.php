
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

        if(empty($_POST['idcliente'])){
            header("location: lista_clientes.php");
            mysqli_close($conection);
            exit;
        }

        $idcliente = $_POST['idcliente'];

        $query_delete = mysqli_query($conection,"UPDATE cliente SET estatus = 0 WHERE idcliente = $idcliente;");
        mysqli_close($conection);

        if($query_delete){
            header("location: lista_clientes.php");
            }else{
            $msg_error = "!Error al eliminar el cliente!";
        }

    }

    if(empty($_REQUEST['id']))
    {
        header("location: lista_clientes.php");
        mysqli_close($conection);
    }else{

    
        
        $idcliente = $_REQUEST['id'];

        $query = mysqli_query($conection,"SELECT * FROM cliente WHERE cliente.idcliente = $idcliente;");

        mysqli_close($conection);

        $result = mysqli_num_rows($query);

        if($result > 0){
          while($data = mysqli_fetch_array($query)){
            $nit = $data["nit"];
            $nombre = $data["nombre"];
          }
        }else{
            header("location: lista_clientes.php"); 
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
    <title>Eliminar Cliente</title>
</head>

<body>
    <!-- Encabezado -->
    <?php include "includes/header.php"; ?>

    <!-- Menu de navegacion -->
    <?php include "includes/nav.php"?>

    <!-- Contenido de la pagina -->
    <h1 class="titulo_eliminar"> <i class="fa-solid fa-user-xmark"></i> Eliminar cliente</h1>
    <section class="cont_eliminar_user">
        <h1>¿Estas seguro de eliminar este cliente?</h1>
        <div class = "cont_info_user">
             <span class="clave">Nombre: </span> <span class="valor"><?php echo($nombre);?></span>
            <br>
            <span class="clave">Nit: </span> <span class="valor"><?php echo($nit);?></span>
        </div>

        <form method ="post" action="" class="form_eliminar">
            <a class="elim_bot" href="lista_clientes.php"><i class="fa-solid fa-hand-point-left"></i> Cancelar</a>
            <input type="hidden" name="idcliente" value="<?php echo($idcliente);?>">
            <button type="submit" class="elim_bot"><i class="fa-solid fa-user-slash"></i> Eliminar</button>
        </form>
        <p class="elim_msg_err"><?php echo($msg_error);?></p>
    </section>

    <!-- Footer -->
    <?php include "includes/footer.php"?>


</body>

</html>