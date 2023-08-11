<?php

require __DIR__ . '/config.php';

$spreadsheet = Dannsheet::getSpreadsheet();
$count = count($spreadsheet);

if($count == 0) exit("Spreadsheet not found\n");

echo "$count sheet(s) found\n";

$mask = "%10s %-15s\n";
echo sprintf($mask, "Sheet ID", "Name");
foreach ($spreadsheet as $sheet) {
    echo sprintf($mask, $sheet->properties->sheetId, $sheet->properties->title);
}