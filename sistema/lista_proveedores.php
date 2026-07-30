<?php
    // Utilizo esta funcion porque si no la funcion header() no me sirve por temas de bufer 
    ob_start(); // Iniciar el almacenamiento en búfer
    session_start();

    if($_SESSION["rol"] != 1 and $_SESSION["rol"] != 2){
        header("location: ../");
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <!-- Incrustamos el archivo scripts.php -->
    <?php include "includes/scripts.php"?>
    <script src="../sistema/js/prueba.js"></script>
    <title>Lista de proveedores</title>
</head>

<body>
    <!-- Encabezado -->
    <?php include "includes/header.php"; ?>

    <!-- Menu de navegacion -->
    <?php include "includes/nav.php"?>

    <!-- Contenido de la pagina -->
    <section class="container_list_user">
        <h1><i class="fa-solid fa-users"></i> Lista de proveedores</h1>
        <a href="registro_proveedor.php" class="btn_new"> <i class="fa-solid fa-person-circle-plus" style="color: #ffffff;"></i> Crear proveedor</a>

        <form action="buscar_proveedor.php" method="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" placeholder="buscar">
            <button type="submit" class="btn_search"><i class="fa-solid fa-magnifying-glass" style="color: #ffffff;"></i> Buscar</button>
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>PROVEEDOR</th>
                <th>CONTACTO</th>
                <th>TELÈFONO</th>
                <th>DIRECCIÒN</th>
                <th>FECHA</th>
                <th>ACCIONES</th>
            </tr>
            <?php
                // if(!isset($_GET['pagina'])){
                //     header("location: lista_usuarios.php?pagina=1");
                // }

                require "../conexion.php";

                //PAGINADOR
                $sql_register = mysqli_query($conection,"SELECT COUNT(*) AS total_registro FROM proveedor WHERE estatus = 1;");
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

                $query = mysqli_query($conection, "SELECT * FROM proveedor WHERE estatus = 1 ORDER BY codproveedor ASC LIMIT $desde, $por_pagina;");
                $result = mysqli_num_rows($query);

                mysqli_close($conection);

                if($result > 0)
                {

               
                    while($data = mysqli_fetch_array($query))
                    {

                        $formato = 'Y-m-d H:i:s';
                        $fecha = DateTime::createFromFormat($formato,$data["date_add"]);
                
                ?>
                <tr>
                    <td><?php echo($data["codproveedor"])?></td>
                    <td><?php echo($data["proveedor"])?></td>
                    <td><?php echo($data["contacto"])?></td>
                    <td><?php echo($data["telefono"])?></td>
                    <td><?php echo($data["direccion"])?></td>
                    <td><?php echo($fecha->format('d-m-Y'))?></td>
                  
                    <td>
                        <a class="link_edit" href="editar_proveedor.php?id=<?php echo($data["codproveedor"]);?>"><i class="fa-solid fa-pen-to-square" style="color: #74C0FC;"></i> Editar</a>
                        <!-- Evitamos que se deba de eliminar el usuario principal -->
                       
                        <a class="link_delete" href="eliminar_confirmar_proveedor.php?id=<?php echo($data["codproveedor"]);?>"><i class="fa-solid fa-trash-can" style="color: #ff0000;"></i> Eliminar</a>
                      
                    </td>
                </tr>

                <?php
                    }
                }
            ?>
        </table>

        <div class="paginador">
            <ul>
                <li><a href="?pagina=<?php if($pagina > 1){echo($pagina-1);}else{echo($total_paginas);} ?>"><i class="fa-solid fa-caret-left fa-flip-vertical"></i></a></li>
                <li><a href="?pagina=<?php echo(1); ?>"><i class="fa-solid fa-backward"></i></a></li>
                <?php 
                    for($i = 1; $i <= $total_paginas; $i++){
                        if($i == $pagina ){
                            echo('<li><a class="page_selected" href="#">'.$i.'</a></li>');
                        }else{

                         echo('<li><a href="?pagina='.$i.'">'.$i.'</a></li>');
                        }
                    }

                  
                ?>
                <li><a href="?pagina=<?php echo($total_paginas); ?>"><i class="fa-solid fa-backward fa-flip-horizontal"></i></a></li> 
                <li><a href="?pagina=<?php if($pagina < $total_paginas){echo($pagina+1);}else{echo(1);}?>"><i class="fa-solid fa-caret-left fa-flip-horizontal"></i></a></li>
              
            </ul>
        </div>

    
    </section>
    <!-- Footer -->
    <?php include "includes/footer.php"?>


</body>

</html>