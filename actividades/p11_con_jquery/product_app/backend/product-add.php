<?php 
include_once __DIR__.'/database.php';

$input = file_get_contents('php://input');
$data = ['status' => 'error', 'message' => 'Error al agregar producto.'];

if (!empty($input)) {
    $json = json_decode($input);

    if ($json === null) {
        $data['message'] = 'Error: No se pudo interpretar los datos recibidos.';
        echo json_encode($data);
        exit;
    }

    $nombre   = $conexion->real_escape_string($json->nombre);
    $marca    = $conexion->real_escape_string($json->marca);
    $modelo   = $conexion->real_escape_string($json->modelo);
    $precio   = floatval($json->precio);
    $detalles = $conexion->real_escape_string($json->detalles);
    $unidades = intval($json->unidades);
    $imagen   = $conexion->real_escape_string($json->imagen);

    $sql = "SELECT * FROM productos WHERE nombre = '$nombre' AND eliminado = 0";
    $result = $conexion->query($sql);

    if ($result && $result->num_rows == 0) {
        $sql = "INSERT INTO productos VALUES (null, '$nombre', '$marca', '$modelo', $precio, '$detalles', $unidades, '$imagen', 0)";
        if ($conexion->query($sql)) {
            $data['status'] = 'success';
            $data['message'] = 'Producto agregado correctamente.';
        } else {
            $data['message'] = 'Error SQL: ' . $conexion->error;
        }
    } else {
        $data['message'] = 'Ya existe un producto con ese nombre.';
    }

    if (isset($result)) $result->free();
    $conexion->close();
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>
