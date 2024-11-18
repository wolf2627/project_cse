<?php

// https://domain/api/auth/login
${basename(__FILE__, '.php')} = function () {
    if ($this->paramsExists(['user', 'password'])) {
        $user = $this->_request['user'];
        $password = $this->_request['password'];
        $fingerprint = $_COOKIE['fingerprint'];
        $token = UserSession::authenticate($user, $password, $fingerprint);
        if ($token) {
            $result = [
                'message' => 'Authenticated',
                'token' => $token
            ];
            $this->response($this->json($result), 200);
        } else {
            $result = [
                'message' => 'Unauthorized'
            ];
            $this->response($this->json($result), 401);
        }
    } else {
        $this->response($this->json(['message' => 'bad request']), 400);
    }
};
