<?php
    include("C:/xampp/htdocs/tecweb/practicas/p06/src/funciones.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Práctica 6</title>
</head>
<body>
    <h2>Ejercicio 1</h2>
    <p>Escribir programa para comprobar si un número es un múltiplo de 5 y 7</p>
    <?php
        
        if(isset($_GET['numero']))
        {
            $num = $_GET['numero'];
            if ($num%5==0 && $num%7==0)
            {
                echo '<h3>R= El número '.$num.' SÍ es múltiplo de 5 y 7.</h3>';
            }
            else
            {
                echo '<h3>R= El número '.$num.' NO es múltiplo de 5 y 7.</h3>';
            }
        }
    ?>

    <h2>Ejercicio 2 </h2>
    <p>Crea un programa para la generación repetitiva de 3 números aleatorios hasta obtener una secuencia compuesta</p>
    <?php
        generarMatriz();

    ?>

    <h2>Ejercicio 3</h2>
    <p>Utiliza un ciclo while para encontrar el primer número entero obtenido aleatoriamente, pero que además sea múltiplo de un número dado.  </p>
    <?php
        if (isset($_GET['divisor'])) {
            $d = $_GET['divisor'];
            echo "While encontró: ".buscarMultiploWhile($d)."<br>";
            echo "Do-While encontró: ".buscarMultiploDoWhile($d);
        }
  

    ?>

    <h2>Ejercicio 4</h2>
    <p> Crear un arreglo cuyos índices van de 97 a 122 y cuyos valores son las letras de la ‘a’ a la ‘z’.</p>
    <?php
        mostrarArregloAscii();
    ?>

    <h2>EJERCICIO 5 </h2>
    <form action="http://localhost:8080/tecweb/practicas/p06/index.php" method="POST">
        Edad: <input type="number" name="edad" required><br>
        Sexo: 
        <select name="sexo">
            <option value="femenino">Femenino</option>
            <option value="masculino">Masculino</option>
        </select><br>
        <input type="submit" value="Enviar">
    </form>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        echo validarEdadSexo($_POST['edad'], $_POST['sexo']);
    }
    ?>

    <h2>EJERCICIO 6</h2>
    <form action="http://localhost:8080/tecweb/practicas/p06/index.php" method="POST">
        <label>Matrícula: </label>
        <input type="text" name="matricula">
        <input type="submit" name="buscar" value="Buscar">
        <input type="submit" name="todos" value="Mostrar Todos">
    </form>
    <?php
        if (isset($_POST['buscar']) && !empty($_POST['matricula'])) {
            buscarVehiculo($_POST['matricula']);
        } elseif (isset($_POST['todos'])) {
            mostrarVehiculos();
        }
    ?>
</body>
</html>