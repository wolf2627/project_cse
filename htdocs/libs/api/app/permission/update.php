<?php

${basename(__FILE__, '.php')} = function () {

    if ($this->paramsExists(['permission_id', 'permission_name', 'permission_category','description'])) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $permission = new Permission();

        try {
            $permissionId = $this->_request['permission_id'];
            $permissionName = $this->_request['permission_name'];
            $category = $this->_request['permission_category'];
            $description = $this->_request['description'];

            // Update the permission
            $permission->updatePermission($permissionId, $permissionName, $category ,$description);

            // Successful update response
            $this->response(
                $this->json([
                    'success' => true,
                    'message' => 'Permission updated successfully',
                    'permission_id' => $permissionId,
                    'permission_name' => $permissionName,
                    'permission_category' => $category,
                    'description' => $description

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
                        'message' => $permissionName . ": " . $errorMessage
                    ]),
                    404 // Not Found status code
                );
            } else if ($errorMessage === 'No changes were made to the permission') {
                $this->response(
                    $this->json([
                        'success' => false,
                        'message' => 'No changes were made to the permission'
                    ]),
                    409 // Not Modified status code
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
            $this->json([
                'success' => false,
                'message' => 'Bad request'
            ]),
            400 // Bad Request status code
        );
    }
};