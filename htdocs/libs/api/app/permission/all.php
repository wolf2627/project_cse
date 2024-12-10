<?php

${basename(__FILE__, '.php')} = function () {
    // Check if the user is authenticated and Returns all permissions in the database

    if (!Session::isAuthenticated()) {
        $this->response($this->json(['message' => 'Unauthorized']), 401);
    }

    $permission = new Permission();
    $result = $permission->getPermissions();

    $this->response($this->json($result), 200);

};