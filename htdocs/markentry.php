<?php

include 'libs/load.php';


Session::ensureLogin();

if(Session::ensureRole('faculty') || Session::ensureRole('admin')) {
    Session::renderPage();
} else {
    header("Location: /");
    die();
}

