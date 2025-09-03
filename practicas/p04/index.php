<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Práctica 3</title>
</head>
<body>
    <h2>Ejercicio 1</h2>
    <p>Determina cuál de las siguientes variables son válidas y explica por qué:</p>
    <p>$_myvar,  $_7var,  myvar,  $myvar,  $var7,  $_element1, $house*5</p>
    <?php
        //AQUI VA MI CÓDIGO PHP
        $_myvar;
        $_7var;
        //myvar;       // Inválida
        $myvar;
        $var7;
        $_element1;
        //$house*5;     // Invalida
        
        echo '<h4>Respuesta:</h4>';   
    
        echo '<ul>';
        echo '<li>$_myvar es válida porque inicia con guión bajo.</li>';
        echo '<li>$_7var es válida porque inicia con guión bajo.</li>';
        echo '<li>myvar es inválida porque no tiene el signo de dolar ($).</li>';
        echo '<li>$myvar es válida porque inicia con una letra.</li>';
        echo '<li>$var7 es válida porque inicia con una letra.</li>';
        echo '<li>$_element1 es válida porque inicia con guión bajo.</li>';
        echo '<li>$house*5 es inválida porque el símbolo * no está permitido.</li>';
        echo '</ul>';
    ?>

    <h2>Ejercicio 2</h2>
    <p>Proporcionar los valores de $a, $b, $c como sigue y mostrar su evolución:</p>
    <?php
        echo '<div class="code-output">';
        echo '<h4>Primer bloque de asignaciones:</h4>';
        
        $a = "ManejadorSQL";
        $b = 'MySQL';
        $c = &$a;
        
        echo "a = $a<br/>";
        echo "b = $b<br/>";
        echo "c = $c<br/>";
        
        echo '<h4>Segundo bloque de asignaciones:</h4>';
        $a = "PHP server";
        $b = &$a;
        
        echo "a = $a<br/>";
        echo "b = $b<br/>";
        echo "c = $c<br/>";
        
        echo '<h4>Explicación:</h4>';
        echo '<p>En el segundo bloque, cuando $a cambia a "PHP server", $c también cambia porque es una referencia a $a. 
              Luego $b se convierte en referencia a $a, por lo que las tres variables apuntan al mismo valor.</p>';
        echo '</div>';
        
        // Liberar variables
        unset($a, $b, $c);
    ?>

    <h2>Ejercicio 3</h2>
    <p>Muestra el contenido de cada variable inmediatamente después de cada asignación:</p>
    <?php
        echo '<div class="code-output">';
        
        $a = "PHP5";
        echo "Después de \$a = \"PHP5\":<br/>";
        echo "a = "; var_dump($a); echo "<br/>";
        
        $z[] = &$a;
        echo "Después de \$z[] = &\$a:<br/>";
        echo "z = "; var_dump($z); echo "<br/>";
        echo "a = "; var_dump($a); echo "<br/>";
        
        $b = "5a version de PHP";
        echo "Después de \$b = \"5a version de PHP\":<br/>";
        echo "b = "; var_dump($b); echo "<br/>";
        
        $c = $b * 10;
        echo "Después de \$c = \$b * 10:<br/>";
        echo "c = "; var_dump($c); echo "<br/>";
        echo "Nota: \$b se convierte a 5 (conversión automática) y se multiplica por 10<br/>";
        
        $a .= $b;
        echo "Después de \$a .= \$b:<br/>";
        echo "a = "; var_dump($a); echo "<br/>";
        echo "z = "; var_dump($z); echo "<br/>";
        echo "Nota: Como z[0] es referencia a \$a, también cambia<br/>";
        
        $b *= $c;
        echo "Después de \$b *= \$c:<br/>";
        echo "b = "; var_dump($b); echo "<br/>";
        echo "Nota: \$b (5) se multiplica por \$c (50) = 250<br/>";
        
        $z[0] = "MySQL";
        echo "Después de \$z[0] = \"MySQL\":<br/>";
        echo "z = "; var_dump($z); echo "<br/>";
        echo "a = "; var_dump($a); echo "<br/>";
        echo "Nota: Como z[0] es referencia a \$a, \$a también cambia a \"MySQL\"<br/>";
        
        echo '</div>';
        
        // Liberar variables
        unset($a, $b, $c, $z);
    ?>

    <h2>Ejercicio 4</h2>
    <p>Lee y muestra los valores usando la matriz $GLOBALS:</p>
    <?php
        echo '<div class="code-output">';
        
        $a = "PHP5";
        $z[] = &$a;
        $b = "5a version de PHP";
        $c = $b * 10;
        $a .= $b;
        $b *= $c;
        $z[0] = "MySQL";
        
        echo '<h4>Valores usando $GLOBALS:</h4>';
        echo "GLOBALS['a'] = "; var_dump($GLOBALS['a']); echo "<br/>";
        echo "GLOBALS['b'] = "; var_dump($GLOBALS['b']); echo "<br/>";
        echo "GLOBALS['c'] = "; var_dump($GLOBALS['c']); echo "<br/>";
        echo "GLOBALS['z'] = "; var_dump($GLOBALS['z']); echo "<br/>";
        
        // Función usando global
        function mostrarVariablesGlobal() {
            global $a, $b, $c, $z;
            echo '<h4>Valores usando modificador global:</h4>';
            echo "a (global) = "; var_dump($a); echo "<br/>";
            echo "b (global) = "; var_dump($b); echo "<br/>";
            echo "c (global) = "; var_dump($c); echo "<br/>";
            echo "z (global) = "; var_dump($z); echo "<br/>";
        }
        
        mostrarVariablesGlobal();
        echo '</div>';
        
        // Liberar variables
        unset($a, $b, $c, $z);
    ?>
</body>
</html>