<?php

$faculty = new Faculty();

$faculty_id = $faculty->getFacultyId();

$code = base64_decode($_GET['code']);
$testname = base64_decode($_GET['testname']);
$batch = base64_decode($_GET['batch']);
$semester = base64_decode($_GET['semester']);
$maxmarks = $_GET['maxmark'];

Session::loadTemplate('/app/_entermarks', [$testname, $code, $batch, $semester, $maxmarks]);