<?php
// backend/read.php
header('Content-Type: application/json; charset=utf-8');
include_once __DIR__ . '/database.php';

// Helpers de salida (siempre 200)
function out($data) { echo json_encode($data, JSON_UNESCAPED_UNICODE); exit; }
function out_empty_obj() { out([]); } // para ID sin resultado
function out_empty_arr() { out([]); } // para texto sin resultado

// Normaliza entrada (POST prioritario, GET de respaldo)
$id = isset($_POST['id']) ? trim((string)$_POST['id']) : (isset($_GET['id']) ? trim((string)$_GET['id']) : null);
$q  = isset($_POST['q'])  ? trim((string)$_POST['q'])  : (isset($_GET['q'])  ? trim((string)$_GET['q'])  : null);

// Si conexión falló en database.php, responde vacío compatible
if (!isset($conexion) || $conexion->connect_errno) {
  if ($id !== null && $id !== '') out_empty_obj(); else out_empty_arr();
}
@$conexion->set_charset('utf8mb4');

// --- Buscar por ID (prioridad si llegan ambos) ---
if ($id !== null && $id !== '') {
  if (!is_numeric($id)) out_empty_obj();
  $id_int = (int)$id;

  $sql = "SELECT id, nombre, precio, unidades, modelo, marca, detalles
          FROM productos
          WHERE id = ?";
  if ($stmt = @$conexion->prepare($sql)) {
    @$stmt->bind_param("i", $id_int);
    if (@$stmt->execute()) {
      $res = @$stmt->get_result();
      $row = $res ? $res->fetch_assoc() : null;
      @$stmt->close();
      out($row ?: []); // objeto o {}
    }
    @$stmt->close();
  }
  out_empty_obj(); // en cualquier error → objeto vacío
}

// --- Buscar por TEXTO (nombre/marca/modelo/detalles) ---
if ($q !== null && $q !== '') {
  $like = "%".$q."%";
  $sql = "SELECT id, nombre, precio, unidades, modelo, marca, detalles
          FROM productos
          WHERE nombre LIKE ?
             OR marca  LIKE ?
             OR modelo LIKE ?
             OR detalles LIKE ?
          ORDER BY id ASC";
  if ($stmt = @$conexion->prepare($sql)) {
    @$stmt->bind_param("ssss", $like, $like, $like, $like);
    if (@$stmt->execute()) {
      $res = @$stmt->get_result();
      $rows = [];
      while ($res && ($r = $res->fetch_assoc())) { $rows[] = $r; }
      @$stmt->close();
      out($rows); // [] si no hay coincidencias
    }
    @$stmt->close();
  }
  out_empty_arr(); // en cualquier error → []
}

// Sin parámetros válidos → arreglo vacío (seguro para el front)
out_empty_arr();
