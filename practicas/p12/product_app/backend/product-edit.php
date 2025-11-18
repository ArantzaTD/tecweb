<?php
require_once __DIR__ . '/vendor/autoload.php';

use TECWEB\MYAPI\Update\Update;

$update = new Update('root', '12345678a', 'marketzone');

$json = file_get_contents('php://input');
$obj  = json_decode($json);

echo $update->edit($obj);
