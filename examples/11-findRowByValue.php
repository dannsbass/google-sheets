<?php

require __DIR__ . '/config.php';

$row = Dannsheet::findRowByValue('Danns Bass', 'Sheet1!A1:A');
var_dump($row);