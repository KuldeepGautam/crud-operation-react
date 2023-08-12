<?php

$frombody = json_encode(file_get_contents('php://input'));
// $frombody = file_get_contents('php://input');

echo "$frombody\r\n";
var_dump(strpos($frombody, ',\r\n')) . "\r\n";

echo $frombody[50];
