<?php

require __DIR__ . '/config.php';

$sheet_title = 'SedotPDFBot';
$values = Dannsheet::getValues($sheet_title);
foreach ($values as $array) {
    foreach ($array as $key => $value) {
        file_put_contents("$sheet_title.txt", $value . "|", FILE_APPEND);
    }
    file_put_contents("$sheet_title.txt", "\n", FILE_APPEND);
}