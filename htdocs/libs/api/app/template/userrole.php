<?php

${basename(__FILE__, '.php')} = function () {

    if($this->paramsExists(['category', ''])) {
        $category = $this->_request['category'];
    } else {
        $category = null;
    }

    if (!Session::isAuthenticated()) {
        $this->response($this->json(['message' => 'Unauthorized']), 401);
    }

    $role = new Role();
    $allRoles = $role->getRoles();
    //$userRoles = $role->getUserRoles();


};