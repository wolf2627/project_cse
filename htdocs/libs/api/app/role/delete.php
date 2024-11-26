<?php

${basename(__FILE__, '.php')} = function () {

    if ($this->paramsExists(['roleId'])) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $role = new Role();

        //$data = json_decode(file_get_contents("php://input"), true);

        $roleId = $this->_request['roleId'];
        $deletedId = $role->deleteRole($roleId);

        if ($deletedId) {
            $this->response($this->json(['message' => 'Role Deleted successfully', 'Deleted Id' => $deletedId]), 200);
        } else {
            $this->response($this->json(['message' => 'Role Deletion failed']), 400);
        }
    } else {
        $this->response($this->json(['message' => 'bad request']), 400);
    }
};
