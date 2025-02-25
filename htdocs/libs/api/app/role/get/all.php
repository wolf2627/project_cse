<?php

${basename(__FILE__, '.php')} = function () {
    // Check if the user is authenticated and Returns all roles in the database

    if (!Session::isAuthenticated()) {
        $this->response($this->json(['message' => 'Unauthorized']), 401);
    }
    
    $roles = new Role();
    $result = $roles->getRoles();

    if ($result) {
        $this->response($this->json($result), 200);
    } else {
        $this->response($this->json(['message' => 'No roles found']), 404);
    }
};