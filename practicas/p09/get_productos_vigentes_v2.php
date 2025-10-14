<?php

@$conexion = new mysqli('127.0.0.1', 'root', '12345678a', 'marketzone', '3307');
$conexion->set_charset('utf8mb4');

if ($conexion->connect_errno) {
    die("Error al conectar con la base de datos.");
}

$consulta = "SELECT * FROM productos WHERE unidades > 0 ORDER BY id ASC";
$res = $conexion->query($consulta);

if (!$res) {
    die("Error al ejecutar la consulta.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos vigentes (V2)</title>
    <style>
        body { font-family: Arial; background:#f5f5f5; margin:25px; }
        table { width:100%; border-collapse:collapse; background:white; }
        th, td { border:1px solid #ccc; padding:10px; text-align:center; }
        th { background:#2196F3; color:white; }
        tr:nth-child(even){ background:#f9f9f9; }
        a { color:#4CAF50; text-decoration:none; }
        a:hover { text-decoration:underline; }
    </style>
</head>
<body>

<h2 style="text-align:center;">Productos vigentes (stock disponible)</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Marca</th>
        <th>Modelo</th>
        <th>Precio</th>
        <th>Unidades</th>
        <th>Detalles</th>
        <th>Imagen</th>
        <th>Editar</th>
    </tr>

<?php
while ($fila = $res->fetch_assoc()) {
    echo "<tr>";
    echo "<td>".$fila['id']."</td>";
    echo "<td>".htmlspecialchars($fila['nombre'])."</td>";
    echo "<td>".htmlspecialchars($fila['marca'])."</td>";
    echo "<td>".htmlspecialchars($fila['modelo'])."</td>";
    echo "<td>$".number_format($fila['precio'], 2)."</td>";
    echo "<td>".$fila['unidades']."</td>";
    echo "<td>".htmlspecialchars($fila['detalles'])."</td>";
    
    if (!empty($fila['imagen'])) {
        echo "<td><img src='".htmlspecialchars($fila['imagen'])."' width='60'></td>";
    } else {
        echo "<td><em>Sin imagen</em></td>";
    }

    echo "<td><a href='formulario_productos_v2.php?id=".$fila['id']."'>Editar</a></td>";

    echo "</tr>";
}
?>
</table>

</body>
</html>

<?php
$res->free();
$conexion->close();
?>
