<?php 
  session_start(); 
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <!-- Incrustamos el archivo scripts.php -->
    <?php include "includes/scripts.php"?>
    <title>Document</title>
</head>

<body>


    <?php
      include "../conexion.php";  

      $nit = '';
      $nomEmpresa = '';
      $razonSocial = '';
      $telEmpresa = '';
      $emailEmpresa = '';
      $dirEmpresa = '';
      $iva = '';

      $query_empresa = mysqli_query($conection, "SELECT * FROM configuracion;");
      $row_empresa = mysqli_num_rows($query_empresa);
      if($row_empresa > 0){
        while($arrInfoEmpresa = mysqli_fetch_assoc($query_empresa)){
          $nit = $arrInfoEmpresa['nit'];
          $nomEmpresa = $arrInfoEmpresa['nombre'];
          $razonSocial = $arrInfoEmpresa['razon_social'];
          $telEmpresa = $arrInfoEmpresa['telefono'];
          $emailEmpresa = $arrInfoEmpresa['email'];
          $dirEmpresa = $arrInfoEmpresa['direccion'];
          $iva = $arrInfoEmpresa['iva'];
        }
      }
      mysqli_close($conection);

      include "../conexion.php";  
      $query_dash = mysqli_query($conection,"CALL dataDashboard();");
      $num_rows = mysqli_num_rows($query_dash);
      if($num_rows > 0){
        $data = mysqli_fetch_assoc($query_dash);
      }
      mysqli_close($conection);
    ?>
  
    <!-- Encabezado -->
    <?php include "includes/header.php"; ?>

    <!-- Menu de navegacion -->
    <?php include "includes/nav.php"?>

    <!-- Contenido de la pagina -->
    <section id="container">
      <div class="divContainer">
        <div>
          <h1 class="titlePanelControl">Panel de control</h1>
        </div>
        <div class="dashboard">

          <?php
            if($_SESSION["rol"] != 3){
          ?>
          <a href="lista_usuarios.php">
            <i class="fa-solid fa-users"></i>
            <p>
              <strong>Usuarios</strong><br>
              <span><?=$data["usuarios"]?></span>
            </p>
          </a>

          <?php
            }
          ?>

          <a href="lista_clientes.php">
            <i class="fa-solid fa-users"></i>
            <p>
              <strong>Clientes</strong><br>
              <span><?=$data["clientes"]?></span>
            </p>
          </a>

          <?php
            if($_SESSION["rol"] != 3){
          ?>
          <a href="lista_proveedores.php">
            <i class="fa-solid fa-users"></i>
            <p>
              <strong>Proveedores</strong><br>
              <span><?=$data["proveedores"]?></span>
            </p>
          </a>
          <?php
           }
          ?>

          <a href="lista_producto.php">
            <i class="fa-solid fa-users"></i>
            <p>
              <strong>Productos</strong><br>
              <span><?=$data["productos"]?></span>
            </p>
          </a>

          <a href="lista_factura.php">  
            <i class="fa-solid fa-users"></i>
            <p>
              <strong>Ventassss</strong><br>
              <span><?=$data["ventas"]?></span>
            </p>
          </a>

        </div>
      </div>

      <div class="infoSistema">
        <div>
          <h1 class="titlePanelControl">Configuración</h1>
        </div>
        <div class="containerPerfil">
          <div class="containerDataUser">
            <div class="logoUser">
              <img src="img/user.png" alt="Usuario">
            </div>
            <div class="divDataUser">
              <h4>Informacion Personal</h4>

              <div>
                <label for="">Nombre: </label> <span><?=$_SESSION["nombre"]?></span>
              </div>

              <div>
                <label for="">Correo: </label> <span><?=$_SESSION["email"]?></span>
              </div>

              <h4>Datos Usuario</h4>
              <div>
                <label for="">Rol: </label> <span><?=$_SESSION["rol"]; echo(" - "); echo($_SESSION["rol_name"]);?></span>
              </div>
              <div>
                <label for="">Usuario: </label> <span><?=$_SESSION["user"]?></span>
              </div>
              <h4>Cambiar contraseña</h4>
              <form action="" method="post" name="frmChangePass" id="frmChangePass">
                <div>
                  <input type="password" name="txtPassUser" id="txtPassUser" placeholder="Contraseña Acutal" required>
                </div>

                <div>
                  <input type="password" class="newPass" name="txtNewPassUser" id="txtNewPassUser" placeholder="Nueva Contraseña" required>
                </div>

                <div>
                  <input type="password" class="newPass" name="txtPassConfirm" id="txtPassConfirm" placeholder="Confirmar Contraseña" required>
                </div>
                <div class="alertChangePass" style="display:none;">

                </div>
                <div>
                  <button class="btn_save btnChangePass"><i class="fa-solid fa-pen-clip"></i> Cambiar contraseña</button>
                </div>
              </form>
            </div>
          </div>
          <?php
            if($_SESSION["rol"] == 1){
          ?>
          <div class="containerDataEmpresa">
            <div class="logoEmpresa">
              <img src="img/empresa.png" alt="Usuario" width="100px">
            </div>
            <h4>Datos de la empresa</h4>

            <form action="" method="post" name="frmEmpresa" id="frmEmpresa">

                <input type="hidden" name="action" value="updateDataEmpresa">

                <div>
                  <label for="">Nit:</label>
                  <input type="text" name="txtNit" id="txtNit" placeholder="Nit de la empresa" value="<?php echo($nit);?>" required>
                </div>

                <div>
                  <label for="">Nombre:</label>
                  <input type="text" name="txtNombre" id="txtNombre" placeholder="Nombre de la empresa" value="<?php echo($nomEmpresa);?>" required>
                </div>

                <div>
                  <label for="">Razon social:</label>
                  <input type="text" name="txtRSocial" id="txtRSocial" placeholder="Razon social" value="<?php echo($razonSocial);?>" required>
                </div>

                <div>
                  <label for="">Teléfono:</label>
                  <input type="text" name="txtTelEmpresa" id="txtTelEmpresa" placeholder="Numero de la empresa" value="<?php echo($telEmpresa);?>" required>
                </div>

                 <div>
                  <label for="">Correo Electronico:</label>
                  <input type="text" name="txtEmailEmpresa" id="txtEmailEmpresa" placeholder="Correo electronico" value="<?php echo($emailEmpresa);?>" required>
                </div>

                <div>
                  <label for="">Dirección:</label>
                  <input type="text" name="txtDirEmpresa" id="txtDirEmpresa" placeholder="Dirección de la empresa" value="<?php echo($dirEmpresa);?>" required>
                </div>

                <div>
                  <label for="">IVA (%):</label>
                  <input type="text" name="txtIva" id="txtIva" placeholder="Impuesto al valor agregado" value="<?php echo($iva);?>" required>
                </div>

                <div class="alertFormEmpresa" style="display:none;"></div>

                <div>
                  <button class="btn_save btnChangePass"><i class="fa-solid fa-floppy-disk"></i> Guardar datos</button>
                </div>
            </form>
          </div>
          <?php
           }
          ?>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <?php include "includes/footer.php"?>

</body>

</html>