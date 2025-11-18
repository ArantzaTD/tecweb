<?php
    include_once __DIR__.'/database.php';
    

    // SE OBTIENE LA INFORMACIÓN DEL PRODUCTO ENVIADA POR EL CLIENTE
    $producto = file_get_contents('php://input');
    $data = array(
        'status'  => 'error',
        'message' => 'La consulta falló'
    );

    if(!empty($producto)) {
        // SE TRANSFORMA EL STRING DEL JASON A OBJETO
        $jsonOBJ = json_decode($producto);

        if ($jsonOBJ === null) {
            $data['message'] = 'Error: Datos recibidos inválidos.';
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;
        }

        $id       = intval($jsonOBJ->id);
        $nombre   = $conexion->real_escape_string($jsonOBJ->nombre);
        $marca    = $conexion->real_escape_string($jsonOBJ->marca);
        $modelo   = $conexion->real_escape_string($jsonOBJ->modelo);
        $precio   = floatval($jsonOBJ->precio);
        $detalles = $conexion->real_escape_string($jsonOBJ->detalles);
        $unidades = intval($jsonOBJ->unidades);
        $imagen   = $conexion->real_escape_string($jsonOBJ->imagen);
        
        // SE ASUME QUE LOS DATOS YA FUERON VALIDADOS ANTES DE ENVIARSE

        $conexion->set_charset("utf8");
        $sql = "UPDATE productos SET 
                    nombre = '$nombre',
                    marca = '$marca',
                    modelo = '$modelo',
                    precio = $precio,
                    detalles = '$detalles',
                    unidades = $unidades,
                    imagen = '$imagen'
                WHERE id = $id";
        
        if($conexion->query($sql)){
            $data['status'] =  "success";
            $data['message'] =  "Producto actualizado";
        } else {
            $data['message'] = "ERROR: No se ejecuto $sql. " . mysqli_error($conexion);
        }

        // Cierra la conexion
        $conexion->close();
    }

    // SE HACE LA CONVERSIÓN DE ARRAY A JSON
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>