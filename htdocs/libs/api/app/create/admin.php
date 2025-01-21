<?php

${basename(__FILE__, '.php')} = function () {

    if($this->paramsExists(['user', 'password', 'email'])) {

        // Ensure the user is authenticated
        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
       }

        if(!Session::ensureRole('admin', true)) {
            $this->response($this->json(['message' => 'Role Unauthorized']), 401);
        }

        $user = $this->_request['user'];
        $password = $this->_request['password'];
        $email = $this->_request['email'];

        try {
            $result = AppUser::createAdmin($user, $password, $email);

            if($result) {
                $result = [
                    'message' => 'success',
                    'cerenditials' => $result
                ];
                $this->response($this->json($result), 200);
            }
        } catch (Exception $e) {
            $this->response($this->json(['message' => $e->getMessage()]), 500);
        
        } 
    } else {
        $this->response($this->json(['message' => 'bad request']), 400);
    }

};