<?php

require __DIR__ .'/config.php';

$all = Dannsheet::view('Sheet1!A2:C2');
echo Dannsheet::prettyShow($all);
