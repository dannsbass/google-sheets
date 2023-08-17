<?php

require __DIR__ . '/config.php';

Dannsheet::deleteSheetByNumber(2); // third sheet, index starts from 0
Dannsheet::deleteSheetById(9876543210);
Dannsheet::deleteSheetByTitle('Sheet1'); 