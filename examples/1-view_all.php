<?php

require __DIR__ . '/config.php';

$all = Dannsheet::view('Sheet1');
echo Dannsheet::prettyShow($all);