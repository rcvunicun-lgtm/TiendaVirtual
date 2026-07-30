<?php
    // Utilizo esta funcion porque si no la funcion header() no me sirve por temas de bufer 
    ob_start(); // Iniciar el almacenamiento en búfer
    session_start();

    if($_SESSION["rol"] != 1){
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
    <title>Lista de usuarios</title>
</head>

<body>
    <!-- Encabezado -->
    <?php include "includes/header.php"; ?>

    <!-- Menu de navegacion -->
    <?php include "includes/nav.php"?>

    <!-- Contenido de la pagina -->
    <section class="container_list_user">
        <h1> <i class="fa-solid fa-users"></i> Lista de usuarios</h1>
        <a href="registro_usuario.php" class="btn_new"> <i class="fa-solid fa-person-circle-plus" style="color: #ffffff;"></i> Crear usuario</a>

        <form action="buscar_usuario.php" method="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" placeholder="buscar">
            <!-- <input type="submit" value="buscar" class="btn_search"> -->
            <button type="submit" class="btn_search"><i class="fa-solid fa-magnifying-glass" style="color: #ffffff;"></i> Buscar</button>
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
            <?php

                require "../conexion.php";

                $sql_register = mysqli_query($conection,"SELECT COUNT(*) AS total_registro FROM usuario WHERE estatus = 1;");
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
                        header("location:lista_usuarios.php");
                        ob_end_flush(); // Enviar el contenido del búfer
                    }
                }
                
                $desde = ($pagina -1) * $por_pagina;

                $query = mysqli_query($conection, "SELECT usuario.idusuario, usuario.nombre, usuario.correo, usuario.usuario, rol.rol FROM usuario INNER JOIN rol ON usuario.rol = rol.idrol WHERE estatus = 1 ORDER BY idusuario LIMIT $desde, $por_pagina;");
                $result = mysqli_num_rows($query);

                mysqli_close($conection);

                if($result > 0)
                {
                    while($data = mysqli_fetch_array($query))
                    {

                ?>
                <tr>
                    <td><?php echo($data["idusuario"])?></td>
                    <td><?php echo($data["nombre"])?></td>
                    <td><?php echo($data["correo"])?></td>
                    <td><?php echo($data["usuario"])?></td>
                    <td><?php echo($data["rol"])?></td>
                    <td>
                        <a class="link_edit" href="editar_usuario.php?id=<?php echo($data["idusuario"]);?>"><i class="fa-solid fa-pen-to-square" style="color: #74C0FC;"></i> Editar</a>
                        <!-- Evitamos que se deba de eliminar el usuario principal -->
                        <?php 
                            if($data["idusuario"] != 1)
                            {
                        ?>
                        <a class="link_delete" href="eliminar_confirmar_usuario.php?id=<?php echo($data["idusuario"]);?>"><i class="fa-solid fa-trash-can" style="color: #ff0000;"></i> Eliminar</a>
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