<?php
require_once __DIR__ . '/vendor/autoload.php';

use TECWEB\MYAPI\Delete\Delete;

$delete = new Delete('root', '12345678a', 'marketzone');

$id = $_GET['id'] ?? null;

echo $delete->delete($id);
