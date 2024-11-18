<?php

$faculty = new Faculty();

$faculty_id = $faculty->getFacultyId();

$code = base64_decode($_GET['code']);
$testname = base64_decode($_GET['testname']);

Session::loadTemplate('/app/_entermarks', [$testname, $code]);