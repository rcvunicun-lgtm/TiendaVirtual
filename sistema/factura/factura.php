<?php
	// session_start();
	$subtotal 	= 0;
	$iva 	 	= 0;
	$impuesto 	= 0;
	$tl_sniva   = 0;
	$total 		= 0;
 //print_r($configuracion); 
 
 
// Para incrustar imagenes en el archivo PDF debemos Convertir las imagen a base64

// NOTA SUPER IMPORTANTE: Al utilizar la libreria de Dompdf debemos asegurarnos que todas las imagenes que incrustemos esten en formato jpg, los demas formatos de imagenes no los acepta
// Almenos para la version que se esta utilizando actualmente.

 // Ruta de tu imagen
// $imagePath = '../../img/tigre.jpg'; 
$imagePath = '../../img/hoja.jpg';
$imageData = base64_encode(file_get_contents($imagePath));
$src = 'data:image/png;base64,'.$imageData;

/*

tengo una duda porque base 64

ChatGPT Plus
ChatGPT
Usar imágenes en formato base64 tiene varias ventajas en casos como el tuyo, especialmente cuando trabajas con librerías como Dompdf. Aquí te explico las razones principales:

1. Elimina problemas con rutas:
Al convertir una imagen a base64, la imagen se convierte en una cadena de texto que se puede incrustar directamente en el código HTML. Esto significa que no necesitas preocuparte por las rutas de archivos, permisos de archivos o la disponibilidad de imágenes en el servidor.
En Dompdf, las rutas relativas o absolutas pueden causar problemas dependiendo de la configuración del servidor. Incrustar la imagen en base64 elimina esta complicación, ya que la imagen viaja junto con el HTML.
2. Compatibilidad con Dompdf:
Dompdf tiene algunas limitaciones en cuanto al manejo de imágenes. Aunque soporta imágenes locales y remotas, a veces puede fallar en su carga por restricciones de seguridad (como CORS para imágenes remotas), problemas de acceso o incluso formatos no soportados (como GIF o SVG).
Base64 asegura que la imagen será correctamente procesada y renderizada por Dompdf, independientemente de su origen o ubicación.
3. Portabilidad:
Al usar base64, toda la información de la imagen está contenida dentro del documento PDF o del HTML, lo que lo hace más portátil. No importa si el PDF o el HTML se comparte con alguien más, ya que no dependerá de imágenes externas para mostrarse correctamente.
4. Evita problemas de permisos de archivos:
Si el servidor no tiene los permisos correctos para acceder a imágenes locales, Dompdf no podrá mostrarlas en el PDF. Convertir la imagen a base64 hace que este problema desaparezca, ya que no se necesita acceso al sistema de archivos después de convertir la imagen.
5. No requiere conexión a Internet:
Si estás utilizando imágenes remotas (por ejemplo, desde un servidor externo), la conexión a Internet es necesaria para cargarlas. Con base64, la imagen ya está integrada, por lo que no dependerás de una conexión externa para mostrar la imagen.
Desventajas del uso de base64:
Tamaño del archivo: Las imágenes en base64 suelen ser más grandes que las imágenes en su formato original, ya que la conversión a texto aumenta el tamaño de los datos.
Rendimiento: Para documentos que incluyen muchas imágenes grandes, la conversión a base64 puede aumentar el tamaño del PDF y, por lo tanto, afectar su rendimiento al cargar o renderizar.
En resumen, usar imágenes en base64 en tu caso específico con Dompdf simplifica el manejo de imágenes y asegura que siempre se rendericen correctamente en el PDF, sin problemas de rutas o permisos.
*/


 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Factura</title>
    <link rel="stylesheet" href="style.css">

	<!--
		Como vemos en la cabecera dentro del head hemos definido una etiqueta link para asociar los estilos externos css
		del archivo style.css, sin embargo a la hora de generar la factura estos estilos no son tomados en cuenta asi que
		para que los estilos sean tomados en cuenta debemos establecer los estilos en el mismo documento HTML
	
	-->
	<style>
		@import url('fonts/BrixSansRegular.css');
		@import url('fonts/BrixSansBlack.css');

		*{
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}
		p, label, span, table{
			font-family: 'BrixSansRegular';
			font-size: 9pt;
		}
		.h2{
			font-family: 'BrixSansBlack';
			font-size: 16pt;
		}
		.h3{
			font-family: 'BrixSansBlack';
			font-size: 12pt;
			display: block;
			background: #0a4661;
			color: #FFF;
			text-align: center;
			padding: 3px;
			margin-bottom: 5px;
		}
		#page_pdf{
			width: 95%;
			margin: 15px auto 10px auto;
		}

		#factura_head, #factura_cliente, #factura_detalle{
			width: 100%;
			margin-bottom: 10px;
		}
		.logo_factura{
			width: 25%;
		}
		.info_empresa{
			width: 50%;
			text-align: center;
		}
		.info_factura{
			width: 25%;
		}
		.info_cliente{
			width: 100%;
		}
		.datos_cliente{
			width: 100%;
		}
		.datos_cliente tr td{
			width: 50%;
		}
		.datos_cliente{
			padding: 10px 10px 0 10px;
		}
		.datos_cliente label{
			width: 75px;
			display: inline-block;
		}
		.datos_cliente p{
			display: inline-block;
		}

		.textright{
			text-align: right;
		}
		.textleft{
			text-align: left;
		}
		.textcenter{
			text-align: center;
		}
		.round{
			border-radius: 10px;
			border: 1px solid #0a4661;
			overflow: hidden;
			padding-bottom: 15px;
		}
		.round p{
			padding: 0 15px;
		}

		#factura_detalle{
			border-collapse: collapse;
		}
		#factura_detalle thead th{
			background: #058167;
			color: #FFF;
			padding: 5px;
		}
		#detalle_productos tr:nth-child(even) {
			background: #ededed;
		}
		#detalle_totales span{
			font-family: 'BrixSansBlack';
		}
		.nota{
			font-size: 8pt;
		}
		.label_gracias{
			font-family: verdana;
			font-weight: bold;
			font-style: italic;
			text-align: center;
			margin-top: 20px;
		}
		.anulada{
			width: 80%;
			position: absolute;
			left: 50%;
			top: 50%;
			transform: translateX(-50%) translateY(-50%);
			z-index: -1;
		}
	</style>
</head>
<body>

<?php 

// Si la venta fue anulada entonces debemos colocar la imagen de marca de agua anulada, y para eso primero debemos convertir esa imagen a base 64
if($anulada != ''){
	$imagePathanulada = $anulada;
	$imageDataAnulada = base64_encode(file_get_contents($imagePathanulada));
	$srcAnulada = 'data:image/png;base64,'.$imageDataAnulada;
}


?>
<img class="anulada" src="<?php echo($srcAnulada) ?>" alt="Anulada">

<div id="page_pdf">
	<table id="factura_head">
		<tr>
			<td class="logo_factura">
				<div>
					<!-- <img src="../factura/img/logo.png"> -->
					<img src="<?php echo($src); ?>" alt="Anulada" width="200px">
				</div>
			</td>
			<td class="info_empresa">
				<?php
					if($result_config > 0){
						$iva = $configuracion['iva'];
				 ?>
				<div>
					<span class="h2"><?php echo strtoupper($configuracion['nombre']); ?></span>
					<p><?php echo $configuracion['razon_social']; ?></p>
					<p><?php echo $configuracion['direccion']; ?></p>
					<p>NIT: <?php echo $configuracion['nit']; ?></p>
					<p>Teléfono: <?php echo $configuracion['telefono']; ?></p>
					<p>Email: <?php echo $configuracion['email']; ?></p>
				</div>
				<?php
					}
				 ?>
			</td>
			<td class="info_factura">
				<div class="round">
					<span class="h3">Factura</span>
					<p>No. Factura: <strong><?php echo $factura['nofactura']; ?></strong></p>
					<p>Fecha: <?php echo $factura['fecha']; ?></p>
					<p>Hora: <?php echo $factura['hora']; ?></p>
					<p>Vendedor: <?php echo $factura['vendedor']; ?></p>
				</div>
			</td>
		</tr>
	</table>
	<table id="factura_cliente">
		<tr>
			<td class="info_cliente">
				<div class="round">
					<span class="h3">Cliente</span>
					<table class="datos_cliente">
						<tr>
							<td><label>Nit:</label><p><?php echo $factura['nit']; ?></p></td>
							<td><label>Teléfono:</label> <p><?php echo $factura['telefono']; ?></p></td>
						</tr>
						<tr>
							<td><label>Nombre:</label> <p><?php echo $factura['nombre']; ?></p></td>
							<td><label>Dirección:</label> <p><?php echo $factura['direccion']; ?></p></td>
						</tr>
					</table>
				</div>
			</td>

		</tr>
	</table>

	<table id="factura_detalle">
			<thead>
				<tr>
					<th width="50px">Cant.</th>
					<th class="textleft">Descripción</th>
					<th class="textright" width="150px">Precio Unitario.</th>
					<th class="textright" width="150px"> Precio Total</th>
				</tr>
			</thead>
			<tbody id="detalle_productos">

			<?php

				if($result_detalle > 0){

					while ($row = mysqli_fetch_assoc($query_productos)){
			 ?>
				<tr>
					<td class="textcenter"><?php echo $row['cantidad']; ?></td>
					<td><?php echo $row['descripcion']; ?></td>
					<td class="textright"><?php echo $row['precio_venta']; ?></td>
					<td class="textright"><?php echo $row['precio_total']; ?></td>
				</tr>
			<?php
						$precio_total = $row['precio_total'];
						$subtotal = round($subtotal + $precio_total, 2);
					}
				}

				$impuesto 	= round($subtotal * ($iva / 100), 2);
				$tl_sniva 	= round($subtotal - $impuesto,2 );
				$total 		= round($tl_sniva + $impuesto,2);
			?>
			</tbody>
			<tfoot id="detalle_totales">
				<tr>
					<td colspan="3" class="textright"><span>SUBTOTAL Q.</span></td>
					<td class="textright"><span><?php echo $tl_sniva; ?></span></td>
				</tr>
				<tr>
					<td colspan="3" class="textright"><span>IVA (<?php echo $iva; ?> %)</span></td>
					<td class="textright"><span><?php echo $impuesto; ?></span></td>
				</tr>
				<tr>
					<td colspan="3" class="textright"><span>TOTAL Q.</span></td>
					<td class="textright"><span><?php echo $total; ?></span></td>
				</tr>
		</tfoot>
	</table>
	<div>
		<p class="nota">Si usted tiene preguntas sobre esta factura, <br>pongase en contacto con nombre, teléfono y Email</p>
		<h4 class="label_gracias">¡Gracias por su compra!</h4>
	</div>

</div>

</body>
</html>