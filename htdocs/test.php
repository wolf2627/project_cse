<?php 

require_once 'libs/load.php';

$email = Session::getUser()->getName();

echo $email;