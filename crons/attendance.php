<?php

$include_file  = __DIR__. '/../htdocs/libs/load.php';

if(file_exists($include_file)) {
    require_once $include_file;
} else {
    echo 'File not found.';
    exit;
}
    

$crons = new Crons();
$crons->updateAttendance();