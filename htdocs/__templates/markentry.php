<?php

$faculty = new Faculty();

$faculty_id = $faculty->getFacultyId();

$code = $_GET['code'];
$testname = $_GET['testname'];

Session::loadTemplate('/app/_entermarks', [$faculty_id, $code, $testname]);