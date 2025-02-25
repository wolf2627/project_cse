<?php

include 'libs/load.php';


Session::$isError = True;
Session::renderPage(['title' => 'Error']);
