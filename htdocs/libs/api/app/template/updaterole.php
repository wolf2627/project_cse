<?php

//TODO: Implement the update role functionality
${basename(__FILE__, '.php')} = function () {

    if (!Session::isAuthenticated()) {
        $this->response($this->json(['message' => 'Unauthorized']), 401);
    }
    if ($_GET['operation'] == 'update') {
?>
        <h4>Update Role (not Impletement)</h4>
        <form id="update-form">
            <div class="mb-3">
                <label for="roleId" class="form-label">Role ID:</label>
                <input type="text" id="roleId" class="form-control" placeholder="Enter role ID" required>
            </div>
            <div class="mb-3">
                <label for="roleName" class="form-label">Role Name:</label>
                <input type="text" id="roleName" class="form-control" placeholder="Enter new role name">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea id="description" class="form-control" placeholder="Enter new description"></textarea>
            </div>
            <button type="button" class="btn btn-primary" onclick="submitForm('update')">Submit</button>
        </form>
<? } else {
        $this->response($this->json(['message' => 'bad request']), 400);
    }
};
?>