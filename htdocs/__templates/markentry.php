<?php

$faculty = new Faculty();

$faculty_id = $faculty->getFacultyId();

$code = 'GE2C25';

Session::loadTemplate('/app/_entermarks', [$faculty_id, $code]);