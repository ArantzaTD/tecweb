<?php
include_once __DIR__ . '/database.php';
header('Content-Type: application/json; charset=utf-8');

// Leer JSON
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(["ok" => false, "msg" => "Datos JSON inválidos."]);
    exit;
}

$nombre   = trim($data["nombre"] ?? "");
$precio   = floatval($data["precio"] ?? 0);
$unidades = intval($data["unidades"] ?? 0);
$modelo   = trim($data["modelo"] ?? "");
$marca    = trim($data["marca"] ?? "");
$detalles = trim($data["detalles"] ?? "");
$imagen   = trim($data["imagen"] ?? "");

// Validaciones básicas
if ($nombre === "") {
    echo json_encode(["ok" => false, "msg" => "El nombre del producto es obligatorio."]);
    exit;
}

// Asegurar columna 'eliminado'
$columnaExiste = $conexion->query("SHOW COLUMNS FROM productos LIKE 'eliminado'");
$tieneEliminado = ($columnaExiste && $columnaExiste->num_rows > 0);

// Validar duplicado SOLO por nombre (cuando eliminado=0)
if ($tieneEliminado) {
    $sqlCheck = "SELECT id FROM productos WHERE nombre = ? AND eliminado = 0";
} else {
    $sqlCheck = "SELECT id FROM productos WHERE nombre = ?";
}
$stmt = $conexion->prepare($sqlCheck);
$stmt->bind_param("s", $nombre);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows > 0) {
    echo json_encode(["ok" => false, "msg" => "⚠️ Ya existe un producto con ese nombre."]);
    $stmt->close();
    $conexion->close();
    exit;
}
$stmt->close();

// Insertar si no existe
if ($tieneEliminado) {
    $sqlInsert = "INSERT INTO productos (nombre, precio, unidades, modelo, marca, detalles, imagen, eliminado)
                  VALUES (?, ?, ?, ?, ?, ?, ?, 0)";
} else {
    $sqlInsert = "INSERT INTO productos (nombre, precio, unidades, modelo, marca, detalles, imagen)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
}
$stmt = $conexion->prepare($sqlInsert);
$stmt->bind_param("sdissss", $nombre, $precio, $unidades, $modelo, $marca, $detalles, $imagen);
$ok = $stmt->execute();

echo json_encode([
    "ok" => $ok,
    "msg" => $ok ? "✅ Producto insertado correctamente." : "❌ Error al insertar el producto."
]);

$stmt->close();
$conexion->close();