<?php

include 'libs/load.php';

Session::ensureLogin();
Session::ensureRole('admin');

Session::renderPage();
