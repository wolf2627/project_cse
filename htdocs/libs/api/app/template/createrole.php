<?php

${basename(__FILE__, '.php')} = function () {

    if (!Session::isAuthenticated()) {
        $this->response($this->json(['message' => 'Unauthorized']), 401);
    }
    if ($_GET['operation'] == 'create') {
?>
        <h4>Create Role</h4>
        <form id="create-form">
            <div class="mb-3">
                <label for="roleName" class="form-label">Role Name:</label>
                <input type="text" id="roleName" class="form-control" placeholder="Enter role name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea id="description" class="form-control" placeholder="Enter description" required></textarea>
            </div>
            <button type="button" class="btn btn-primary" onclick="submitForm('create')">Submit</button>
        </form>
<? } else {
        $this->response($this->json(['message' => 'bad request']), 400);
    }
};
?>