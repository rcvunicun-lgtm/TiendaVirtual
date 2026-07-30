
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
    <?php include "includes/scripts.php"?>
    <title>Lista de clientes</title>
</head>

<body>
    <!-- Encabezado -->
    <?php include "includes/header.php"; ?>

    <!-- Menu de navegacion -->
    <?php include "includes/nav.php"?>

    <!-- Contenido de la pagina -->
    <section class="container_list_user">
        <h1><i class="fa-solid fa-users"></i> Lista de clientes</h1>
        <a href="registro_cliente.php" class="btn_new"> <i class="fa-solid fa-person-circle-plus" style="color: #ffffff;"></i> Crear cliente</a>

        <?php
            require "../conexion.php";

            $busqueda = strtolower($_REQUEST["busqueda"]);

            if(empty($busqueda)){
                header("location: lista_clientes.php");
                mysqli_close($conection);
            }
        ?>

        <form action="buscar_cliente.php" method="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" placeholder="buscar" value="<?php echo($busqueda); ?>">
            <button type="submit" class="btn_search"><i class="fa-solid fa-magnifying-glass" style="color: #ffffff;"></i> Buscar</button>
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Nit</th>
                <th>Nombre</th>
                <th>Telefono</th>
                <th>Direccion</th>
                <th>Acciones</th>
            </tr>
            <?php
        
                //paginador
                $sql_register = mysqli_query($conection,"SELECT COUNT(*) AS total_registro FROM cliente 
                WHERE 
                (
                idcliente LIKE '%$busqueda%'OR
                nit LIKE '%$busqueda%'OR
                nombre LIKE '%$busqueda%'OR
                telefono LIKE '%$busqueda%' OR 
                direccion LIKE '%$busqueda%' 
                )
                AND estatus = 1;");
                
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
                        header("location:buscar_cliente.php?pagina=1&busqueda=$busqueda");
                        ob_end_flush(); // Enviar el contenido del búfer
                    }
                }

                $desde = ($pagina -1) * $por_pagina;

                $query = mysqli_query($conection, "SELECT * FROM cliente WHERE 
                (
                    idcliente LIKE '%$busqueda%'OR
                    nit LIKE '%$busqueda%'OR
                    nombre LIKE '%$busqueda%'OR
                    telefono LIKE '%$busqueda%' OR
                    direccion LIKE '%$busqueda%'
            
                    ) AND estatus = 1 ORDER BY idcliente LIMIT $desde, $por_pagina;");

                mysqli_close($conection);

                $result = mysqli_num_rows($query);

                if($result > 0)
                {
                    while($data = mysqli_fetch_array($query))
                    {

            ?>
                <tr>
                    <td><?php echo($data["idcliente"])?></td>
                    <td><?php echo($data["nit"])?></td>
                    <td><?php echo($data["nombre"])?></td>
                    <td><?php echo($data["telefono"])?></td>
                    <td><?php echo($data["direccion"])?></td>
                    <td>
                        <a class="link_edit" href="editar_cliente.php?id=<?php echo($data["idcliente"]);?>">Editar</a>
                        <!-- Evitamos que se deba de eliminar el usuario principal -->
                        <?php 
                            if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2)
                            {
                        ?>
                        <a class="link_delete" href="eliminar_confirmar_usuario.php?id=<?php echo($data["idcliente"]);?>">Eliminar</a>
                        <?php 
                            }
                        ?>
                    </td>
                </tr>

                <?php
                    }
                }
            ?>
        </table>
        
        <?php if($total_registro != 0)
            {
        ?>
                <div class="paginador">
                    <ul>
                        <li><a href="?pagina=<?php if($pagina > 1){echo($pagina-1);}else{echo($total_paginas);} ?>&busqueda=<?php echo($busqueda);?>"><i class="fa-solid fa-caret-left fa-flip-vertical"></i></a></li>
                        <li><a href="?pagina=<?php echo(1);?>&busqueda=<?php echo($busqueda);?>"><i class="fa-solid fa-backward"></i></a></li>
                        <?php 
                            for($i = 1; $i <= $total_paginas; $i++){
                                if($i == $pagina ){
                                    echo('<li><a class="page_selected" href="#">'.$i.'</a></li>');
                                }else{

                                echo('<li><a href="?pagina='.$i.'&busqueda='.$busqueda.'">'.$i.'</a></li>');
                                }
                            }

                        
                        ?>
                        <li><a href="?pagina=<?php echo($total_paginas);?>&busqueda=<?php echo($busqueda);?>"><i class="fa-solid fa-backward fa-flip-horizontal"></i></a></li> 
                        <li><a href="?pagina=<?php if($pagina < $total_paginas){echo($pagina+1);}else{echo(1);}?>&busqueda=<?php echo($busqueda);?>"><i class="fa-solid fa-caret-left fa-flip-horizontal"></i></a></li>
                    
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