<?php
require_once __DIR__ . '/vendor/autoload.php';

use TECWEB\MYAPI\Create\Create;

// Instancia: user, password, database
$create = new Create('root', '12345678a', 'marketzone');

$json = file_get_contents('php://input');
$obj  = json_decode($json);

echo $create->add($obj);
