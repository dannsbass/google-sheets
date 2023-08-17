<?php

require __DIR__ . '/config.php';

// sample 1
$range = 'Sheet1!A5:C';
Dannsheet::clear($range);

// sample 2
$cells = Dannsheet::findCellByValue('Some text', 'Sheet1!A1:B', false);
if (!$cells) exit('cell not found' . PHP_EOL);
foreach ($cells as $cell) {
    Dannsheet::clear("Sheet1!$cell");
}


