<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexión a la base de datos
@$link = new mysqli('127.0.0.1', 'root', '12345678a', 'marketzone', '3307');
$link->set_charset('utf8mb4');

if ($link->connect_errno) {
    die("<h3>ERROR: No se pudo conectar con la base de datos. " . $link->connect_error . "</h3>");
}

$id        = $_POST['id'];
$nombre    = $_POST['nombre'];
$marca     = $_POST['marca'];
$modelo    = $_POST['modelo'];
$precio    = $_POST['precio'];
$unidades  = $_POST['unidades'];
$detalles  = $_POST['detalles'];
$imagen    = $_POST['imagen'];


$sql = "UPDATE productos 
        SET nombre='$nombre', 
            marca='$marca', 
            modelo='$modelo', 
            precio=$precio, 
            unidades=$unidades, 
            detalles='$detalles', 
            imagen='$imagen' 
        WHERE id=$id";


if ($link->query($sql)) {
    echo "<h2>✅ Producto actualizado correctamente.</h2>";
} else {
    echo "<h3>❌ Error al actualizar el producto: " . $link->error . "</h3>";
}

// Mostrar enlaces de regreso
echo "<p><a href='get_productos_xhtml_v2.php'>Volver al listado general</a></p>";
echo "<p><a href='get_productos_vigentes_v2.php'>Ver productos vigentes</a></p>";

// Cerrar conexión
$link->close();
?>
