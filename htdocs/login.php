<?php
include 'libs/load.php';


if(Session::isAuthenticated()){
    header("Location: /dashboard");
    die();
}
Session::renderPage(['title' => 'Login']);
