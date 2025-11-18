<?php
namespace TECWEB\MYAPI\Core;

abstract class DataBase {
    protected $conexion;

    public function __construct($user, $pass, $db) {
        $this->conexion = @mysqli_connect(
            'localhost',
            $user,
            $pass,
            $db,
            3307 // ← PUERTO CORRECTO PARA TU MÁQUINA
        );

        if (!$this->conexion) {
            die('¡Base de datos NO conectada!');
        }

        $this->conexion->set_charset("utf8");
    }

    public function __destruct() {
        if ($this->conexion) {
            $this->conexion->close();
        }
    }
}
