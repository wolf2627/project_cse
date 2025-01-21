<?php

${basename(__FILE__, '.php')} = function () {

    if ($this->paramsExists(['permission_name', 'permission_category', 'description'])) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $permission = new Permission();

        try {
            $permissionName = $this->_request['permission_name'];
            $description = $this->_request['description'];
            $category = $this->_request['permission_category'];

            // Create the permission and get the permissionId
            $permissionId = $permission->createPermission($permissionName, $category, $description);

            // Convert MongoDB ObjectId to string if necessary
            if ($permissionId instanceof MongoDB\BSON\ObjectId) {
                $permissionId = (string)$permissionId;
            }

            // Successful creation response
            $this->response(
                $this->json([
                    'success' => true,
                    'message' => 'Permission created successfully',
                    'permissionId' => $permissionId
                ]),
                200
            );
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();

            // Handle specific errors
            if ($errorMessage === 'Permission already exists') {
                $this->response(
                    $this->json([
                        'success' => false,
                        'message' => $errorMessage
                    ]),
                    409 // Conflict status code
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
            400 // Bad Request
        );
    }
};
