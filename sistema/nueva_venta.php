<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Incrustamos el archivo scripts.php -->
    <?php include "../sistema/includes/scripts.php"?>
    <title>Nueva venta</title>
</head>
<body>

    <!-- Encabezado -->
    <?php include "includes/header.php"; ?>

     <!-- Menu de navegacion -->
     <?php include "includes/nav.php"?>

      <!-- Contenido de la pagina -->
    <section class="container_list_user">
        <div class="title_page">
            <h1><i class="fa-solid fa-cart-shopping"></i> Nueva venta</h1>
        </div>

        <div class="datos_cliente">
            <div class="action_cliente">
                <h4>Datos del cliente</h4>
                <a href="#" class="btn_new btn_new_cliente"><i class="fa-solid fa-person-circle-plus"></i> Nuevo</a>
            </div>
            <form name="form_new_cliente_venta" id="form_new_cliente_venta" class="datos">
                <input type="hidden" name="action" value="addCliente">
                <input type="hidden" id="idcliente" name="idcliente" value="" required>

                <div class="wd30">
                    <label for="">Nit</label>
                    <input type="text" name="nit_cliente" id="nit_cliente">
                </div>

                <div class="wd30">
                    <label for="">Nombre</label>
                    <input type="text" name="nom_cliente" id="nom_cliente" disabled required>
                </div>

                <div class="wd30">
                    <label for="">Teléfono</label>
                    <input type="text" name="tel_cliente" id="tel_cliente" disabled required>
                </div>

                <div class="wd100">
                    <label for="">Dirección</label>
                    <input type="text" name="dir_cliente" id="dir_cliente" disabled required>
                </div>

                <div id="div_registro_cliente" class="wd100">
                    <button type="submit" class="btn_save"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
                </div>
            </form>
        </div>

        <div class="datos_venta">
            <h4>Datos de venta</h4>
            <div class="datos">
                <div class="wd50">
                    <label for="">Vendedor</label>
                    <p><?php echo($_SESSION['nombre']);?></p>
                </div>
                <div class="wd70">
                    <label for="" style="display:block;">Acciones</label>
                    <div id="acciones_venta">
                        <a href="#" class="btn_anular txtcenter" id="btn_anular_venta"><i class="fa-solid fa-ban"></i> Anular</a>
                        <a href="#" class="btn_new textcenter" id="btn_facturar_venta" style="display:none;"><i class="fa-solid fa-circle-check"></i> Procesar</a>
                    
                    </div>
                </div>
            </div>
        </div>

        <table class="tbl_venta">
            <thead>
                <tr>
                    <th class="wd30">Código</th>
                    <th>Descripción</th>
                    <th>Existencia</th>
                    <th class="wd100">Cantidad</th>
                    <th class="textright">Precio</th>
                    <th class="textright">Precio Total</th>
                    <th>Acción</th>
                </tr>
                <tr>
                    <td><input type="text" name="txt_cod_producto" id="txt_cod_producto"></td>
                    <td id="txt_descripcion">-</td>
                    <td id="txt_existencia">-</td>
                    <td><input type="text" name="txt_cant_producto" id="txt_cant_producto" value="0" min="1" disabled></td>
                    <td id="txt_precio" class="txtright">0.00</td>
                    <td id="txt_precio_total" class="txtright">0.00</td>
                    <td><a href="#" id="add_product_venta" class="link_add"><i class="fa-solid fa-cart-plus"></i> Agregar</a></td>
                </tr>
                <tr>
                    <th>Código</th>
                    <th colspan="2" class="wd60">Descripción</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Precio Total</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="detalle_venta">
                <!-- Contenido Ajax -->
            </tbody>
            <tfoot id="detalle_totales">
                <!-- Contenido AJAX -->
            </tfoot>
        </table>
    </section>

    <!-- Footer -->
    <?php include "includes/footer.php"?>
   
    <script type="text/javascript">
        $(document).ready(function(){
            let usuarioid = '<?php echo($_SESSION['idUser']); ?>';
            searchForDetalle(usuarioid);

            /* 
            La funcion viewProcesar() la estoy llamando dentro de la funcion searchForDetalle() del arhcivo funciones.js 
            Esto debido a que la funcion es asincrona, y si llamo a esta funcion viewProcesar() directamente en este archivo 
            esa funcion parecera que no esta funcionando pero en realidad si funcionara, 
            solo que como se ejecuta al mismo tiempo que la funcion searchForDetalle() pues el boton no enontrara
            las etiquetas tr y por eso no muestrara el contenido, una forma de llamar a la funcion viewProcesar() aqui es
            haciendo que la programacion no sea asincrona osea colocando como valor false en el ajax de la funcion viewProcesar()
            esto hara que hasta que el ajax no retorne los datos la funcion viewProcesar() no se ejecutara.
            */
            // viewProcesar();
        });
    </script>
</body>
</html>