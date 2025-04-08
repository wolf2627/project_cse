<?php

if (Session::get('role') == 'student') {
    $student_id = Session::getUser()->getRegNo();
    if (isset($_GET['atye'])) {
        $token = $_GET['atye'];
        if ($token == base64_encode('sw')) {
            Session::loadTemplate('app/attendance/student/_subwise', ['student_id' => $student_id]);
        } else if ($token == base64_encode('at')) {
            // echo "loading att";
            Session::loadTemplate('app/attendance/student/_att', ['student_id' => $student_id]);
        } else {
            Session::loadTemplate('_error');
        }
    } else {
        Session::loadTemplate('_error');
    }
} else {
    if(Session::get('role') == 'faculty') { // check if the user is a tutor

        $student_id = $_GET['student_id'];
        if (isset($_GET['atye'])) {
            $token = $_GET['atye'];
            if ($token == base64_encode('sw')) {
                Session::loadTemplate('app/attendance/student/_subwise', ['student_id' => $student_id]);
            } else if ($token == base64_encode('at')) {
                // echo "loading att";
                Session::loadTemplate('app/attendance/student/_att', ['student_id' => $student_id]);
            } else {
                Session::loadTemplate('_error');
            }
        } else {
            Session::loadTemplate('_error');
        }
    } else {
        Session::loadTemplate('_error');
    }
}
