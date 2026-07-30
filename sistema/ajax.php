<?php
   
    // echo (var_dump($_POST));
    // echo(json_encode($_POST));
    // print_r($_POST);
   

    include "../conexion.php";
    session_start();

    if(!empty($_POST)){

        // Extraer datos del producto
        if($_POST["action"] === "infoProducto"){
            $producto_id = $_POST["producto"];
            $query = mysqli_query($conection,"SELECT codproducto, descripcion, existencia, precio FROM producto WHERE codproducto = $producto_id AND estatus = 1;");
            mysqli_close($conection);
            $result = mysqli_num_rows($query);

            if($result > 0){
                $data = mysqli_fetch_assoc($query);
                // $data = mysqli_fetch_array($query);
                echo(json_encode($data,JSON_UNESCAPED_UNICODE));
                exit;
            }
            echo ('error');
            exit;
        }

        // Agregar productos a entrada
        if($_POST["action"] === "agregarProducto"){
            // echo (var_dump($_POST));
            // echo (var_dump($_POST["formulario"]));
            $datosRecibidos = $_POST["formulario"];
            parse_str($datosRecibidos, $array);
            // echo(var_dump($array));

            if($array["action"] === "addProduct"){
               if(!empty($array["cantidad"]) || !empty($array["precio"]) || !empty($array["producto_id"]))
               {
                    $cantidad = $array["cantidad"];
                    $precio = $array["precio"];
                    $producto_id = $array["producto_id"];
                    $usuario = $_SESSION["idUser"];

                    $query_insert = mysqli_query($conection,"INSERT INTO entradas (codproducto, cantidad, precio, usuario_id) VALUES($producto_id, $cantidad, $precio, $usuario);");
                    
                    if($query_insert)
                    {
                        //Ejecutar procedimiento almacenado
                        $query_upd = mysqli_query($conection,"CALL actualizar_precio_producto($cantidad, $precio, $producto_id)");
                        $result_pro = mysqli_num_rows($query_upd);
                        if($result_pro > 0)
                        {
                            $data = mysqli_fetch_assoc($query_upd);
                            $data['producto_id'] = $producto_id;
                            echo(json_encode($data,JSON_UNESCAPED_UNICODE));
                            exit;
                        }else{
                            echo ('error');
                        }
                    }
                    mysqli_close($conection);
                }else{
                    echo ('error');
                }
            }
            
            exit;
        }

        // Eliminar producto
        if($_POST["action"] == "eliminarProducto"){
            // echo(json_encode($_POST));
            $datosRecibidos = $_POST["formulario"];
            parse_str($datosRecibidos, $array);

            
            if($array["action"] === "delProduct"){

                if(empty($array['producto_id']) || !is_numeric($array['producto_id'])){
                    echo("Error");
                }else{
                        $idproducto = $array['producto_id'];

                        $query_delete = mysqli_query($conection,"UPDATE producto SET estatus = 0 WHERE codproducto = $idproducto;");
                        mysqli_close($conection);
            
                        if($query_delete){
                            // header("location: lista_proveedores.php");
                            echo("Producto eliminado");
                        }else{
                            echo("Error al elimnar el producto");
                            }  
                    }
              
             }
             echo("Error");
             exit;
        }

         // Extraer datos del cliente para la venta
         if($_POST["action"] === "searchCliente"){
            // echo("Buscar cliente");
            // echo(var_dump($_POST));
            // echo(json_encode($_POST));

            if(!empty($_POST['cliente'])){
                $nit = $_POST['cliente'];
                $query = mysqli_query($conection, "SELECT * FROM cliente WHERE nit LIKE $nit and estatus = 1;");
                // $query = mysqli_query($conection, "SELECT * FROM cliente WHERE nit = $nit and estatus = 1;");
                mysqli_close($conection);
                $result = mysqli_num_rows($query);
                $data = 0;
                if($result > 0){
                    $data = mysqli_fetch_assoc($query);
                }else{
                    $data = 0;
                }
                echo(json_encode($data,JSON_UNESCAPED_UNICODE));
            }
            exit;
        }

         // Registro cliente desde la pestaña Ventas
        if($_POST["action"] === "addClient"){
            // echo(var_dump($_POST));
            // echo(json_encode($_POST));
            
             // Dividir la cadena en pares clave=valor
            $pairs = explode('&', $_POST["datos"]);
            
            $array = [];
            foreach ($pairs as $pair) {
                list($key, $value) = explode('=', $pair);
                $array[$key] = $value;
            }
            
            // Mostrar el array
            // echo(json_encode($array));

            if($array["action"] == "addCliente"){
                $nit = $array["nit_cliente"];
                $nombre = $array["nom_cliente"];
                $telefono = $array["tel_cliente"];
                $direccion = $array["dir_cliente"];
                $usuario_id = $_SESSION["idUser"];

                $query_insert = mysqli_query($conection, "INSERT INTO cliente(nit, nombre, telefono, direccion, usuario_id) VALUES('$nit', '$nombre', $telefono, '$direccion', $usuario_id)");
                
                if($query_insert){
                    $codCliente = mysqli_insert_id($conection);
                    $msg = $codCliente;  
                }else{
                    $msg = "Error al registrar el cliente";
                }
                mysqli_close($conection);
                echo($msg);
            }
            
            exit;
        }

        // Agregar producto al detalle temporal
        if($_POST["action"] == "addProductDetalle"){
            // echo(var_dump($_POST));
            if(empty($_POST["codproducto"]) || empty($_POST["cantidad"])){
                echo("error campos vacios");
            }else{
                $codproducto = $_POST["codproducto"];
                $cantidad = $_POST["cantidad"];
                $token = md5($_SESSION["idUser"]);

                $query_iva = mysqli_query($conection, "SELECT iva FROM configuracion");
                $result_iva = mysqli_num_rows($query_iva);

                $query_detalle_temp = mysqli_query($conection,"CALL add_detalle_temp($codproducto, $cantidad, '$token');");
                $result = mysqli_num_rows($query_detalle_temp);

                $detalleTabla = '';
                $sub_total = 0;
                $iva = 0;
                $total = 0;
                $arrayData = array();

                if($result > 0){
                    if($result_iva > 0){
                        $info_iva = mysqli_fetch_assoc($query_iva);
                        $iva = $info_iva["iva"];
                    }

                    while($data = mysqli_fetch_assoc($query_detalle_temp)){
                        $precioTotal = round($data['cantidad'] * $data['precio_venta'], 2);
                        $sub_total = round($sub_total + $precioTotal,2);
                        $total = round($total + $precioTotal,2);

                        $detalleTabla .= '
                            <tr>
                                <td>'.$data["codproducto"].'</td>
                                <td colspan="2">'.$data["descripcion"].'</td>
                                <td class="textcenter">'.$data["cantidad"].'</td>
                                <td class="textcenter">'.$data["precio_venta"].'</td>
                                <td class="textcenter">'.$precioTotal.'</td>
                                <td class="">
                                    <a href="#" class="link_delete" onclick="event.preventDefault(); del_product_detalle('.$data["correlativo"].');"><i class="fa-solid fa-trash-can"></i></a>
                                </td>
                            </tr>
                        ';
                    }

                    $impuesto = round($sub_total * ($iva / 100), 2);
                    $tl_sniva = round($sub_total - $impuesto, 2);
                    $total = round($tl_sniva + $impuesto, 2);

                    $detalleTotales = '
                        <tr>
                            <td colspan="5" class="textright">SUBTOTAL.Q</td>
                            <td class="textright">'.$tl_sniva.'</td>
                        </tr>
                        <tr>
                            <td colspan="5" class="textright">IVA ('.$iva.')</td>
                            <td class="textright">'.$impuesto.'</td>
                        </tr>
                        <tr>
                            <td colspan="5" class="textright">TOTAL Q.</td>
                            <td class="textright">'.$total.'</td>
                        </tr>
                    ';
                
                    $arrayData['detalle'] = $detalleTabla;
                    $arrayData['totales'] = $detalleTotales;

                    echo(json_encode($arrayData,JSON_UNESCAPED_UNICODE));
                }else{
                    echo('Hubo un error al procesar campos');
                }
                mysqli_close($conection);
            }
            exit;
        }


          // Extrae datos del detalle temp
          if($_POST["action"] == "searchForDetalle"){
            // echo(var_dump($_POST));
            if(empty($_POST["user"])){
                echo("error campos vacios");
            }else{
             
                $token = md5($_SESSION["idUser"]);

                $query = mysqli_query($conection, "SELECT tmp.correlativo, tmp.token_user, tmp.cantidad, tmp.precio_venta, p.codproducto, p.descripcion FROM detalle_temp AS tmp INNER JOIN producto AS p ON tmp.codproducto = p.codproducto WHERE token_user = '$token';");
                $result = mysqli_num_rows($query);

                $query_iva = mysqli_query($conection, "SELECT iva FROM configuracion");
                $result_iva = mysqli_num_rows($query_iva);

               

                $detalleTabla = '';
                $sub_total = 0;
                $iva = 0;
                $total = 0;
                $arrayData = array();

                if($result > 0){
                    if($result_iva > 0){
                        $info_iva = mysqli_fetch_assoc($query_iva);
                        $iva = $info_iva["iva"];
                    }

                    while($data = mysqli_fetch_assoc($query)){
                        $precioTotal = round($data['cantidad'] * $data['precio_venta'], 2);
                        $sub_total = round($sub_total + $precioTotal,2);
                        $total = round($total + $precioTotal,2);

                        $detalleTabla .= '
                            <tr>
                                <td>'.$data["codproducto"].'</td>
                                <td colspan="2">'.$data["descripcion"].'</td>
                                <td class="textcenter">'.$data["cantidad"].'</td>
                                <td class="textcenter">'.$data["precio_venta"].'</td>
                                <td class="textcenter">'.$precioTotal.'</td>
                                <td class="">
                                    <a href="#" class="link_delete" onclick="event.preventDefault(); del_product_detalle('.$data["correlativo"].');"><i class="fa-solid fa-trash-can"></i></a>
                                </td>
                            </tr>
                        ';
                    }

                    $impuesto = round($sub_total * ($iva / 100), 2);
                    $tl_sniva = round($sub_total - $impuesto, 2);
                    $total = round($tl_sniva + $impuesto, 2);

                    $detalleTotales = '
                        <tr>
                            <td colspan="5" class="textright">SUBTOTAL.Q</td>
                            <td class="textright">'.$tl_sniva.'</td>
                        </tr>
                        <tr>
                            <td colspan="5" class="textright">IVA ('.$iva.')</td>
                            <td class="textright">'.$impuesto.'</td>
                        </tr>
                        <tr>
                            <td colspan="5" class="textright">TOTAL Q.</td>
                            <td class="textright">'.$total.'</td>
                        </tr>
                    ';
                
                    $arrayData['detalle'] = $detalleTabla;
                    $arrayData['totales'] = $detalleTotales;

                    echo(json_encode($arrayData,JSON_UNESCAPED_UNICODE));
                }else{
                    echo('Hubo un error al procesar campos');
                }
                mysqli_close($conection);
            }
            exit;
        }

         // Eliminar datos del detalle temp con procedimiento almacenado
        if($_POST["action"] == "delProductoDetalle"){
             // echo(var_dump($_POST));
             if(empty($_POST["id_detalle"])){
                echo("error campos vacios");
            }else{
             
                $id_detalle = $_POST["id_detalle"];
                $token = md5($_SESSION["idUser"]);

                $query_iva = mysqli_query($conection, "SELECT iva FROM configuracion");
                $result_iva = mysqli_num_rows($query_iva);

                $query_detalle_temp = mysqli_query($conection,"CALL del_detalle_temp($id_detalle, '$token');");
                $result = mysqli_num_rows($query_detalle_temp);

                $detalleTabla = '';
                $sub_total = 0;
                $iva = 0;
                $total = 0;
                $arrayData = array();

                if($result > 0){
                    if($result_iva > 0){
                        $info_iva = mysqli_fetch_assoc($query_iva);
                        $iva = $info_iva["iva"];
                    }

                    while($data = mysqli_fetch_assoc($query_detalle_temp)){
                        $precioTotal = round($data['cantidad'] * $data['precio_venta'], 2);
                        $sub_total = round($sub_total + $precioTotal,2);
                        $total = round($total + $precioTotal,2);

                        $detalleTabla .= '
                            <tr>
                                <td>'.$data["codproducto"].'</td>
                                <td colspan="2">'.$data["descripcion"].'</td>
                                <td class="textcenter">'.$data["cantidad"].'</td>
                                <td class="textcenter">'.$data["precio_venta"].'</td>
                                <td class="textcenter">'.$precioTotal.'</td>
                                <td class="">
                                    <a href="#" class="link_delete" onclick="event.preventDefault(); del_product_detalle('.$data["correlativo"].');"><i class="fa-solid fa-trash-can"></i></a>
                                </td>
                            </tr>
                        ';
                    }

                    $impuesto = round($sub_total * ($iva / 100), 2);
                    $tl_sniva = round($sub_total - $impuesto, 2);
                    $total = round($tl_sniva + $impuesto, 2);

                    $detalleTotales = '
                        <tr>
                            <td colspan="5" class="textright">SUBTOTAL.Q</td>
                            <td class="textright">'.$tl_sniva.'</td>
                        </tr>
                        <tr>
                            <td colspan="5" class="textright">IVA ('.$iva.')</td>
                            <td class="textright">'.$impuesto.'</td>
                        </tr>
                        <tr>
                            <td colspan="5" class="textright">TOTAL Q.</td>
                            <td class="textright">'.$total.'</td>
                        </tr>
                    ';
                
                    $arrayData['detalle'] = $detalleTabla;
                    $arrayData['totales'] = $detalleTotales;

                    echo(json_encode($arrayData,JSON_UNESCAPED_UNICODE));
                }else{
                    echo('Hubo un error al procesar campos');
                }
                mysqli_close($conection);
            }
            exit;
        }
      
        // Anular venta

        if($_POST["action"] == "anularVenta"){
            
            $token = md5($_SESSION["idUser"]);
            $query_del = mysqli_query($conection,"DELETE FROM detalle_temp WHERE token_user = '$token'");
            mysqli_close($conection);
            if($query_del){
                echo("ok");
            }else{
                echo("error");
            }
            exit;
        }

        // Procesar /Facturar venta
        if($_POST["action"] == "procesarVenta"){
            
            if(empty($_POST["codCliente"])){
                $codCliente = 1;
            }else{
                $codCliente = $_POST["codCliente"];
            }

            $token = md5($_SESSION['idUser']);
            $usuario = $_SESSION['idUser'];

            $query = mysqli_query($conection, "SELECT * FROM detalle_temp WHERE token_user = '$token';");
            $result = mysqli_num_rows($query);

            if($result > 0){
                $query_procesar = mysqli_query($conection, "CALL procesar_venta($usuario, $codCliente, '$token');");
                $result_detalle = mysqli_num_rows($query_procesar);

                if($result_detalle > 0){
                   $data = mysqli_fetch_assoc($query_procesar);
                   echo(json_encode($data,JSON_UNESCAPED_UNICODE));
                }else{
                    echo('Hubo un error al procesar campos');
                }
            }else{
                echo('No hay productos en el carrito');
            }
            exit;
        }

        if($_POST["action"] == "infoFactura"){
            // print_r($_POST);
            if(!empty($_POST['nofactura'])){
                
                $nofactura = $_POST['nofactura'];
                
                $query = mysqli_query($conection, "SELECT * FROM factura WHERE nofactura = $nofactura AND estatus = 1;");
                
                mysqli_close($conection);

                $result = mysqli_num_rows($query);

                if($result > 0){

                    $data = mysqli_fetch_assoc($query);

                    echo(json_encode($data, JSON_UNESCAPED_UNICODE));
                }
            }else{
                echo('error');
            }
            exit;
        }

        if($_POST["action"] == "anularFactura"){
            // print_r($_POST);
            if(!empty($_POST["noFactura"])){

                $noFactura = $_POST["noFactura"];
                $query_anular = mysqli_query($conection,"CALL anular_factura($noFactura);");

                mysqli_close($conection);

                $result = mysqli_num_rows($query_anular);

                if($result > 0){
                    $data = mysqli_fetch_assoc($query_anular);
                    echo(json_encode($data,JSON_UNESCAPED_UNICODE));
                }else{
                    echo('error');
                }
        
            } else{
                echo('error');
            }  
        exit;
        }

        // Cambiar contraseña
        if($_POST["action"] == "changePassword"){
            // print_r($_POST);
            if(!empty($_POST["passActual"]) && !empty($_POST["passNuevo"])){
                $password = md5($_POST["passActual"]);
                $newPass = md5($_POST["passNuevo"]);
                $idUser = $_SESSION['idUser'];

                $code = '';
                $msg = '';
                $arrData = array();

                $query_user = mysqli_query($conection, "SELECT * FROM usuario WHERE clave = '$password' AND idusuario = '$idUser';");

                $result = mysqli_num_rows($query_user);
                if($result > 0){
                    $query_update = mysqli_query($conection, "UPDATE usuario SET clave = '$newPass' WHERE idusuario = $idUser;");
                    mysqli_close($conection);
                    if($query_update){
                        $code = '00';
                        $msg = "Su contraseña se ha actualizado con exito.";
                    }else{
                        $code = '2';
                        $msg = "No fue posible cambiar su contraseña.";
                    }
                }else{
                    $code = '1';
                    $msg = "La contraseña actual es incorrecta.";
                    mysqli_close($conection);
                }

                $arrData = array('cod'=> $code, 'msg'=>$msg);
                echo(json_encode($arrData, JSON_UNESCAPED_UNICODE));
            }else{
                echo("error");
            }
            exit;
        }

        // Actualizar datos de la empresa
        if($_POST["action"] == "updateDataEmpresa"){
    
                $code = '';
                $msg = '';
                $arrData = array();

               // Dividir la cadena en pares clave=valor
               $pairs = explode('&', $_POST["data"]);
            
               $array = [];
               foreach ($pairs as $pair) {
                   list($key, $value) = explode('=', $pair);
                   $array[$key] = $value;
               }

               if($array["action"] === "updateDataEmpresa"){
              
                if(empty($array['txtNit']) || empty($array['txtNombre']) || empty($array['txtRSocial']) || empty($array['txtTelEmpresa']) || empty($array['txtEmailEmpresa']) || empty($array['txtDirEmpresa']) || empty($array['txtIva'])){
                    $code = '1';
                    $msg = 'Todos los campos son obligatorios.';
                }else{
                    
                    /*
                     La funcion urldecode la debemos utilizar si nos llegan a aparecer caracteres extraños, por ejemplo en los 
                     arroas @ o en los espacios en blanco
                    */

                    $intNit = urldecode(intval($array['txtNit']));
                    $strNomEmpresa = urldecode($array['txtNombre']);
                    $strRazonSocial = urldecode($array['txtRSocial']);
                    $intTelEmpresa =  urldecode(intval($array['txtTelEmpresa']));
                    $strEmailEmpresa =  urldecode($array['txtEmailEmpresa']);
                    $strDirEmpresa =  urldecode($array['txtDirEmpresa']);
                    $strIva = urldecode($array['txtIva']);
                    
                    $query_update = mysqli_query($conection, "UPDATE configuracion SET nit = $intNit, nombre = '$strNomEmpresa', razon_social = '$strRazonSocial', telefono = $intTelEmpresa, email = '$strEmailEmpresa', direccion = '$strDirEmpresa', iva = '$strIva' WHERE id = 1;");
                    mysqli_close($conection);

                    if($query_update){
                        $code = '00';
                        $msg = 'Datos actualizados correctamente.'; 
                    }else{
                        $code = '2';
                        $msg = 'Error al actualizar los datos.'; 
                    }
                } 
                $arrData = array('cod'=> $code, 'msg'=> $msg);
                echo(json_encode($arrData, JSON_UNESCAPED_UNICODE));
               }
          
            exit;
        }
        
    }
    exit;

?>