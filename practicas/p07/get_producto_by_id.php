<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es">
<?php
header('Content-Type: text/html; charset=UTF-8');

function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
function web_path($rel){
    $rel = ltrim((string)$rel, "/\\");
    if ($rel === '') return '';
    if (preg_match('~^https?://~i', $rel)) return $rel;
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    return $base . '/' . $rel;
}

$row = null;

$id = (isset($_GET['id']) && ctype_digit($_GET['id'])) ? (int)$_GET['id'] : null;

if ($id !== null) {

    @$link = new mysqli('127.0.0.1', 'root', '12345678a', 'marketzone', 3307);
    $link->set_charset('utf8mb4');

    if ($link->connect_errno) {
        die('Falló la conexión: ' . $link->connect_error . '<br/>');
        
    }

    $stmt = $link->prepare("SELECT id, nombre, marca, modelo, precio, unidades, detalles, imagen
                            FROM productos WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $result->free();
    $stmt->close();
    $link->close();
}
?>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Producto</title>
    <link rel="stylesheet"
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
          crossorigin="anonymous" />
</head>
<body>
    <h3>PRODUCTO</h3>
    <br/>

    <?php if ($row): ?>
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
                <tr>
                    <th scope="row"><?= (int)$row['id'] ?></th>
                    <td><?= h($row['nombre']) ?></td>
                    <td><?= h($row['marca']) ?></td>
                    <td><?= h($row['modelo']) ?></td>
                    <td><?= number_format((float)$row['precio'], 2) ?></td>
                    <td><?= (int)$row['unidades'] ?></td>
                    <!-- quitamos utf8_encode: ya usamos utf8mb4 y meta UTF-8 -->
                    <td><?= h($row['detalles']) ?></td>
                    <td>
                        <?php if (!empty($row['imagen'])): ?>
                            <img src="<?= h(web_path($row['imagen'])) ?>"
                                 alt="<?= h('Imagen de '.$row['nombre']) ?>"
                                 style="max-width:160px;height:auto;" />
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php elseif ($id !== null): ?>
        <script>alert('El ID del producto no existe');</script>
    <?php endif; ?>
</body>
</html>
