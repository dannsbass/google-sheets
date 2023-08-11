<?php

require __DIR__ . '/config.php';

$range = 'Sheet1!B1:B';
$values = Dannsheet::view($range);
echo Dannsheet::prettyShow($values);

