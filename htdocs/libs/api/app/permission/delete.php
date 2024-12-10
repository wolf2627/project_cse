<?php

${basename(__FILE__, '.php')} = function () {
    if ($this->paramsExists(['permission_id'])) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $permission = new Permission();

        try {
            $permissionId = $this->_request['permission_id'];

            // Delete the permission
            $permission->deletePermission($permissionId);

            // Successful deletion response
            $this->response(
                $this->json([
                    'success' => true,
                    'message' => 'Permission deleted successfully'
                ]),
                200
            );
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();

            // Handle specific errors
            if ($errorMessage === 'Permission not found') {
                $this->response(
                    $this->json([
                        'success' => false,
                        'message' => $errorMessage
                    ]),
                    404 // Not Found status code
                );
            } else {
                // Generic error response
                $this->response(
                    $this->json([
                        'success' => false,
                        'message' => $errorMessage
                    ]),
                    500 // Internal Server Error
                );
            }
        }
    } else {
        // Bad request response
        $this->response(
            $this->json([
                'success' => false,
                'message' => 'Bad request'
            ]),
            400 // Bad Request status code
        );
    }
};