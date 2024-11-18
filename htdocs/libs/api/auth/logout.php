<?php

// https://domain/api/auth/logout
${basename(__FILE__, '.php')} = function () {
    if (Session::isset("session_token")) {
        $session = new UserSession(Session::get('session_token'));
        if ($session->removeSession()) {
            echo "Previous Session is removed from db";
        } else {
            echo "Previous Session is not removed from db";
        }
    }
    Session::destroy();
    header("Location: /");
    die();
};
