<?php
namespace TECWEB\MYAPI\Update;

use TECWEB\MYAPI\Core\DataBase;

class Update extends DataBase {

    public function edit(object $jsonOBJ) {

        $sql = "UPDATE productos SET 
            nombre   = '{$jsonOBJ->nombre}',
            marca    = '{$jsonOBJ->marca}',
            precio   = {$jsonOBJ->precio},
            unidades = {$jsonOBJ->unidades},
            modelo   = '{$jsonOBJ->modelo}',
            detalles = '{$jsonOBJ->detalles}',
            imagen   = '{$jsonOBJ->imagen}'
        WHERE id = {$jsonOBJ->id}";

        if ($this->conexion->query($sql)) {
            return json_encode([
                'status'  => 'success',
                'message' => 'Producto actualizado correctamente'
            ]);
        }

        return json_encode([
            'status'  => 'error',
            'message' => 'No se pudo actualizar el producto'
        ]);
    }
}
