<?php
/* 
    * This class contains own built methods for php session functions.
    * This allows us to implement the session in our own way which gives higher efficiency.
    * It is a general purpose class, that is used 
 */
class Session
{
    public static $isError = false;
    public static $user = null;
    public static $usersession = null;

    /**
     * This function is used to start the session.
     *
     * @return void
     */
    public static function start()
    {
        session_start();
    }

    /**
     * This function is used to unset the session.
     *
     * @return void
     */
    public static function unset()
    {
        session_unset();
    }

    /**
     * This function is used to destroy the session.
     *
     * @return void
     */
    public static function destroy()
    {
        session_destroy();
    }

    /**
     * This function is used to set the session variable.
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * This function is used to delete the session variable.
     * 
     * @param string $key
     */
    public static function del($key)
    {
        unset($_SESSION[$key]);
    }


    /**
     * This function is used to check if the session variable is set.
     *
     * @param string $key
     * @return void
     */
    public static function isset($key)
    {
        return isset($_SESSION[$key]);
    }


    /**
     * This function is used to get the session variable.
     *
     * @param string $key
     * @return string $value
    */
    public static function get($key, $default = false)
    {
        if (Session::isset($key)) {
            return $_SESSION[$key];
        } else {
            return $default;
        }
    }

    /**
     * This function is used to get the User.
     *
     * @return User
     */
    public static function getUser()
    {
        return Session::$user;
    }

    /**
     * This function is used to get the UserSession.
     *
     * @return UserSession
     */
    public static function getUserSession()
    {
        return Session::$usersession;
    }

    /**
     * This function is used to load the template file.
     * It takes the name of the template file and the data to be passed to the template file.
     * It includes the template file if it exists, otherwise it includes the error template file.
     *
     * @param String $name
     * @param Array $data
     */
    public static function loadTemplate($name, $data = [])
    {
        extract($data);
        $script = $_SERVER["DOCUMENT_ROOT"] . get_config("base_path") . "__templates/$name.php";
        if (is_file($script)) {
            include $script;
        } else {
            Session::loadTemplate('_error');
        }
    }


    /**
     * This function is used to render the page. which is used to load the master template.
     *
     * @return void
     */
    public static function renderPage()
    {
        Session::loadTemplate('_master');
    }

    /**
     * This function is used to get the current script.
     *
     * @return String
     */

    public static function currentScript()
    {
        return basename($_SERVER["PHP_SELF"], ".php");
    }

    /**
     * This function is used to check if the user is authenticated.
     *
     * @return boolean
     */
    public static function isAuthenticated()
    {
        //TODO: Is it a Correct Implementation, change with instance of User.
        if (is_object(Session::getUserSession())) {
            return Session::getUserSession()->isValid();
        }
        return false;
    }

    /**
     * This function is used to ensure that the user is logged in.
     *
     * @return void
     */
    public static function ensureLogin()
    {
        if (!Session::isAuthenticated()) {
            Session::set('_redirect', $_SERVER['REQUEST_URI']);
            header("Location: /");
            die();
        }
    }

    //TODO: fix this function
    /**
     * Takes an email as input and return true if the current session user is same email.
     *
     * @param String $owner
     * @return boolean
     */
//     public static function isOwnerOf($owner)
//     {
//         $sess_user = Session::getUser();
//         if ($sess_user) {
//             return $sess_user->getEmail() == $owner;
//         } else {
//             return false;
//         }
//     }
// }
}