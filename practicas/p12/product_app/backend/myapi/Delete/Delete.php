<?php
namespace TECWEB\MYAPI\Delete;

use TECWEB\MYAPI\Core\DataBase;

class Delete extends DataBase {

    public function delete(string $id) {

        $sql = "UPDATE productos SET eliminado = 1 
                WHERE id = $id";

        if ($this->conexion->query($sql)) {
            return json_encode([
                'status'  => 'success',
                'message' => 'Producto eliminado correctamente'
            ]);
        }

        return json_encode([
            'status'  => 'error',
            'message' => 'No se pudo eliminar el producto'
        ]);
    }
}
