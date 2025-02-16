<?php

if (isset($_GET['contestid'])) {
    $contestId = base64_decode($_GET['contestid']);
}


if (Session::getUser()->getRole() == 'student') {
    $userId = Session::getUser()->getRegNo();
} else if (Session::getUser()->getRole() == 'admin') {
    $userId = Session::getUser()->getAdminId();
} else {
    $userId = Session::getUser()->getFacultyId();
}

$contest = new Contest($contestId);
$isCoordinator = $contest->isCoordinator($userId);

if ($isCoordinator) {
    Session::loadTemplate('contest/_coordinator', ['contestId' => $contestId, 'userId' => $userId]);
} else {
    if (Session::getUser()->getRole() == 'admin') {
        Session::loadTemplate('contest/_admin');
    } else if (Session::getUser()->getRole() == 'faculty') {
        Session::loadTemplate('contest/_faculty', ['contestId' => $contestId]);
    } else {
        Session::loadTemplate('contest/_student', ['contestId' => $contestId]);
    }
}
