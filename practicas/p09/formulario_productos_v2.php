<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexión a la base de datos
@$conexion = new mysqli('127.0.0.1', 'root', '12345678a', 'marketzone', '3307');
$conexion->set_charset('utf8mb4');

if ($conexion->connect_errno) {
    die("<h3>Error al conectar con la base de datos: " . $conexion->connect_error . "</h3>");
}

// Verificar si llega el parámetro id
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("<h3>No se proporcionó un ID de producto válido.</h3>");
}

$id = intval($_GET['id']);

// Consultar el producto con ese ID
$consulta = "SELECT * FROM productos WHERE id = $id";
$resultado = $conexion->query($consulta);

if ($resultado->num_rows == 0) {
    die("<h3>Producto no encontrado.</h3>");
}

$producto = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f7;
            margin: 25px;
        }
        h2 {
            text-align: center;
            color: #2e4053;
        }
        form {
            width: 60%;
            margin: 0 auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 10px 15px;
            margin-top: 15px;
            cursor: pointer;
            border-radius: 5px;
        }
        input[type="submit"]:hover {
            background-color: #219150;
        }
        a {
            display: inline-block;
            margin-top: 15px;
            color: #3498db;
            text-decoration: none;
        }
    </style>
</head>
<body>

<h2>Editar Producto: <?php echo htmlspecialchars($producto['nombre']); ?></h2>

<form action="update_productos.php" method="post">
    <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">

    <label>Nombre:</label>
    <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>

    <label>Marca:</label>
    <input type="text" name="marca" value="<?php echo htmlspecialchars($producto['marca']); ?>" required>

    <label>Modelo:</label>
    <input type="text" name="modelo" value="<?php echo htmlspecialchars($producto['modelo']); ?>" required>

    <label>Precio:</label>
    <input type="number" name="precio" step="0.01" value="<?php echo $producto['precio']; ?>" required>

    <label>Unidades:</label>
    <input type="number" name="unidades" value="<?php echo $producto['unidades']; ?>" required>

    <label>Detalles:</label>
    <textarea name="detalles" rows="4"><?php echo htmlspecialchars($producto['detalles']); ?></textarea>

    <label>Imagen (URL):</label>
    <input type="text" name="imagen" value="<?php echo htmlspecialchars($producto['imagen']); ?>">

    <input type="submit" value="Actualizar producto">
</form>

<div style="text-align:center;">
    <a href="get_productos_xhtml_v2.php">⬅ Volver al listado</a>
</div>

</body>
</html>

<?php
$resultado->free();
$conexion->close();
?>
