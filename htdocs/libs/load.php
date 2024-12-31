<?php

include 'vendor/autoload.php';
use MongoDB\BSON\UTCDateTime;

include_once 'includes/WebAPI.class.php';
include_once 'includes/Session.class.php';
include_once 'includes/Database.class.php';
include_once 'includes/User.class.php';
include_once 'includes/UserSession.class.php';
include_once 'includes/REST.class.php';
include_once 'includes/API.class.php';
include_once 'includes/Log.class.php';

include_once 'app/AppUser.class.php';
include_once 'app/essentials.class.php';
include_once 'app/Admin.class.php';

include_once 'app/Classes.class.php';
include_once 'app/Test.class.php';

include_once 'app/Faculty.class.php';
include_once 'app/Creator.class.php';
include_once 'app/Role.class.php';
include_once 'app/Marks.class.php';
include_once 'app/Permission.class.php';

include_once 'app/Access.class.php';

include_once 'app/reports/ClassReport.class.php';

global $__site_config;

$wapi = new WebAPI();
$wapi->initiateSession();

if(!$__site_config){
    die("Unable to Read Configuration");
}

function get_config($key, $default = null)
{
    global $__site_config;
    $array = json_decode($__site_config, true);
    if (isset($array[$key])) {
        return $array[$key];
    } else {
        return $default;
    }
}