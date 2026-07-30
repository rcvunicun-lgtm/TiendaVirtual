
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

        <?php
            require "../conexion.php";

            $busqueda = strtolower($_REQUEST["busqueda"]);

            if(empty($busqueda)){
                header("location: lista_usuarios.php");
                mysqli_close($conection);
            }
        ?>

        <form action="buscar_usuario.php" method="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" placeholder="buscar" value="<?php echo($busqueda); ?>">
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
                // if(!isset($_GET['pagina'])){
                //     header("location: lista_usuarios.php?pagina=1");
                // }     
                
                $rol = '';
                if($busqueda == "administrador"){
                    $rol = " OR usuario.rol LIKE '%1%'";
                }else if($busqueda == "supervisor"){
                    $rol = " OR usuario.rol LIKE '%2%'";
                }else if($busqueda == "vendedor"){
                    $rol = " OR usuario.rol LIKE '%3%'";
                }

                $sql_register = mysqli_query($conection,"SELECT COUNT(*) AS total_registro FROM usuario 
                WHERE 
                (
                idusuario LIKE '%$busqueda%'OR
                nombre LIKE '%$busqueda%'OR
                correo LIKE '%$busqueda%'OR
                usuario LIKE '%$busqueda%' 
                $rol
                )
                AND estatus = 1;");
                
                $result_register = mysqli_fetch_array($sql_register);
                $total_registro = $result_register["total_registro"];
            
                $por_pagina = 5;
                $total_paginas = ceil($total_registro / $por_pagina);

                if(empty($_GET['pagina'])){

                    $pagina = 1;

                }else{
                    $pagina = $_GET['pagina'];
                }

                // var_dumps(empty(0)); //Esto como reusltado me da true
                if(empty($_GET['pagina'])){
                    $pagina = 1;
                }else{
                    $pagina = $_GET['pagina'];
                    if($pagina > $total_paginas || $pagina < 0 ){
                        header("location:buscar_usuario.php?pagina=1&busqueda=$busqueda");
                        ob_end_flush(); // Enviar el contenido del búfer
                    }
                }

                $desde = ($pagina -1) * $por_pagina;

            
                
                if( isset($_GET['pagina']) && $_GET['pagina'] < 1){
                    header("location: buscar_usuario.php?pagina=$total_paginas&busqueda=$busqueda");
                }else if(isset($_GET['pagina']) && $_GET['pagina'] > $total_paginas){
                    header("location: buscar_usuario.php?pagina=1&busqueda=$busqueda");
                }

                $query = mysqli_query($conection, "SELECT usuario.idusuario, usuario.nombre, usuario.correo, usuario.usuario, rol.rol FROM usuario INNER JOIN rol ON usuario.rol = rol.idrol WHERE 
                (
                    idusuario LIKE '%$busqueda%'OR
                    nombre LIKE '%$busqueda%'OR
                    correo LIKE '%$busqueda%'OR
                    usuario LIKE '%$busqueda%' 
                    $rol
                    ) AND estatus = 1 ORDER BY idusuario LIMIT $desde, $por_pagina;");

                mysqli_close($conection);

                $result = mysqli_num_rows($query);

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
                        <a class="link_edit" href="editar_usuario.php?id=<?php echo($data["idusuario"]);?>">Editar</a>
                        <!-- Evitamos que se deba de eliminar el usuario principal -->
                        <?php 
                            if($data["idusuario"] != 1)
                            {
                        ?>
                        <a class="link_delete" href="eliminar_confirmar_usuario.php?id=<?php echo($data["idusuario"]);?>">Eliminar</a>
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