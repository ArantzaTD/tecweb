<?php

//EJERCICIO1
    function comprobarMultiplo($numero) {
        if ($numero % 5 == 0 && $numero % 7 == 0) {
            return "$numero es múltiplo de 5 y 7.";
        } else {
            return "$numero no es múltiplo de 5 y 7.";
        }
    }

//EJERCICIO2
    function generarMatriz() {
        $matriz = [];
        $iteraciones = 0;

        do {
            $fila = [];
            $fila[0] = rand(100, 999);
            $fila[1] = rand(100, 999);
            $fila[2] = rand(100, 999);
            $matriz[] = $fila;
            $iteraciones++;
        } while (!($fila[0] % 2 != 0 && $fila[1] % 2 == 0 && $fila[2] % 2 != 0));

        foreach ($matriz as $f) {
            echo implode(", ", $f) . "<br>";
        }

        $total = $iteraciones * 3;
        echo "<b>$total números obtenidos en $iteraciones iteraciones</b><br>";
    }

//EJERCICIO3
    function buscarMultiploWhile($divisor) {
        while (true) {
            $num = rand(1,1000);
            if ($num % $divisor == 0) {
                return $num;
            }
        }
    }

    function buscarMultiploDoWhile($divisor) {
        do {
            $num = rand(1,1000);
        } while ($num % $divisor != 0);
        return $num;
    }

//EJERCICIO4

    function mostrarArregloAscii() {
        $arreglo = [];
        for ($i = 97; $i <= 122; $i++) {
            $arreglo[$i] = chr($i);
        }

        echo "<table border='1'>";
        foreach ($arreglo as $key => $value) {
            echo "<tr><td>$key</td><td>$value</td></tr>";
        }
        echo "</table>";
    }

//EJERCICIO5

    function validarEdadSexo($edad, $sexo) {
        if ($sexo == "femenino" && $edad >= 18 && $edad <= 35) {
            echo "Bienvenida, usted está en el rango de edad permitido.";
        } else {
            echo "Lo sentimos, no cumple con los requisitos.";}
    }    
?>