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

//EJERCICIO6

    function ParqueVehicular() {
        return [
    "UBN6338" => [
        "Auto" => ["marca" => "HONDA", "modelo" => 2020, "tipo" => "camioneta"],
        "Propietario" => ["nombre" => "Alfonzo Esparza", "ciudad" => "Puebla, Pue.", "direccion" => "C.U."]
    ],
    "XCD9123" => [
        "Auto" => ["marca" => "TOYOTA", "modelo" => 2018, "tipo" => "sedan"],
        "Propietario" => ["nombre" => "María López", "ciudad" => "Monterrey, NL", "direccion" => "Av. Juárez 230"]
    ],
    "JKL4421" => [
        "Auto" => ["marca" => "NISSAN", "modelo" => 2021, "tipo" => "hatchback"],
        "Propietario" => ["nombre" => "Carlos Ramírez", "ciudad" => "Guadalajara, Jal.", "direccion" => "Col. Americana"]
    ],
    "TRS8890" => [
        "Auto" => ["marca" => "MAZDA", "modelo" => 2019, "tipo" => "sedan"],
        "Propietario" => ["nombre" => "Ana Torres", "ciudad" => "Querétaro, Qro.", "direccion" => "Av. Constituyentes 54"]
    ],
    "LMN5543" => [
        "Auto" => ["marca" => "FORD", "modelo" => 2022, "tipo" => "camioneta"],
        "Propietario" => ["nombre" => "Jorge Castillo", "ciudad" => "Toluca, Edo. Méx.", "direccion" => "Zona Industrial"]
    ],
    "QWE7788" => [
        "Auto" => ["marca" => "CHEVROLET", "modelo" => 2020, "tipo" => "camioneta"],
        "Propietario" => ["nombre" => "Lucía Hernández", "ciudad" => "Mérida, Yuc.", "direccion" => "Centro Histórico"]
    ],
    "RTY9921" => [
        "Auto" => ["marca" => "VOLKSWAGEN", "modelo" => 2017, "tipo" => "sedan"],
        "Propietario" => ["nombre" => "Fernando Morales", "ciudad" => "Tijuana, BC", "direccion" => "Col. Libertad"]
    ],
    "PLK6610" => [
        "Auto" => ["marca" => "KIA", "modelo" => 2021, "tipo" => "sedan"],
        "Propietario" => ["nombre" => "Gabriela Flores", "ciudad" => "Cancún, Q. Roo", "direccion" => "Av. Bonampak"]
    ],
    "GHJ3344" => [
        "Auto" => ["marca" => "HYUNDAI", "modelo" => 2016, "tipo" => "sedan"],
        "Propietario" => ["nombre" => "Manuel Ortega", "ciudad" => "León, Gto.", "direccion" => "Blvd. López Mateos"]
    ],
    "BVC2299" => [
        "Auto" => ["marca" => "TESLA", "modelo" => 2023, "tipo" => "sedan"],
        "Propietario" => ["nombre" => "Sandra Aguilar", "ciudad" => "CDMX", "direccion" => "Col. Roma Norte"]
    ],
    "XZY8473" => [
        "Auto" => ["marca" => "PEUGEOT", "modelo" => 2019, "tipo" => "hatchback"],
        "Propietario" => ["nombre" => "Ricardo Núñez", "ciudad" => "San Luis Potosí, SLP", "direccion" => "Col. Del Valle"]
    ],
    "WSA5201" => [
        "Auto" => ["marca" => "BMW", "modelo" => 2021, "tipo" => "sedan"],
        "Propietario" => ["nombre" => "Patricia Mendoza", "ciudad" => "Aguascalientes, Ags.", "direccion" => "Av. Universidad"]
    ],
    "EDC1180" => [
        "Auto" => ["marca" => "MERCEDES", "modelo" => 2020, "tipo" => "sedan"],
        "Propietario" => ["nombre" => "Luis Herrera", "ciudad" => "Culiacán, Sin.", "direccion" => "Zona Centro"]
    ],
    "RFV4512" => [
        "Auto" => ["marca" => "AUDI", "modelo" => 2018, "tipo" => "sedan"],
        "Propietario" => ["nombre" => "Sofía Jiménez", "ciudad" => "Morelia, Mich.", "direccion" => "Col. Chapultepec"]
    ],
    "TGB6709" => [
        "Auto" => ["marca" => "JEEP", "modelo" => 2023, "tipo" => "camioneta"],
        "Propietario" => ["nombre" => "Diego Sánchez", "ciudad" => "Veracruz, Ver.", "direccion" => "Malecón"]
    ],
];

    }

    function buscarVehiculo($matricula) {
    $parque = parqueVehicular();
    if (isset($parque[$matricula])) {
        echo "<pre>";
        print_r($parque[$matricula]);
        echo "</pre>";
    } else {
        echo "No se encontró vehículo con matrícula $matricula.";
    }
    }

    function mostrarVehiculos() {
        $parque = parqueVehicular();
        echo "<pre>";
        print_r($parque);
        echo "</pre>";
    }
?>