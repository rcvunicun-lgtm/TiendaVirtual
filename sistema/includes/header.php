   <!-- Validamos si existe la sesion de lo contrario nos devolvera al login -->
   <?php
        if(empty($_SESSION["Active"])){
            header("location: ../");
        }
    ?>

    <!-- Encabezado -->
    <header>
        <a href="salir.php"><img src="../sistema/img/logOut.png" alt="salir" class="salir"></a>
        <img src="../sistema/img/user.png" alt="miFoto" class="usuario">


        <h1>Sistema Facturación</h1>
        <p class="usuario"><?php echo($_SESSION["nombre"] . "-" .$_SESSION["rol"] ); ?></p>
        <p class="separador">|</p>
        <p class="fecha"><?php echo fechaC(); ?></p>
        <p class="ubicacion">Colombia</p>
    </header>

    <!-- 
        Pasamos el formulario al archivo funciones.js dentro del elemento add_product obtenido con jquery
        ya que vamos a usar ese modal de forma generica para otras acciones 
    -->
    <div class="modal">
        <div  class="bodyModal modalCenter">

        </div>
    </div>