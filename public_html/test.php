<?php
header('Content-Type: text/plain');

$dt = DateTime::createFromFormat("Y-m-d", "2009-03-12");
print_r($dt);

$dt = DateTime::createFromFormat("H:i", "15:03");
print_r($dt);

$dt = DateTime::createFromFormat("Y-m-d", "2009-15-35");
print_r($dt);
?>