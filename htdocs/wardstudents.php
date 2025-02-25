<?php

include 'libs/load.php';

Session::ensureLogin();

Session::renderPage(['Title' => 'Ward Students']);