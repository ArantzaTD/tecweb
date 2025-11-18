<?php
namespace TECWEB\MYAPI\Create;

use TECWEB\MYAPI\Core\DataBase;

class Create extends DataBase {

    public function add(object $jsonOBJ) {
        
        // ¿Ya existe?
        $sql = "SELECT * FROM productos 
                WHERE nombre = '{$jsonOBJ->nombre}' 
                AND eliminado = 0";
        $result = $this->conexion->query($sql);

        if ($result->num_rows > 0) {
            return json_encode([
                'status'  => 'error',
                'message' => 'Ya existe un producto con ese nombre'
            ]);
        }

        // Insertar nuevo
        $sql = "INSERT INTO productos VALUES(
            null,
            '{$jsonOBJ->nombre}',
            '{$jsonOBJ->marca}',
            {$jsonOBJ->precio},
            {$jsonOBJ->unidades},
            '{$jsonOBJ->modelo}',
            '{$jsonOBJ->detalles}',
            '{$jsonOBJ->imagen}',
            0
        )";

        if ($this->conexion->query($sql)) {
            return json_encode([
                'status'  => 'success',
                'message' => 'Producto agregado correctamente'
            ]);
        }

        return json_encode([
            'status'  => 'error',
            'message' => 'No se pudo insertar el producto'
        ]);
    }
}
