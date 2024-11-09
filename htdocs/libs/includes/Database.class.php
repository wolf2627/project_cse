<?php

require 'vendor/autoload.php';

class Database
{
    public static $conn = null;

    public static function getConnection()
    {
        if (Database::$conn == null) {

            $db_user = get_config('db_user');
            $db_password = get_config('db_password');
            $db_host = get_config('db_host');
            $db_port = get_config('db_port');
            $db_authSource = get_config('db_authSource');

            // if Database is not connected, It will establish a new Database Connection
            // mongodb://Charm:akjh%4024238_@mongodb.selfmade.ninja:27017/?authSource=users"
            $uri = "mongodb://$db_user:$db_password@$db_host:$db_port/?authSource=$db_authSource";
            $mongoClient = new MongoDB\Client($uri);
            Database::$conn = $mongoClient->{'Charm_projectcse'};

            if (Database::$conn == null) {
                throw new Exception("Connection to the database failed");
            }
            // echo "Connected to the database";
            return Database::$conn;
        } else {
            // Already If Database is Connected, It will return the Exisiting Connection
            return Database::$conn;
        }
    }

    public static function getCurrentDB()
    {
        $config_json = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/../env.json');
        $config = json_decode($config_json, true);
        return $config['database'];
    }

    public static function getArray($doc)
    {
        return json_decode(json_encode($doc), true);
    }
}
