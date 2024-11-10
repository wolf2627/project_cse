<?php
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
        //echo "authenticate called..";
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

            try {
                $result = $collection->insertOne(['username' => $username, 'token' => $token, 'login_time' => time(), 'ip' => $ip, 'user_agent' => $agent, 'active' => 1, 'fingerprint' => $fingerprint]);
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
            if (isset($_SERVER['REMOTE_ADDR']) and isset($_SERVER["HTTP_USER_AGENT"])) {
                if ($session->isValid() and $session->isActive()) {
                    if ($_SERVER['REMOTE_ADDR'] == $session->getIp()) {
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
                //print_r($row);
                $this->username = $row['username'];
                // echo "user Object Constructed for id: $this->id";
            } else {
                //echo "Session Expired.";
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
        if ($_COOKIE['fingerprint'] == $this->getFingerprint()) {
            return true;
        } else {
            return false;
        }

        if (isset($this->data['login_time'])) {
            $login_time = DateTime::createFromFormat('Y-m-d H:i:s', $this->data['login_time']);
            if (3600 > time() - $login_time->getTimestamp()) {
                return true;
            } else {
                return false;
            }
        } else {
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
        if (isset($this->data['username'])) {
            $id = $this->data['username'];
            if (!$this->conn) {
                $this->conn = Database::getConnection();
            }
            try {
                $result = $this->collection->findOneAndUpdate(['username' => $this->username], ['$set' => ['active' => 0]]);
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
