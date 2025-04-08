<?php

//require 'vendor/autoload.php';

class Database
{
    public static $conn = null;
    public static $session = null; // Store session reference
    private static $mongoClient = null;

    public static function getConnection()
    {
        if (Database::$conn == null) {

            // $db_user = get_config('db_user');
            // $db_password = get_config('db_password');
            // $db_host = get_config('db_host');
            // $db_port = get_config('db_port');
            // $db_authSource = get_config('db_authSource');
            $db_name = get_config('db_name');
            $db_uri = get_config('db_uri');

            try {
                // Establish a new Database Connection
                //$uri = "mongodb://$db_user:$db_password@$db_host:$db_port/?authSource=$db_authSource";
                $uri = $db_uri;
                Database::$mongoClient = new MongoDB\Client($uri);
                Database::$conn = Database::$mongoClient->{$db_name};

                if (Database::$conn == null) {
                    throw new Exception("Connection to the database failed");
                }

                return Database::$conn;
            } catch (Exception $e) {
                die("Error Occurred: " . $e->getMessage());
            }
        } else {
            // Return existing connection
            return Database::$conn;
        }
    }

    // These functions are used to manage transactions and sessions. 
    // But they are not used in the current implementation.
    // They can be used only when replication is enabled in MongoDB.

    public static function startTransaction()
    {
        // Ensure the MongoDB client and connection are initialized
        if (Database::$mongoClient === null) {
            Database::getConnection();
        }

        // Start a session and transaction if not already started
        if (Database::$session === null) {
            Database::$session = Database::$mongoClient->startSession();
            Database::$session->startTransaction();
        }
    }

    // Method to commit the transaction
    public static function commitTransaction()
    {
        if (Database::$session !== null) {
            try {
                Database::$session->commitTransaction();
            } catch (Exception $e) {
                // Handle transaction commit error
                echo "Transaction commit failed: " . $e->getMessage();
            }
        }
    }

    // Method to abort the transaction
    public static function abortTransaction()
    {
        if (Database::$session !== null) {
            try {
                Database::$session->abortTransaction();
            } catch (Exception $e) {
                // Handle transaction abort error
                echo "Transaction abort failed: " . $e->getMessage();
            }
        }
    }

    // Method to end the session
    public static function endSession()
    {
        if (Database::$session !== null) {
            try {
                Database::$session->endSession();
                Database::$session = null; // Clear session reference
            } catch (Exception $e) {
                // Handle session end error
                echo "Session end failed: " . $e->getMessage();
            }
        }
    }

    public static function getSession()
    {
        return Database::$session;
    }

    // Utility function to convert BSON to Array
    public static function getArray($doc)
    {
        return json_decode(json_encode($doc), true);
    }
}
