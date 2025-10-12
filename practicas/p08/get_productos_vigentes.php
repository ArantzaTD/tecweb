<?php
declare(strict_types=1);
header('Content-Type: application/xhtml+xml; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '1');


$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '12345678a';
$DB_NAME = 'marketzone';
$DB_PORT = 3307; 


$mysqli = @new mysqli($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME,$DB_PORT);
$mysqli->set_charset('utf8mb4');

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n";
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es">
  <head>
    <title>Productos vigentes</title>
    <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
    <style type="text/css">
      body { font-family: -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin: 20px; }
      table { border-collapse: collapse; width: 100%; }
      th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; vertical-align: top; }
      th { background:#f3f4f6; }
      .box { padding:12px; border-radius:8px; }
      .err { background:#fef2f2; border:1px solid #fecaca; color:#7f1d1d; }
      .warn{ background:#fff7ed; border:1px solid #fed7aa; color:#7c2d12; }
      img { max-height: 60px; }
      code { background:#f3f4f6; padding:2px 6px; border-radius:6px; }
    </style>
  </head>
  <body>
    <h1>Productos vigentes</h1>

<?php if ($mysqli->connect_errno): ?>
    <div class="box err">
      Error de conexión: <?php echo htmlspecialchars($mysqli->connect_error,ENT_QUOTES,'UTF-8'); ?><br />
      Revisa host/usuario/pass/BD y <strong>puerto</strong> (actual: <?php echo (int)$DB_PORT; ?>).
    </div>
  </body></html>
<?php exit; endif; ?>

<?php
$sql = "SELECT id, nombre, marca, modelo, precio, unidades, detalles, imagen
        FROM productos
        WHERE eliminado = 0
        ORDER BY id DESC";
$res = $mysqli->query($sql);

if ($res === false) {
    echo '<div class="box err">Error en la consulta: '.htmlspecialchars($mysqli->error,ENT_QUOTES,'UTF-8').'</div>';
    echo '<p>Tip: verifica que la tabla <code>productos</code> exista y tenga la columna <code>eliminado</code>.</p>';
    echo '</body></html>'; exit;
}

if ($res->num_rows === 0) {
    echo '<div class="box warn">No hay productos con <code>eliminado = 0</code>.<br />
          Inserta 1–2 productos desde el formulario o en phpMyAdmin ejecuta:<br />
          <code>UPDATE productos SET eliminado = 0 WHERE eliminado IS NULL OR eliminado = 1 LIMIT 2;</code></div>';
    echo '</body></html>'; exit;
}
?>

    <table>
      <tr>
        <th>id</th><th>nombre</th><th>marca</th><th>modelo</th>
        <th>precio</th><th>unidades</th><th>detalles</th><th>imagen</th>
      </tr>
      <?php while ($row = $res->fetch_assoc()): ?>
      <tr>
        <td><?php echo (int)$row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['nombre'],ENT_QUOTES,'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($row['marca'],ENT_QUOTES,'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($row['modelo'],ENT_QUOTES,'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($row['precio'],ENT_QUOTES,'UTF-8'); ?></td>
        <td><?php echo (int)$row['unidades']; ?></td>
        <td><?php echo htmlspecialchars($row['detalles'] ?? '',ENT_QUOTES,'UTF-8'); ?></td>
        <td>
          <?php
            $img = trim((string)($row['imagen'] ?? ''));
            if ($img !== '') {
              echo '<img src="'.htmlspecialchars($img,ENT_QUOTES,'UTF-8').'" alt="img" />';
            } else {
              echo '&mdash;';
            }
          ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
  </body>
</html>
