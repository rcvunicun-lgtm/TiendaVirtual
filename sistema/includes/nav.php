 <!-- Menu de navegacion -->
 <nav>
        <ul>
            <!-- <li><a href="#home">Home</a></li>
            <li><a href="#news">News</a></li> -->
            <li class="dropdown">
                <a href="../index.php" class="dropbtn"><i class="fa-solid fa-house" style="color: #ffffff;"></i>INICIO</a>
                <!-- <div class="dropdown-content">
                    <a href="#">Link 1</a>
                    <a href="#">Link 2</a>
                    <a href="#">Link 3</a>
                </div> -->
            </li>
            <?php
                if($_SESSION["rol"] == 1)
                {  
            ?>
            <li class="dropdown">
                <a href="javascript:void(0)" class="dropbtn"><i class="fa-solid fa-users" style="color: #ffffff;"></i>USUARIOS</a>
                <div class="dropdown-content">
                    <a href="registro_usuario.php"><i class="fa-solid fa-user-plus" style="color: #ffffff;"></i> NUEVO USUARIO</a>
                    <a href="lista_usuarios.php"><i class="fa-solid fa-users" style="color: #ffffff;"></i> LISTA DE USUARIOS</a>
                </div>
            </li>
            <?php
                }
            ?>
            <li class="dropdown">
                <a href="javascript:void(0)" class="dropbtn"><i class="fa-solid fa-people-group" style="color: #ffffff;"></i> CLIENTES</a>
                <div class="dropdown-content">
                    <a href="registro_cliente.php"><i class="fa-solid fa-person-circle-check" style="color: #ffffff;"></i> NUEVO CLIENTE</a>
                    <a href="lista_clientes.php"><i class="fa-solid fa-people-line" style="color: #ffffff;"></i> LISTA DE CLIENTES</a>
                </div>
            </li>
            <?php
                if($_SESSION["rol"] == 1 || $_SESSION["rol"] == 2)
                {  
            ?>
            <li class="dropdown">
                <a href="javascript:void(0)" class="dropbtn"><i class="fa-solid fa-truck" style="color: #ffffff;"></i> PROVEEDORES</a>
                <div class="dropdown-content">
                    <a href="registro_proveedor.php"><i class="fa-solid fa-cart-plus" style="color: #ffffff;"></i> NUEVO PROVEEDOR</a>
                    <a href="lista_proveedores.php"><i class="fa-regular fa-id-card" style="color: #ffffff;"></i> LISTA DE PROVEEDORES</a>
                </div>
            </li>
            <?php
                } 
            ?>
            <li class="dropdown">
                <a href="javascript:void(0)" class="dropbtn"><i class="fa-solid fa-laptop-medical" style="color: #ffffff;"></i> PRODUCTOS</a>
                <div class="dropdown-content">
                    <?php
                    if($_SESSION["rol"] == 1 || $_SESSION["rol"] == 2)
                        {  
                    ?>
                    <a href="registro_producto.php"><i class="fa-solid fa-plug-circle-plus" style="color: #ffffff;"></i> NUEVO PRODUCTO</a>
                    <?php
                        }
                    ?>
                    <a href="lista_producto.php"><i class="fa-solid fa-cash-register" style="color: #ffffff;"></i> LISTA DE PRODUCTOS</a>
                </div>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0)" class="dropbtn"><i class="fa-solid fa-file-invoice-dollar" style="color: #ffffff;"></i> VENTAS</a>
                <div class="dropdown-content">
                    <a href="nueva_venta.php"><i class="fa-solid fa-list-check" style="color: #ffffff;"></i> NUEVA VENTA</a>
                    <a href="lista_factura.php"><i class="fa-solid fa-money-bill-wheat" style="color: #ffffff;"></i> LISTA DE FACTURAS</a>
                </div>
            </li>
        </ul>
    </nav>