<?php
require_once __DIR__ . '/vendor/autoload.php';

use TECWEB\MYAPI\Read\Read;

$read = new Read('root', '12345678a', 'marketzone');

echo $read->list();
