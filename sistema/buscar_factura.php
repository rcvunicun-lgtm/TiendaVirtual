<?php
    // Utilizo esta funcion porque si no la funcion header() no me sirve por temas de bufer 
    ob_start(); // Iniciar el almacenamiento en búfer
    session_start();

    // if($_SESSION["rol"] != 1 and $_SESSION["rol"] != 2){
    //     header("location: ../");
    // }

    $busqueda = '';
    $fecha_de = '';
    $fecha_a = '';

    if(isset($_REQUEST['busqueda']) && $_REQUEST['busqueda'] == ''){
        header("location:  lista_factura.php");
    }

    if(isset($_REQUEST['fecha_de']) || isset($_REQUEST['fecha_a'])){
        if($_REQUEST['fecha_de'] == '' || $_REQUEST['fecha_a'] == ''){
      
            header("location:  lista_factura.php");
        }
    }

    if(isset($_REQUEST['busqueda']) && !empty($_REQUEST['busqueda'])){
        if(!is_numeric($_REQUEST['busqueda'])){
            header("location:  lista_factura.php");
        }
            $busqueda = strtolower($_REQUEST["busqueda"]);
            $where = "nofactura = $busqueda"; 
            $buscar = "busqueda=$busqueda";
    }

    if(!empty($_REQUEST["fecha_de"]) && !empty($_REQUEST["fecha_a"])){
        $fecha_de = $_REQUEST["fecha_de"];
        $fecha_a = $_REQUEST["fecha_a"];
        
        // Esta variable la vamos a usar para los datos que vamos a pasar en la paginacion
        $buscar = '';

        if($fecha_de > $fecha_a){
            header("location: lista_factura.php");
        }else if($fecha_de == $fecha_a){
      
            $where = "fecha LIKE '$fecha_de%'";
            $buscar = "fecha_de=$fecha_de&fecha_a=$fecha_a";
       
        }else{
            $f_de = $fecha_de. ' 00:00:00';
            $f_a = $fecha_a. ' 23:59:59';
            $where = "fecha BETWEEN '$f_de' AND '$f_a'";
            $buscar = "fecha_de=$fecha_de&fecha_a=$fecha_a";
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
    <title>Lista de facturas</title>
</head>

<body>
    <!-- Encabezado -->
    <?php include "includes/header.php"; ?>

    <!-- Menu de navegacion -->
    <?php include "includes/nav.php"?>

    <!-- Contenido de la pagina -->
    <section class="container_list_user">
        <h1><i class="fa-solid fa-users"></i> Lista de facturas</h1>
        <a href="nueva_venta.php" class="btn_new"> <i class="fa-solid fa-person-circle-plus" style="color: #ffffff;"></i> Nueva venta</a>

        <form action="buscar_factura.php" method="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" placeholder="Nº Factura" value="<?php echo($busqueda);?>">
            <button type="submit" class="btn_search"><i class="fa-solid fa-magnifying-glass" style="color: #ffffff;"></i> Buscar</button>
        </form>

        <div class="cont_filt_fecha">
            <h5>Buscar por fecha</h5>
            <form action="buscar_factura.php" method="get" class="form_search_date">
                <label for="">De: </label>
                <input type="date" name="fecha_de" id="fecha_de" value="<?php echo($fecha_de); ?>" required>
                <label for="">A: </label>
                <input type="date" name="fecha_a" id="fecha_a" value="<?php echo($fecha_a); ?>" required>
                <button  type="submit" class="btn_search"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
           
        </div>

        <table>
            <tr>
                <th>NUMERO FACTURA</th>
                <th>FECHA / HORA</th>
                <th>CLIENTE</th>
                <th>VENDEDOR</th>
                <th>ESTADO</th>
                <th class="textright">TOTAL FACTURA</th>
                <th class="textright">ACCIONES</th>
            </tr>
            <?php
                // if(!isset($_GET['pagina'])){
                //     header("location: lista_usuarios.php?pagina=1");
                // }

                require "../conexion.php";
    
                //PAGINADOR
                $sql_register = mysqli_query($conection,"SELECT COUNT(*) AS total_registro FROM factura WHERE $where");
                $result_register = mysqli_fetch_array($sql_register);
                $total_registro = $result_register["total_registro"];
                
                $por_pagina = 5;
                $total_paginas = ceil($total_registro / $por_pagina);

                // var_dumps(empty(0)); //Esto como reusltado me da true
                if(empty($_GET['pagina'])){
                    $pagina = 1;
                }else{
                    $pagina = $_GET['pagina'];
                    if($pagina > $total_paginas || $pagina < 0){
                        mysqli_close($conection);
                        header("location:lista_proveedores.php");
                        ob_end_flush(); // Enviar el contenido del búfer
                    }
                }

                $desde = ($pagina -1) * $por_pagina;

                $query = mysqli_query($conection, "SELECT f.nofactura, f.fecha, f.totalfactura, f.codcliente, f.estatus, u.nombre AS vendedor, cl.nombre AS cliente FROM factura AS f INNER JOIN usuario AS u ON f.usuario = u.idusuario INNER JOIN cliente AS cl ON f.codcliente = cl.idcliente WHERE $where AND f.estatus != 10 ORDER BY f.fecha DESC LIMIT $desde, $por_pagina;");
                $result = mysqli_num_rows($query);

                mysqli_close($conection);

                if($result > 0)
                {

               
                    while($data = mysqli_fetch_array($query))
                    {

                        $formato = 'Y-m-d H:i:s';
                        $fecha = DateTime::createFromFormat($formato,$data["fecha"]);

                        if($data["estatus"] == 1){
                            $estado = '<p class="estado" style="background:rgb(34, 234, 134); padding:5px; border-radius:10px;">Pagada</p>';
                        }else if($data["estatus"] == 2){
                            $estado = '<p class="estado" style="background:rgb(249, 4, 64); padding:5px; border-radius:10px;">Anulada</p>';
                        }
                
                ?>
                <tr id="row_<?php echo($data["nofactura"])?>">
                    <td><?php echo($data["nofactura"])?></td>
                    <td><?php echo($fecha->format('d-m-Y'))?></td>
                    <td><?php echo($data["cliente"])?></td>
                    <td><?php echo($data["vendedor"])?></td>
                 
                    <td><?php echo($estado)?></td>
                    <td style="text-align:right;"><?php echo($data["totalfactura"])?></td>
                  
                    <td style="text-align:right;">
                        <a class="link_edit" href="../sistema/factura/generaFactura.php/?cl=<?php echo($data["codcliente"]);?>&f=<?php echo($data["nofactura"]);?>" target="_blank" style=" color:white; background:rgb(47, 179, 241); border-radius:10px;"><i class="fa-solid fa-eye"></i></a>
                        <!-- Evitamos que se deba de eliminar el usuario principal -->
                        
                        <?php 
                            if($_SESSION["idUser"] != 3){

                           
                                if($data["estatus"] == 1)
                                {  
                        ?>
                        <a class="link_delete anular_factura" fac="<?php echo($data["nofactura"])?>" href="#" style=" color:white; background:rgb(249, 4, 64); border-radius:10px;"><i class="fa-solid fa-trash-can"></i></a>
                        <?php 
                                }else if($data["estatus"] == 2){  
                        ?>
                        <a class="link_delete" href="#" style="color:gray; background:rgb(184, 170, 173); border-radius:10px;" ><i class="fa-solid fa-trash-can"></i></a>
                        <?php
                                }
                            }
                        ?>
                    </td>
                </tr>

                <?php
                    }
                }
            ?>
        </table>
                
        <div class="paginador">
            <ul>
                <li><a href="?pagina=<?php if($pagina > 1){echo($pagina-1);}else{echo($total_paginas);}?>&<?php echo($buscar);?>"><i class="fa-solid fa-caret-left fa-flip-vertical"></i></a></li>
                <li><a href="?pagina=<?php echo(1); ?>&<?php echo($buscar);?>"><i class="fa-solid fa-backward"></i></a></li>
                <?php 
                    for($i = 1; $i <= $total_paginas; $i++){
                        if($i == $pagina ){
                            echo('<li><a class="page_selected" href="#">'.$i.'</a></li>');
                        }else{

                         echo('<li><a href="?pagina='.$i.'&'.$buscar.'">'.$i.'</a></li>');
                        }
                    }

                  
                ?>
                <li><a href="?pagina=<?php echo($total_paginas); ?>&<?php echo($buscar);?>"><i class="fa-solid fa-backward fa-flip-horizontal"></i></a></li> 
                <li><a href="?pagina=<?php if($pagina < $total_paginas){echo($pagina+1);}else{echo(1);}?>&<?php echo($buscar);?>"><i class="fa-solid fa-caret-left fa-flip-horizontal"></i></a></li>
              
            </ul>
        </div>

    
    </section>
    <!-- Footer -->
    <?php include "includes/footer.php"?>


</body>

</html>