<?php
    // Utilizo esta funcion porque si no la funcion header() no me sirve por temas de bufer 
    ob_start(); // Iniciar el almacenamiento en búfer
    session_start();

    // if($_SESSION["rol"] != 1){
    //     header("location: ../");
    // }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <!-- Incrustamos el archivo scripts.php -->
    <?php include "../sistema/includes/scripts.php"?>
 
    <title>Lista de productos</title>
</head>

<body>
    <!-- Encabezado -->
    <?php include "includes/header.php"; ?>

    <!-- Menu de navegacion -->
    <?php include "includes/nav.php"?>

    <!-- Contenido de la pagina -->
    <section class="container_list_user">

        <?php
            $busqueda = '';
            $search_proveedor = '';

            if(empty($_REQUEST["busqueda"]) && empty($_REQUEST["proveedor"])){
                header('location: lista_producto.php');
            }

            if(!empty($_REQUEST['busqueda'])){
                $busqueda = strtolower($_REQUEST['busqueda']);
                $where = " 
                (
                p.codproducto LIKE '%$busqueda%'OR
                p.descripcion LIKE '%$busqueda%'          
                )
                AND p.estatus = 1 ";
                $buscar = 'busqueda='.$busqueda;
            }

            if(!empty($_REQUEST['proveedor'])){
                $search_proveedor = strtolower($_REQUEST['proveedor']);
                $where =  "p.proveedor LIKE $search_proveedor AND p.estatus = 1 ";
                $buscar = 'proveedor='.$search_proveedor;
            }
        ?>

        <h1><i class="fa-solid fa-cash-register" style="color: #000000;"></i> Lista de productos</h1>
        <a href="registro_producto.php" class="btn_new"> <i class="fa-solid fa-person-circle-plus" style="color: #ffffff;"></i> Crear producto</a>

        <form action="buscar_producto.php" method="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" placeholder="buscar" value="<?php echo($busqueda) ?>">
            <button type="submit" class="btn_search"><i class="fa-solid fa-magnifying-glass" style="color: #ffffff;"></i> Buscar</button>
        </form>

        <table>
            <tr>
                <th>CÒDIGO</th>
                <th>DESCRIPCIÒN</th>
                <th>PRECIO</th>
                <th>EXISTENCIA</th>
                <th>

                    <?php 
                        require "../conexion.php";

                        $pro = 0;

                        if(!empty($_REQUEST['proveedor'])){
                            $pro = $_REQUEST['proveedor'];
                        }

                        $query_proveedor = mysqli_query($conection, "SELECT codproveedor, proveedor FROM proveedor WHERE estatus = 1 ORDER BY proveedor ASC;");

                        $result_proveedor = mysqli_num_rows($query_proveedor);
                        
                    ?>

                    <select name="proveedor" id="search_proveedor">
                        <option value="" selected>PROVEEDORES</option>
                    <?php
                        if($result_proveedor > 0)
                        {
                            while($proveedor = mysqli_fetch_array($query_proveedor))
                            {
                                if($pro == $proveedor["codproveedor"])

                                {

                    ?>
                        
                                    <option value="<?php echo($proveedor["codproveedor"]);?>" selected><?php echo($proveedor["proveedor"]);?></option>

                    <?php
                                }else{
                    ?>

                                    <option value="<?php echo($proveedor["codproveedor"]);?>"><?php echo($proveedor["proveedor"]);?></option>
                    
                    <?php
                                }
                            }
                        }
                    ?>
                    </select>

                </th>
                <th>FOTO</th>
                <?php
                if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2)
                    {
                ?>
                <th>ACCIONES</th>
                <?php
                    }
                ?>
            </tr>
            <?php
                // if(!isset($_GET['pagina'])){
                //     header("location: lista_usuarios.php?pagina=1");
                // }

                $sql_register = mysqli_query($conection,"SELECT COUNT(*) AS total_registro FROM producto AS p WHERE $where ;");

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
                        header("location:lista_producto.php");
                        ob_end_flush(); // Enviar el contenido del búfer
                    }
                }


                $desde = ($pagina -1) * $por_pagina;

                $query = mysqli_query($conection, "SELECT p.codproducto, p.descripcion, p.precio, p.existencia, pr.proveedor, p.foto FROM producto as p INNER JOIN proveedor as pr ON p.proveedor =  pr.codproveedor WHERE $where ORDER BY p.codproducto ASC LIMIT $desde, $por_pagina;");
                $result = mysqli_num_rows($query);

                mysqli_close($conection);

                if($result > 0)
                {

                    while($data = mysqli_fetch_array($query))
                    {
                        
                        if($data['foto'] != 'img_producto.png'){
                            $foto = '../sistema/img/uploads/'.$data['foto'];
                        }else{
                            $foto = '../sistema/img/'.$data['foto'];
                        }

                ?>
                <tr class="row<?php echo($data["codproducto"]);?>">
                    <td><?php echo($data["codproducto"])?></td>
                    <td><?php echo($data["descripcion"])?></td>
                    <td class="celPrecio"><?php echo($data["precio"])?></td>
                    <td class="celExistencia"><?php echo($data["existencia"])?></td>
                    <td><?php echo($data["proveedor"])?></td>
                    <td><img src="<?php echo($foto)?>" alt="<?php echo($foto)?>" width=100px></td>
                
                    <?php
                        if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2)
                        {
                    ?>

                    <td>
                        <a class="link_add add_product" product="<?php echo($data["codproducto"]);?>" href="#"><i class="fa-solid fa-truck-ramp-box" style="color: #36a684;"></i> Agregar </a>

                        <a class="link_edit" href="editar_producto.php?id=<?php echo($data["codproducto"]);?>"><i class="fa-solid fa-pen-to-square" style="color: #74C0FC;"></i> Editar</a>              
                      
                        <a class="link_delete del_product" product="<?php echo($data["codproducto"]);?>" href="#"><i class="fa-solid fa-trash-can" style="color: #ff0000;"></i> Eliminar</a>
                      
                    </td>

                    <?php 
                        } 
                    ?>
                </tr>

                <?php
                    }
                }
            ?>
        </table>

        <?php
            if($total_paginas != 0)
            {
        ?>

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
        <?php
            }
        ?>
    
    </section>
    <!-- Footer -->
    <?php include "includes/footer.php"?>
</body>

</html>