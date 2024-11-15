<?php

include 'vendor/autoload.php';

use MongoDB\BSON\UTCDateTime;

class UserSession
{
    private $conn;
    private $token;
    private $data;
    private $username;
    private $collection;

    /*
     * This function will return a session ID if username and password is correct.
     * @return SessionID
     */
    public static function authenticate($user, $pass, $fingerprint = null)
    {
        if ($fingerprint == null) {
            $fingerprint = $fingerprint = $_COOKIE['fingerprint'];
        }

        if ($fingerprint == null) {
            throw new Exception("Fingerprint is null");
        }

        $username = User::login($user, $pass);
        if ($username) {
            $user = new User($username);
            $conn = Database::getConnection();

            $collection = $conn->session;

            $ip = $_SERVER['HTTP_X_REAL_IP'];
            $agent = $_SERVER['HTTP_USER_AGENT'];

            $token = md5(rand(0, 9999999) . $ip . $agent . time());
            //TODO: Check new MongoDate() is correct or not.
            $now = new DateTime();
            $now = $now->format('Y-m-d H:i:s');
            $IST_time = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
            $IST_time = $IST_time->format('Y-m-d H:i:s');
            try {
                $result = $collection->insertOne(['username' => $username, 'token' => $token, 'login_time' => $now, 'login_time_in' => $IST_time ,'ip' => $ip, 'user_agent' => $agent, 'active' => 1, 'fingerprint' => $fingerprint]);
                if ($result) {
                    Session::set('session_token', $token);
                    return $token;
                } else {
                    return false;
                }
            } catch (Exception $e) {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function verify_login($password){
        
    }


    /*
    * Authorize function have has 4 level of checks 
        1.Check that the IP and User agent field is filled.
        2.Check if the session is correct and active.
        3.Check that the current IP is the same as the previous IP
        4.Check that the current user agent is the same as the previous user agent

        @return true else false;
    */

    public static function authorize($token)
    {
        try {
            $session = new UserSession($token);
            if (isset($_SERVER['HTTP_X_REAL_IP']) and isset($_SERVER["HTTP_USER_AGENT"])) {
                if ($session->isValid() and $session->isActive()) {
                    if ($_SERVER['HTTP_X_REAL_IP'] == $session->getIp()) {
                        if ($_SERVER['HTTP_USER_AGENT'] == $session->getUserAgent()) {
                            if ($session->getFingerprint() == $_COOKIE['fingerprint']) { //TODO: This is always True, fix it.
                                Session::$user = $session->getUser();
                                return $session;
                            } else {
                                throw new Exception("Fingerprint doesn't match");
                            }
                        } else {
                            throw new Exception("User agnet doesn't match");
                        }
                    } else {
                        throw new Exception("Ip doesn't match");
                    }
                } else {
                    throw new Exception("Invalide Session");
                }
            } else {
                throw new Exception("authorize: ip and useragent are null");
            }
        } catch (Exception $e) {
            throw new Exception("Something went wrong");
        }
    }

    public function __construct($token)
    {
        //print("token : $token");
        $this->conn = Database::getConnection();
        $this->token = $token;
        $this->data = null;
        // $query = "SELECT * FROM `session` WHERE `token` = '$token'";
        $this->collection = $this->conn->session;
        try {
            $result = $this->collection->findOne(['token' => $token]);
            if ($result) {
                $row = Database::getArray($result);
                $this->data = $row;
                // print_r($row);
                $this->username = $row['username'];
            } else {
                throw new Exception("UserSession::__construct -> Session is invalid.");
            }
        } catch (Exception $e) {
            throw new Exception("UserSession::__construct -> Session is invalid.");
        }
    }

    public function getUser()
    {
        return new User($this->username);
    }

    /**
     * Check if the validity of the session is within one hour, else make it inactive.
     * @return Boolean
     */

     public function isValid()
     {
         // Check if the fingerprint matches
         if ($_COOKIE['fingerprint'] != $this->getFingerprint()) {
             return false;
         }
     
         // Check if login_time exists
         if (isset($this->data['login_time'])) {
             // MongoDB stores Date objects, so ensure you handle it properly
             if ($this->data['login_time'] instanceof MongoDB\BSON\UTCDateTime) {
                 // Convert MongoDB UTCDateTime to PHP DateTime
                 $login_time = $this->data['login_time']->toDateTime();
             } else {
                 // If it's stored as a string, create DateTime from string
                 $login_time = DateTime::createFromFormat('Y-m-d H:i:s', $this->data['login_time']);
             }
     
             // Check if the session is still valid (3600 seconds, i.e., 1 hour)
             if ($login_time && (time() - $login_time->getTimestamp()) < 3600) {
                 // echo "Session is valid";
                 return true;
             } else {
                 // Session has expired, deactivate
                 $this->deactivate();
                 return false;
             }
         } else {
             // Handle the case where login_time is not set
             throw new Exception("UserSession::isValid -> login time is null");
         }
     }
     

    public function isActive()
    {
        if (isset($this->data['active'])) {
            return $this->data['active'] ? true : false;
        }
    }

    public function getIp()
    {
        return isset($this->data['ip']) ? $this->data['ip'] : false;
    }

    public function getUserAgent()
    {
        return isset($this->data['user_agent']) ? $this->data['user_agent'] : false;
    }

    public function deactivate()
    {
        if (isset($this->data['token'])) {
            $id = $this->data['token'];
            if (!$this->conn) {
                $this->conn = Database::getConnection();
            }
            try {
                $result = $this->collection->findOneAndUpdate(['token' => $this->data['token']], ['$set' => ['active' => 0]]);
                // echo "Session deactivated";
                return $result ? true : false;
            } catch (Exception $e) {
                return false;
            }
        }
    }

    public function getFingerprint()
    {
        if (isset($this->data['fingerprint'])) {
            return $this->data['fingerprint'] ? true : false;
        }
    }

    public function removeSession()
    {
        if (isset($this->data['username'])) {
            $username = $this->data['username'];
            if (!$this->conn) {
                $this->conn = Database::getConnection();
            }
            try {
                $result = $this->collection->findOneAndDelete(['username' => $username]);
                return $result ? true : false;
            } catch (Exception $e) {
                return false;
            }
        }
    }
}
