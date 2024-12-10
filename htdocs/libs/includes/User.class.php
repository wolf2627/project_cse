<?php

class User
{
    private $conn;
    private $user_id;
    private $collection;

    /**
     * Constructor gets user_id and role, initializes the MongoDB collection.
     */
    public function __construct($user_id, $role)
    {
        $this->conn = Database::getConnection();

        // Choose the collection based on the user role
        switch ($role) {
            case 'student':
                $this->collection = $this->conn->students;
                break;
            case 'faculty':
                $this->collection = $this->conn->faculties;
                break;
            case 'admin':
                $this->collection = $this->conn->admins;
                break;
            default:
                throw new Exception("User::__construct() -> Role not found.");
        }

        // Search data to match user by user_id, username, or email
        $search_data = [
            '$or' => [
                ['_id' => new MongoDB\BSON\ObjectId($user_id)],
                ['user' => $user_id],
                ['email' => $user_id]
            ]
        ];

        try {
            $result = $this->collection->findOne($search_data);
            if (!$result) {
                throw new Exception("User::__construct() -> User not found.");
            }
            $this->user_id = (string)$result->_id;  // Store user ID for further operations
        } catch (Exception $e) {
            throw new Exception("User::__construct() -> " . $e->getMessage());
        }
    }

    /**
     * Handles user login by verifying password.
     */
    public static function login($username, $pass)
    {
        $conn = Database::getConnection();
        $collection = $conn->Auth;
        $result = $collection->findOne(['username' => $username]);

        if ($result) {
            $row = Database::getArray($result);
            if (password_verify($pass, $row['password'])) {
                return [
                    'user_id' => (string)$row['user_id']['$oid'], 
                    'role' => $row['role'], 
                    'username' => $row['username']
                ];
            }
        }
        return false;  // Return false if login fails
    }

    /**
     * Magic method to dynamically handle getter and setter calls.
     */
    public function __call($name, $arguments)
    {
        $property = preg_replace("/[^0-9a-zA-Z]/", "", substr($name, 3)); // Extract property name
        $property = strtolower(preg_replace('/\B([A-Z])/', '_$1', $property)); // Convert to snake_case

        // Handle getters
        if (substr($name, 0, 3) == "get") {
            return $this->_get_data($property);
        } 
        // Handle setters
        else if (substr($name, 0, 3) == "set") {
            if (isset($arguments[0])) {
                return $this->_set_data($property, $arguments[0]);
            } else {
                throw new Exception("User::__call() -> Missing value for setter method.");
            }
        }
        // Handle invalid method calls
        else {
            throw new Exception("User::__call() -> $name function unavailable.");
        }
    }

    /**
     * Updates data in the database (used by magic setter).
     */
    public function _set_data($key, $value)
    {
        $result = $this->collection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($this->user_id)],
            ['$set' => [$key => $value]]
        );

        // Return true if exactly one document was modified
        return $result->getModifiedCount() === 1;
    }

    /**
     * Retrieves data from the database (used by magic getter).
     */
    public function _get_data($key)
    {
        $result = $this->collection->findOne(['_id' => new MongoDB\BSON\ObjectId($this->user_id)]);
        // print_r($result);
        // Check if the result exists and return the requested field
        if ($result) {
            return isset($result->$key) ? $result->$key : "not found";
        } else {
            echo "User::_get_data() -> User not found. <br>";
            return false;
        }
    }

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
