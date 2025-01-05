<?php

${basename(__FILE__, '.php')} = function () {

    if ($this->paramsExists(['category', 'user_id'])) { //'roles_id'

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $category = $this->_request['category'];
        $user_id = $this->_request['user_id'];
        
        $roles_id = isset($this->_request['roles_id']) &&
            ((is_array($this->_request['roles_id']) && count($this->_request['roles_id']) > 0) ||
                (is_string($this->_request['roles_id']) && strlen($this->_request['roles_id']) > 0))
            ? $this->_request['roles_id']
            : [];

        error_log("Roles ID: " . json_encode($roles_id));

        $role = new Role();

        try {
            $result = $role->assignOtherRoles($category, $user_id, $roles_id);

            $this->response(
                $this->json([
                    'success' => true,
                    'message' => 'Roles assigned successfully',
                    'result' => $result
                ]),
                200
            );
        } catch (Exception $e) {
            $this->response(
                $this->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]),
                500
            );
        }
    } else {
        $this->response(
            $this->json([
                'success' => false,
                'message' => 'Bad request new'
            ]),
            400
        );
    }
};
