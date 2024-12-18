<?php

${basename(__FILE__, '.php')} = function () {

    if ($this->paramsExists(['roleId'])) { //'permissionsID'

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $permission = new Permission();

        try {

            $roleId = $this->_request['roleId'];
            $permissionsID = isset($this->_request['permissionsID']) &&
                ((is_array($this->_request['permissionsID']) && count($this->_request['permissionsID']) > 0) ||
                    (is_string($this->_request['permissionsID']) && strlen($this->_request['permissionsID']) > 0))
                ? $this->_request['permissionsID']
                : [];


            // Grant the permission to the role
            $result = $permission->assignPermissionToRole($roleId, $permissionsID);

            $finalResult = [
                'success' => true,
                'message' => $result
            ];

            // Successful creation response
            $this->response(
                $this->json($finalResult),
                200
            );
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            if (preg_match('/\b(?:not|no)\b/i', $errorMessage)) {
                $this->response(
                    $this->json([
                        'success' => false,
                        'message' => $errorMessage
                    ]),
                    404 // Internal Server Error
                );
            } else {
                $this->response(
                    $this->json([
                        'success' => false,
                        'message' => $errorMessage
                    ]),
                    500
                );
            }
        }
    } else {
        // Bad request response Missing Parameters
        $this->response(
            $this->json([
                'success' => false,
                'message' => 'Bad request'
            ]),
            400 // Bad Request
        );
    }
};
