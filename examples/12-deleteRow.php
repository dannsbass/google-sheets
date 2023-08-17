<?php

require __DIR__ . '/config.php';

$rows = Dannsheet::findRowByValue('Eko', 'Sheet1!A1:B');
if (!$rows) exit('row not found' . PHP_EOL);
foreach ($rows as $row) {
    Dannsheet::deleteRow('Sheet1', $row);
}
