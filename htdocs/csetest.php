<?php

include 'libs/load.php';

Session::ensureLogin();

Session::renderPage(['title' => 'cse test layout']);