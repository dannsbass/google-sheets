<?php

require __DIR__ . '/vendor/autoload.php';

Dannsheet::setCredentials(__DIR__ . '/data/credentials.json');
Dannsheet::setSpreasheetId('1_cQzy-S0YTtQOdsqoLJL0Y96N0A3x5vwIdLr6MwbgMU');

function p($range){
    return Dannsheet::prettyPrint($range);
}