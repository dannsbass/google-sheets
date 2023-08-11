<?php

require __DIR__ . '/config.php';

// contoh 1
$updateRow = ['ID', 'NAMA', 'ALAMAT'];
$range = 'Sheet1!A1:C1';

// contoh 2
$updateRow = ['456', 'Muhammad Usamah', 'Karanganyar Jateng'];
$range = 'Sheet1!A3';

// contoh 3
$updateRow = ['Jati Blora Jateng'];
$range = 'Sheet1!C2:C2';

$rows = [$updateRow];

Dannsheet::update($rows, $range);


