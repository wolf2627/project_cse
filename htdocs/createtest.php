<?php

require_once 'libs/load.php';

Session::ensureLogin();

if(Session::get('role') == 'admin') {
    Session::renderPage(['title' => 'Create Test']);
} else {
    Session::loadTemplate("_error");
}