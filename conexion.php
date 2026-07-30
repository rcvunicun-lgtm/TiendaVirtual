<?php
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $db = "tienda_virtual_1";

    /*
    El símbolo @ en PHP se utiliza para suprimir errores que puedan generarse en una expresión o función. 
    Cuando colocas @ antes de una función, como en tu ejemplo @mysqli_connect(), si esa función genera una advertencia o un error, este no será mostrado en la salida.
    Por ejemplo, si mysqli_connect() falla (por un host incorrecto o credenciales inválidas), normalmente mostraría un mensaje de error. 
    Sin embargo, al usar @, esos mensajes se suprimen. Esto puede ser útil para evitar mostrar detalles técnicos al usuario, 
    aunque generalmente no es la mejor práctica, ya que puede dificultar la depuración de errores. 
    */

    // Create connection
    $conection = @mysqli_connect($host, $user, $password, $db);

    // Check connection
    if (!$conection) {
    die("Connection failed: " . mysqli_connect_error());
    }

    // echo "Connected successfully";
?>
