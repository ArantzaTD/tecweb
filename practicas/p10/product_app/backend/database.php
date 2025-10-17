<?php
$hostname = '127.0.0.1';
$username = 'root';
$password = '12345678a';
$database = 'marketzone';
$port     = 3307; // cambia a 3306 si tu MySQL usa el puerto por defecto

$conexion = @new mysqli($hostname, $username, $password, $database, $port);
if ($conexion->connect_errno) {
    echo json_encode([]);
    exit;
}
$conexion->set_charset('utf8mb4');
