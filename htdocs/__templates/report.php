<?php

$test_id = base64_decode($_GET['testid']); // Test ID to be passed
$testname = base64_decode($_GET['testname']); // Test ID to be passed
$department = base64_decode($_GET['dept']); // Test ID to be passed

Session::loadTemplate('app/reports/_profInReport', [$test_id, $testname, $department]);