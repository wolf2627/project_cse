<?php

class WebAPI
{
    public function __construct()
    {
        // This is requred when project is in root. 

        // if (php_sapi_name() == "cli") {
        //     global $__site_config;
        //     $__site_config_path = "/home/wolf/htdocs/app/project/photogramconfig.json";
        //     $__site_config = file_get_contents($__site_config_path);
        //     print($__site_config);
        // } else if (php_sapi_name() == "apache2handler") {
        //     global $__site_config;
        //     $__site_config_path = dirname(is_link($_SERVER['DOCUMENT_ROOT']) ? readlink($_SERVER['DOCUMENT_ROOT']) : $_SERVER['DOCUMENT_ROOT']) . '/project/photogramconfig.json';
        //     $__site_config = file_get_contents($__site_config_path);
        // }
        
        global $__site_config;
        $__site_config_path = __DIR__."/../../../env.json";
        $__site_config = file_get_contents($__site_config_path);

        if(get_config('database') == "enable"){
            Database::getConnection(); // Initialize the database connection
        } 
    }

    public function initiateSession()
    {
        Session::start();
        if(Session::isset("session_token")){
            try{
                Session::$usersession = UserSession::authorize(Session::get("session_token"));
            } catch (Exception $e){
                //TODO: Handle error
            }
        }
    }
}
