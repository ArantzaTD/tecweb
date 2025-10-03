<?php
// get_productos_xhtml.php  (XHTML 1.1 + Bootstrap)
// Muestra productos con unidades <= ?tope
// URL: http://localhost:8080/tecweb/practicas/p07/get_productos_xhtml.php?tope=10

header('Content-Type: application/xhtml+xml; charset=UTF-8');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* Helpers mínimos */
function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
function web_path($rel){
    $rel = str_replace('\\','/', (string)$rel);
    $rel = ltrim($rel, '/');
    if ($rel === '') return '';
    if (preg_match('~^https?://~i', $rel)) return $rel;
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    return $base . '/' . $rel;
}

/* Validación de parámetro */
$err  = '';
$rows = [];
if (!isset($_GET['tope']) || !ctype_digit($_GET['tope'])) {
    $err = 'Parámetro "tope" inválido. Usa un entero (ej. ?tope=10).';
} else {
    $tope = (int)$_GET['tope'];

    // CONEXIÓN — AJUSTA LA CONTRASEÑA SI TU root LA TIENE
    @$link = new mysqli('127.0.0.1', 'root', '12345678a', 'marketzone', 3307);
    // Si entras a phpMyAdmin SIN contraseña: usa '' en lugar de '12345678a'
    $link->set_charset('utf8mb4');

    if ($link->connect_errno) {
        $err = 'Falló la conexión: ' . $link->connect_error;
    } else {
        $sql = "SELECT id, nombre, marca, modelo, precio, unidades, detalles, imagen
                FROM productos
                WHERE unidades <= ?
                ORDER BY unidades ASC, id ASC";
        $stmt = $link->prepare($sql);
        $stmt->bind_param('i', $tope);
        $stmt->execute();
        $res = $stmt->get_result();

        while ($r = $res->fetch_assoc()) {
            $r['imagen'] = web_path($r['imagen'] ?? '');
            $rows[] = $r;
        }
        $res->free();
        $stmt->close();
        $link->close();
    }
}

$xml = '<?xml version="1.0" encoding="UTF-8"?>';
?>
<?= $xml ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es">
  <head>
    <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />
    <title>Productos (≤ tope)</title>
    <link rel="stylesheet"
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
          crossorigin="anonymous" />
    <style type="text/css">
      img { max-width:120px; height:auto; }
    </style>
  </head>
  <body>
    <h3>PRODUCTOS (unidades ≤ <?= isset($tope) ? (int)$tope : 0 ?>)</h3>
    <br />

    <?php if ($err !== ''): ?>
      <div class="alert alert-danger" role="alert"><?= h($err) ?></div>
    <?php elseif (count($rows) === 0): ?>
      <div class="alert alert-warning" role="alert">
        No hay productos con unidades ≤ <?= (int)$tope ?>.
      </div>
    <?php else: ?>
      <table class="table">
        <thead class="thead-dark">
          <tr>
            <th scope="col">#</th>
            <th scope="col">Nombre</th>
            <th scope="col">Marca</th>
            <th scope="col">Modelo</th>
            <th scope="col">Precio</th>
            <th scope="col">Unidades</th>
            <th scope="col">Detalles</th>
            <th scope="col">Imagen</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $p): ?>
          <tr>
            <th scope="row"><?= (int)$p['id'] ?></th>
            <td><?= h($p['nombre']) ?></td>
            <td><?= h($p['marca']) ?></td>
            <td><?= h($p['modelo']) ?></td>
            <td><?= number_format((float)$p['precio'], 2) ?></td>
            <td><?= (int)$p['unidades'] ?></td>
            <td><?= h($p['detalles']) ?></td>
            <td>
              <?php if (!empty($p['imagen'])): ?>
                <img src="<?= h($p['imagen']) ?>"
                     alt="<?= h('Imagen de '.$p['nombre']) ?>" />
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </body>
</html>
