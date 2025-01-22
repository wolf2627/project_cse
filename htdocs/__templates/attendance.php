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
    echo "You are not authorized to view this page";
}
