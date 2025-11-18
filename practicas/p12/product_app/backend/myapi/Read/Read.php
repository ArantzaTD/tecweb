<?php
namespace TECWEB\MYAPI\Read;

use TECWEB\MYAPI\Core\DataBase;

class Read extends DataBase {

    public function list() {
        $sql = "SELECT * FROM productos WHERE eliminado = 0";
        $result = $this->conexion->query($sql);
        return json_encode($result->fetch_all(MYSQLI_ASSOC));
    }

    public function search(string $valor) {
        $sql = "SELECT * FROM productos 
                WHERE nombre LIKE '%$valor%' 
                AND eliminado = 0";

        $result = $this->conexion->query($sql);
        return json_encode($result->fetch_all(MYSQLI_ASSOC));
    }

    public function single(string $id) {
        $sql = "SELECT * FROM productos 
                WHERE id = $id 
                AND eliminado = 0";

        $result = $this->conexion->query($sql);
        return json_encode($result->fetch_assoc());
    }
}
