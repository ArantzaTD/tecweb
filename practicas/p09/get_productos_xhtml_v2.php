<?php

@$conexion = new mysqli('127.0.0.1', 'root', '12345678a', 'marketzone', '3307');
$conexion->set_charset('utf8mb4');

if ($conexion->connect_errno) {
    die("<h3>Error al conectar con la base de datos: " . $conexion->connect_error . "</h3>");
}

$consulta = "SELECT * FROM productos ORDER BY id ASC";
$resultado = $conexion->query($consulta);

if (!$resultado) {
    die("<h3>Error al ejecutar la consulta: " . $conexion->error . "</h3>");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Productos (XHTML V2)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9f9;
            margin: 25px;
        }
        h2 {
            text-align: center;
            color: #2e4053;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #27ae60;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        a {
            color: #3498db;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        img {
            width: 60px;
        }
    </style>
</head>
<body>

<h2>Listado de Productos (XHTML V2)</h2>

<table>
    <thead>
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
    </thead>
    <tbody>
        <?php
        while ($fila = $resultado->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $fila['id'] . "</td>";
            echo "<td>" . htmlspecialchars($fila['nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($fila['marca']) . "</td>";
            echo "<td>" . htmlspecialchars($fila['modelo']) . "</td>";
            echo "<td>$" . number_format($fila['precio'], 2) . "</td>";
            echo "<td>" . $fila['unidades'] . "</td>";
            echo "<td>" . htmlspecialchars($fila['detalles']) . "</td>";

            if (!empty($fila['imagen'])) {
                echo "<td><img src='" . htmlspecialchars($fila['imagen']) . "' alt='imagen'></td>";
            } else {
                echo "<td><em>Sin imagen</em></td>";
            }

            echo "<td><a href='formulario_productos_v2.php?id=" . $fila['id'] . "'>Editar</a></td>";

            echo "</tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>

<?php
$resultado->free();
$conexion->close();
?>
