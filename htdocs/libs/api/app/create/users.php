<?php

// https://domain/api/users/createuser
${basename(__FILE__, '.php')} = function () {
    if ($this->paramsExists(['userType', 'role']) and isset($_FILES['users_file'])) {
        
        if(!Session::isAuthenticated()){
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }
        $userType = $this->_request['userType'];
        $role = $this->_request['role'];
        $file = $_FILES['users_file']['tmp_name'];
        $app = new AppUser();
        $creation_result = $app->createUser($userType, $role, $file);
        
        if ($creation_result) {
            $result = [
                'message' => 'Users created successfully',
                'successCount' => $creation_result['success'],
                'failureCount' => $creation_result['failure']
            ];
            $this->response($this->json($result), 200);
        }
    } else {
        $this->response($this->json(['message' => 'bad request']), 400);
    }
};
