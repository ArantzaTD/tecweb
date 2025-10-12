<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');


$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '12345678a';
$DB_NAME = 'marketzone';
$DB_PORT = 3307; 


if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST)) {
    header('Location: formulario_productos.html');
    exit;
}


$nombre   = isset($_POST['nombre'])   ? trim((string)$_POST['nombre'])   : '';
$marca    = isset($_POST['marca'])    ? trim((string)$_POST['marca'])    : '';
$modelo   = isset($_POST['modelo'])   ? trim((string)$_POST['modelo'])   : '';
$precio   = isset($_POST['precio'])   ? (float)$_POST['precio']          : 0.0;
$unidades = isset($_POST['unidades']) ? (int)$_POST['unidades']          : 0;
$detalles = isset($_POST['detalles']) ? trim((string)$_POST['detalles']) : '';
$imagen   = isset($_POST['imagen'])   ? trim((string)$_POST['imagen'])   : '';

$errores = [];


if ($nombre === '' || mb_strlen($nombre) > 100)   $errores[] = 'Nombre vacío o mayor a 100 caracteres.';
if ($marca  === '' || mb_strlen($marca)  > 50)    $errores[] = 'Marca vacía o mayor a 50 caracteres.';
if ($modelo === '' || mb_strlen($modelo) > 50)    $errores[] = 'Modelo vacío o mayor a 50 caracteres.';
if (!is_numeric($_POST['precio'] ?? null) || $precio < 0)     $errores[] = 'Precio inválido (número ≥ 0).';
if (!is_numeric($_POST['unidades'] ?? null) || $unidades < 0) $errores[] = 'Unidades inválidas (entero ≥ 0).';
if (mb_strlen($detalles) > 255)                                 $errores[] = 'Detalles excede 255 caracteres.';
if ($imagen !== '' && !preg_match('/\.(png|jpg|jpeg|gif)$/i', $imagen)) {
    $errores[] = 'Imagen debe terminar en .png, .jpg, .jpeg o .gif (o dejarse vacía).';
}


$mysqli = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
if ($mysqli->connect_errno) {
    respuesta_xhtml(false, 'Falló la conexión: '.$mysqli->connect_error);
    exit;
}
$mysqli->set_charset('utf8mb4');


if ($errores) {
    respuesta_xhtml(false, listado_errores($errores));
    exit;
}

$check = $mysqli->query("
  SELECT 1
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME   = 'productos'
    AND COLUMN_NAME  = 'eliminado'
");
if ($check && $check->num_rows === 0) {
    if (!$mysqli->query("ALTER TABLE productos ADD COLUMN eliminado TINYINT NOT NULL DEFAULT 0")) {
        respuesta_xhtml(false, 'No se pudo crear la columna eliminado: '.$mysqli->error);
        exit;
    }
}


$sqlDup = "SELECT COUNT(*) FROM productos WHERE nombre=? AND marca=? AND modelo=?";
$st = $mysqli->prepare($sqlDup);
if (!$st) { respuesta_xhtml(false, 'Error en prepare (duplicado): '.$mysqli->error); exit; }
$st->bind_param('sss', $nombre, $marca, $modelo);
$st->execute();
$st->bind_result($existe);
$st->fetch();
$st->close();

if ((int)$existe > 0) {
    respuesta_xhtml(false, 'El producto ya existe (misma combinación de nombre, marca y modelo).');
    exit;
}

$sql = "INSERT INTO productos VALUES (DEFAULT, ?, ?, ?, ?, ?, ?, ?, 0)";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('sssdiss', $nombre, $marca, $modelo, $precio, $detalles, $unidades, $imagen);


/* --- Consulta anterior  ---
$sqlIns = "INSERT INTO productos
           (nombre, marca, modelo, precio, unidades, detalles, imagen, eliminado)
           VALUES (?, ?, ?, ?, ?, ?, ?, 0)";
-------------------------------------------------------------- */

/* >>> NUEVA consulta con column names (sin id ni eliminado) <<< */
$sqlIns = "INSERT INTO productos
           (nombre, marca, modelo, precio, unidades, detalles, imagen)
           VALUES (?, ?, ?, ?, ?, ?, ?)";

$st = $mysqli->prepare($sqlIns);
if (!$st) { respuesta_xhtml(false, 'Error en prepare (insert): '.$mysqli->error); exit; }

/* Tipos para bind: s s s d i s s  -> "sssdiss" (sin espacios) */
if (!$st->bind_param('sssdiss', $nombre, $marca, $modelo, $precio, $unidades, $detalles, $imagen)) {
    respuesta_xhtml(false, 'Error en bind_param: '.$st->error); exit;
}
if (!$st->execute()) {
    respuesta_xhtml(false, 'Error al insertar: '.$st->error); exit;
}
$st->close();


$tablaResumen = <<<HTML
<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse">
  <tr><th align="left">Campo</th><th align="left">Valor</th></tr>
  <tr><td>Nombre</td><td>{$nombre}</td></tr>
  <tr><td>Marca</td><td>{$marca}</td></tr>
  <tr><td>Modelo</td><td>{$modelo}</td></tr>
  <tr><td>Precio</td><td>{$precio}</td></tr>
  <tr><td>Unidades</td><td>{$unidades}</td></tr>
  <tr><td>Detalles</td><td>{$detalles}</td></tr>
  <tr><td>Imagen</td><td>{$imagen}</td></tr>
</table>
HTML;

respuesta_xhtml(true, 'Registro insertado correctamente (id y eliminado tomaron sus valores por defecto).', $tablaResumen);
exit;


function respuesta_xhtml(bool $ok, string $mensaje, string $extraHTML = ''): void {
    header('Content-Type: application/xhtml+xml; charset=utf-8');
    $titulo = $ok ? 'Alta exitosa' : 'Error en el registro';
    $color  = $ok ? '#065f46' : '#7f1d1d';
    $bg     = $ok ? '#ecfdf5' : '#fef2f2';
    echo <<<XHTML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es">
  <head>
    <title>{$titulo}</title>
    <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
    <style type="text/css">
      body { font-family: -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin: 20px; }
      .box { background: {$bg}; border: 1px solid #e5e7eb; padding: 14px; border-radius: 8px; color: {$color}; }
      table { margin-top: 12px; }
      th { background:#f3f4f6; }
      .actions a { margin-right: 10px; }
    </style>
  </head>
  <body>
    <h1>{$titulo}</h1>
    <div class="box">{$mensaje}</div>
    {$extraHTML}
    <div class="actions" style="margin-top:14px;">
      <a href="formulario_productos.html">← Capturar otro</a>
    </div>
  </body>
</html>
XHTML;
}

function listado_errores(array $errs): string {
    $items = '';
    foreach ($errs as $e) {
        $e = htmlspecialchars($e, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $items .= '<li>'.$e.'</li>';
    }
    return '<p>Se detectaron errores en los datos enviados:</p><ul>'.$items.'</ul>';
}
