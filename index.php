<?php
    $alert = "";
    session_start();

    // Verifica si la sesion esta activa, si es asi entonces me redirige a la pantalla principal del sistema
    if(!empty($_SESSION['Active'])){
        header('location: sistema/');
    }else
    {
        if(!empty($_POST))
        {
            // $alert = "hA dado clic al ingresar";
            // echo $alert;
            if(empty($_POST['usuario']) || empty($_POST['clave'])){
                $alert = "Ingrese su usuario y su clave";
            }else{
                require "conexion.php";

                // La funcion mysql_real_escape_string evita que sean ingresados caracteres extraños en el formulario
                $usuario = mysqli_real_escape_string($conection,$_POST['usuario']);

                // La funcion md5 encripta una cadena de caracteres
                $pass = md5(mysqli_real_escape_string($conection,$_POST['clave']));

                $query = mysqli_query($conection,"SELECT u.idusuario,u.nombre,u.correo,u.usuario,r.idrol,r.rol 
                FROM usuario AS u
                INNER JOIN rol AS r
                ON u.rol = r.idrol
                WHERE u.usuario= '$usuario' AND u.clave = '$pass' AND u.estatus = 1;");
                
                mysqli_close($conection);

                $result = mysqli_num_rows($query);

                if($result > 0){
                    $data = mysqli_fetch_array($query);
                
                    $_SESSION["Active"] = true;
                    $_SESSION["idUser"] = $data["idusuario"];
                    $_SESSION["nombre"] = $data["nombre"];
                    $_SESSION["email"] = $data["correo"];
                    $_SESSION["user"] = $data["usuario"];
                    $_SESSION["rol"] = $data["idrol"];
                    $_SESSION["rol_name"] = $data["rol"];

                    header('location: sistema/');
                }else{
                    $alert = "Usuario o contraseña incorrectos";
                    session_destroy();
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <title>Document</title>
</head>

<body>
    <section id="container">
        <form action="" method="post">
            <h3>INICIAR SESIÓN</h3>
            <img src="img/login.png" alt="login">

            <input type="text" name="usuario" placeholder="Usuario">
            <input type="password" name="clave" placeholder="Contraseña">
            <p> <?php echo (isset($alert) ? $alert : " "); ?> </p>
            <input type="submit" value="INGRESAR">
            <p>admin</p>
            <p>123</p>
        </form>
    </section>

</body>

</html>