<?php

${basename(__FILE__, '.php')} = function () {

    if ($this->paramsExists(['roleName', 'description', 'roleCategory'])) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $role = new Role();

        try {
            $roleName = $this->_request['roleName'];
            $roleCategory = $this->_request['roleCategory'];
            $description = $this->_request['description'];

            // Create the role and get the roleId
            $roleId = $role->createRole($roleName,$roleCategory ,$description);

            // Convert MongoDB ObjectId to string if necessary
            if ($roleId instanceof MongoDB\BSON\ObjectId) {
                $roleId = (string)$roleId;
            }

            // Successful creation response
            $this->response(
                $this->json([
                    'success' => true,
                    'message' => 'Role created successfully',
                    'roleId' => $roleId
                ]),
                200
            );
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();

            // Handle specific errors
            if ($errorMessage === 'Role already exists') {
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
                'message' => 'Missing required parameters'
            ]),
            400 // Bad Request
        );
    }
};
