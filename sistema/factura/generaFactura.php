<?php

	//print_r($_REQUEST);
	//exit;
	//echo base64_encode('2');
	//exit;
	session_start();
	if(empty($_SESSION['Active']))
	{
		header('location: ../../');
	}

	include "../../conexion.php";

	/*
		Nota super importante
		Para utilizar la libreria vendor no podemos solo copear las carpetas que aparecen en internet
		sino que debemos descargar e instalar composer en nuestra PC, composer es un gestor de dependecias de PHP
		
		Para instalarlo debemos:
		
			0- Verificamos si tenemos composer instalado con CMD escribiendo el comando: composer -v
			0.1 - Si no lo tenemos instalado debemos hacer los siguientes pasos:
			1- Ir a la pagina https://getcomposer.org/download/
			2- Descargar la version para windows
			3- Ejecutar el archivo .exe
			4- Seguir las instrucciones que aparecen en pantalla
				4.1 - La instalacion nos va a pedir donde tenemos el archivo php.exe (COMO NOSOTROS INSTALAMOS PHP JUNTO CON XAMPP entonces la ruta estara dentro de XAMPP: D:\Instalados\Xampp\php.php.exe)
			5- Abrimos CMD como administrador
			6- Verificamos que se haya instalado composer con el comando: composer -v
			6- Nos Dirigimos a la carpeta de nuestro proyecto donde vamos a utilizar la libreria de dompdf con (D:\Instalados\Xampp\htdocs\TiendaVirtual)
			8- Escribimos el comando (composer require dompdf/dompdf) para instalar la libreria
			9- Final mente aparecera la carpeta vendor y dentro de ella todas las dependencias para usar la libreria dompdf
	*/
	require_once '../../vendor/autoload.php';
	
	use Dompdf\Dompdf;

	if(empty($_REQUEST['cl']) || empty($_REQUEST['f']))
	{
		echo "No es posible generar la factura.";
	}else{


		$codCliente = $_REQUEST['cl'];
		$noFactura = $_REQUEST['f'];
		$anulada = '';

		$query_config   = mysqli_query($conection,"SELECT * FROM configuracion");
		$result_config  = mysqli_num_rows($query_config);
		if($result_config > 0){
			$configuracion = mysqli_fetch_assoc($query_config);
		}


		$query = mysqli_query($conection,"SELECT f.nofactura, DATE_FORMAT(f.fecha, '%d/%m/%Y') as fecha, DATE_FORMAT(f.fecha,'%H:%i:%s') as  hora, f.codcliente, f.estatus,
												 v.nombre as vendedor,
												 cl.nit, cl.nombre, cl.telefono,cl.direccion
											FROM factura f
											INNER JOIN usuario v
											ON f.usuario = v.idusuario
											INNER JOIN cliente cl
											ON f.codcliente = cl.idcliente
											WHERE f.nofactura = $noFactura AND f.codcliente = $codCliente  AND f.estatus != 10 ");

		$result = mysqli_num_rows($query);
		if($result > 0){

			$factura = mysqli_fetch_assoc($query);
			$no_factura = $factura['nofactura'];

			if($factura['estatus'] == 2){
				$anulada = '../factura/img/anulado.jpg';
			}

			$query_productos = mysqli_query($conection,"SELECT p.descripcion,dt.cantidad,dt.precio_venta,(dt.cantidad * dt.precio_venta) as precio_total
														FROM factura f
														INNER JOIN detallefactura dt
														ON f.nofactura = dt.nofactura
														INNER JOIN producto p
														ON dt.codproducto = p.codproducto
														WHERE f.nofactura = $no_factura ");
			$result_detalle = mysqli_num_rows($query_productos);

			ob_start();
		    include(dirname('__FILE__').'/factura.php');
		    $html = ob_get_clean();

			// instantiate and use the dompdf class
			$dompdf = new Dompdf();
			// $dompdf = new Dompdf(['chroot' => __DIR__]); 

			$dompdf->loadHtml($html);
			// (Optional) Setup the paper size and orientation
			$dompdf->setPaper('letter', 'portrait');
			// Render the HTML as PDF
			$dompdf->render();
			// Output the generated PDF to Browser
			$dompdf->stream('factura_'.$noFactura.'.pdf',array('Attachment'=>0));
			exit;
		}
	}

?>