<?php

${basename(__FILE__, '.php')} = function () {

    if ($this->paramsExists(['roleId'])) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $permission = new Permission();

        try {
            $permissionId = $this->_request['roleId'];

            // Get the permission
            $permissionData = $permission->getFormattedPermissionsForRole($permissionId);

            $this->response(
                $this->json([
                    'success' => true,
                    'permission' => $permissionData
                ]),
                200
            );
            
        } catch (Exception $e){
            $errorMessage = $e->getMessage();

            // Handle specific errors
            if ($errorMessage === 'Permission not found' || $errorMessage === 'Role not found') {
                $this->response(
                    $this->json([
                        'success' => false,
                        'message' => $errorMessage
                    ]),
                    404 // Not found status code
                );
            } else {
                // Generic error response
                $this->response(
                    $this->json([
                        'success' => false,
                        'message' => 'An error occurred: ' . $errorMessage
                    ]),
                    500 // Internal Server Error
                );
            }
        }
    } else {
        // Bad request response
        $this->response(
            $this->json(['message' => 'Invalid request']),
            400
        );
    }

};