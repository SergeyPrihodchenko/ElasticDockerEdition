<?php

$data = file_get_contents(__DIR__ . '/HP-2.pdf');

$test = preg_match('применений', $data);

var_dump($test);