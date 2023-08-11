<?php

require __DIR__ . '/config.php';

$rows[] = [
    '987',
    'Syamsiah Syahruddin',
    'Batubara Sumut',
];

$rows[] = [
    '789',
    'Rumaysha Tasnim',
    'Karanganyar Jateng',
];

$result = Dannsheet::appendRow($rows, 'Sheet1');