<?php

class User
{
    // use SQLGetterSetter;
    private $conn;
    private $username;
    private $collection;

    /*
    * Constructor gets username and stores it to $username variable.
    * This stored $username can be used to do other stuffs. 
    * User Object can be constructed with Username.
    */

    public function __construct($username)
    {

        // echo "User Object Constructed with username: $username";
        $this->conn = Database::getConnection();
        
        $this->collection = $this->conn->Auth;

        $search_data = [
            '$or' => [  // Use $or here instead of "or"
                ['username' => $username],
                ['email' => $username]  // If you want to search for either username or email
            ]
        ];

        try {
            $result = $this->collection->findOne($search_data);

            if(!$result){
                throw new Exception("User::__construct() -> Username not found.");
            }
            
            $this->username = $result->username;
            
        } catch (Exception $e) {
            // echo "Error: " . $e->getMessage();
            throw new Exception("User::__construct() -> Username not found.");
        }
    }


    public static function createUserLogin($username, $email, $password)
    {
        $conn = Database::getConnection();

        $collection = $conn->Auth;

        $options = [
            'cost' => 9,
        ];

        $password = password_hash($password, PASSWORD_BCRYPT, $options); //most secure and prefered way for password saving, suggested by official php

        $data = [
            'username' => $username,
            'email' => $email,
            'password' => $password,

        ];
        try {
            $result = $collection->insertOne($data);
            return true;
        } catch (Exception $e) {
            // echo "Error: " . $e->getMessage();
            return false;
        }
    }



    public static function login($username, $pass)
    {
        //echo "checklogin called..";
        // $query = "SELECT * FROM `auth` WHERE `username` = '$username' or `email` = '$username'";
        $conn = Database::getConnection();

        $collection = $conn->Auth;
        $result = $collection->findOne(['username' => $username]);
        //echo $query;
        if ($result) {
            $row = Database::getArray($result);
            if (password_verify($pass, $row['password'])) { //most secure and prefered way for password saving, suggested by official php
                /* TODO
                1. Generate Session Token.
                2. Insert Session Token
                3. Build Session and session to user.
                */
                //echo $row['username'];
                //echo $row['password'];
                return $row['username']; //returning username on successful login.
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    // Testing Functions.
    // private function _get_data($property)
    // {
    //     return isset($this->properties[$property]) ? $this->properties[$property] : null;
    // }

    // private function _set_data($property, $value)
    // {
    //     $this->properties[$property] = $value;
    //     return $this;
    // }


    //Updates data to database. (invoked by __call method).

    // public function _set_data($key, $value)
    // {
    //     if (!$this->conn) {
    //         $this->conn = Database::getConnection();
    //     }
    //     $query = "UPDATE `auth` SET `$key` = '$value' WHERE `id` = '$this->id';";
    //     if ($this->conn->query($query) === TRUE) {
    //         return True;
    //     } else {
    //         //echo "Error updating record: " . $this->conn->error;
    //         return False;
    //     }
    // }

    // //Retrives data from database. (invoked by __call method).
    // public function _get_data($key)
    // {
    //     if (!$this->conn) {
    //         $this->conn = Database::getConnection();
    //     }
    //     $query = "SELECT `$key` FROM `auth` WHERE `id` = '$this->id'";
    //     //echo $query;
    //     $result = $this->conn->query($query);
    //     if ($result->num_rows == 1) {
    //         $row = $result->fetch_assoc();
    //         $result = $row[$key];
    //         return $result;
    //     } else {
    //         return false;
    //     }
    // }

    /* 
    * Since Date of Birth has special format, a override method is written for updating Dob. 
    * However it formats the dob and invokes _set_data method to update.
    */
    // public function setDob($month, $day, $year)
    // {
    //     if (checkdate($month, $day, $year)) {
    //         return $this->_set_data("dob", $month, $day, $year);
    //     } else {
    //         return false;
    //     }
    // }

    /*
    * Since Username is not in the User table, Override method for getting Username from auth table.
    * Here this username is given by user and verified indirectly in constructor and stored in $username variable.
    */

    // public function getUsername()
    // {
    //     return $this->username;
    // }

    //Below comments are sample comments, that represents what if magic methods are not available.

    // public function setBio($bio)
    // {
    //     $this->_set_data("bio", $bio);
    // }

    // public function getBio()
    // {
    //     return $this->_get_data("bio");
    // }

    // public function setAvatar($link)
    // {
    //     $this->_set_data("link", $link);
    // }

    // public function getAvatar()
    // {
    //     return $this->_get_data("avatar");
    // }

    // public function setFirstname($firstname)
    // {
    //     $this->_set_data("firstname", $firstname);
    // }

    // public function getFirstname()
    // {
    //     return $this->_get_data("firstname");
    // }

    // public function setLastname($lastname)
    // {
    //     $this->_set_data("lastname", $lastname);
    // }

    // public function getLastname()
    // {
    // }

    // public function getDob()
    // {
    // }

    // public function setInstagram($instagram)
    // {
    //     $this->_set_data("instagram", $instagram);
    // }

    // public function getInstagram()
    // {
    // }

    // public function setTwitter()
    // {
    // }

    // public function getTwitter()
    // {
    // }

    // public function setFacebook()
    // {
    // }

    // public function getFacebook()
    // {
    // }
}
