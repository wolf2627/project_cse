<?php

if(isset($_GET['contestid'])){
    $contestId = base64_decode($_GET['contestid']);
}

if (Session::getUser()->getRole() == 'admin') {
    Session::loadTemplate('contest/_admin');
} else if (Session::getUser()->getRole() == 'faculty') {
    Session::loadTemplate('contest/_faculty', ['contestId' => $contestId]);
} else {
    Session::loadTemplate('contest/_student');
}
