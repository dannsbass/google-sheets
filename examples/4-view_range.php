<?php

require __DIR__ .'/config.php';

$values = Dannsheet::view('Sheet1!A1:C');
echo Dannsheet::prettyShow($values);

