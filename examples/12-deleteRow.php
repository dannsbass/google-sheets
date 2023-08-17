<?php

require __DIR__ . '/config.php';

$row = Dannsheet::findRowByValue('Budi', 'Sheet1!A1:B');
if(!$row) exit('row not found' . PHP_EOL);
Dannsheet::deleteRow($row, $row, 'Sheet1');
