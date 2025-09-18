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
        
?>