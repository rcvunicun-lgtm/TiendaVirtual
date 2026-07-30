<!-- TRIGGER DE BD -->
<!-- 
    DELIMITER //
	CREATE TRIGGER entradas_A_I AFTER INSERT ON producto FOR EACH ROW
    BEGIN
    	INSERT INTO entradas (codproducto, cantidad, precio, usuario_id)
        VALUES(new.codproducto, new.existencia, new.precio, new.usuario_id);
    END;//
DELIMITER ;
 -->


<?php
    session_start();

    if($_SESSION["rol"] != 1 and $_SESSION["rol"] != 2){
        header("location: ../");
    }
    
    require "../conexion.php";

    if(!empty($_POST)){
    
        $alert = "";
        if(empty($_POST["proveedor"]) || empty($_POST["producto"]) || empty($_POST["precio"]) || $_POST["precio"] <= 0 || empty($_POST["cantidad"]) || $_POST["cantidad"] <= 0){
            $alert = "<p class='msg_error'>Todos los campos son obligatorios</p>";
        }else{

            $proveedor = $_POST["proveedor"];
            $producto = $_POST["producto"];
            $precio = $_POST["precio"];
            $cantidad = $_POST["cantidad"];
            $usuario_id = $_SESSION["idUser"];
            $foto = $_FILES["foto"];
            $nombre_foto = $foto["name"];
            $nombre_type = $foto["type"];
            $nombre_url_temp = $foto["tmp_name"];
            $size_foto = $foto["size"];

            $imgProducto = 'img_producto.png';

            if($nombre_foto != ''){
                $destino = '../sistema/img/uploads/';
                $img_nombre = 'img_'. md5(date('d-m-Y H:m:s'));
                $imgProducto = $img_nombre.'.jpg';
                $src = $destino.$imgProducto;
            }
         
            $query_insert = mysqli_query($conection, "INSERT INTO producto(descripcion, proveedor, precio, existencia, usuario_id, foto) VALUES('$producto', '$proveedor', $precio, '$cantidad', $usuario_id, '$imgProducto')");
                
            if($query_insert)
                {
                    if($nombre_foto != ''){
                        move_uploaded_file($nombre_url_temp, $src);
                    }
                    $alert = "<p class='msg_save'>Producto guardado correctamente</p>";
                 
                }else{
                    $alert = "<p class='msg_error'>Error al guardar el producto</p>";
                    
                }
            // mysqli_close($conection);
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
    <title>Registro Productos</title>
</head>

<body>
    <!-- Encabezado -->
    <?php include "includes/header.php"; ?>

    <!-- Menu de navegacion -->
    <?php include "includes/nav.php"?>

    <!-- Contenido de la pagina -->
    <section class="container">
        <h1><i class="fa-solid fa-cart-plus" style="color: #74C0FC;"></i> Registro Productos</h1>
        <hr>
        <div class="alert"><?php echo(isset($alert) ? $alert : '');?></div>
        <form action="" method="post" enctype="multipart/form-data" class="registrar">

            <label for="proveedor">Proveedor</label>

            <?php 
                $query_proveedor = mysqli_query($conection, "SELECT codproveedor, proveedor FROM proveedor WHERE estatus = 1 ORDER BY proveedor ASC;");

                $result_proveedor = mysqli_num_rows($query_proveedor);
                
                mysqli_close($conection);

            ?>

            <select name="proveedor" id="proveedor">

            <?php
                if($result_proveedor > 0)
                {
                    while($proveedor = mysqli_fetch_array($query_proveedor))
                    {
            ?>

                <option value="<?php echo($proveedor["codproveedor"]);?>"><?php echo($proveedor["proveedor"]);?></option>

            <?php
                    }
                }
            ?>
            </select>


            <label for="producto">Producto</label>
            <input type="text" name="producto" id="producto" placeholder="Nombre del producto">


            <label for="precio">Precio</label>
            <input type="number" name="precio" id="precio" placeholder="Precio del producto">

            <label for="cantidad">Cantidad</label>
            <input type="number" name="cantidad" id="cantidad" placeholder="Cantidad del producto">


            <div class="photo">
                <label for="foto">Foto</label>
                <div class="prevPhoto">
                    <span class="delPhoto notBlock">X</span>
                    <label for="foto"></label>
                </div>
                <div class="upimg">
                    <input type="file" name="foto" id="foto">
                </div>
                <div id="form_alert"></div>
            </div>

            <button type="submit" class="btn_save"><i class="fa-solid fa-floppy-disk" style="color: #ffffff;"></i>
                Guardar Producto</button>
        </form>
    </section>
    <!-- Footer -->
    <?php include "includes/footer.php"?>


</body>


</html>