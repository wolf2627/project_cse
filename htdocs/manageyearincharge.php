<?php

include 'libs/load.php';

Session::ensureLogin();
Session::renderPage(['title' => 'Manage Year Incharge']);