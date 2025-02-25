<?php

${basename(__FILE__, '.php')} = function () {

    if (!Session::isAuthenticated()) {
        $this->response($this->json(['message' => 'Unauthorized']), 401);
    }
    if ($_GET['operation'] == 'unassign') {
?>
        <h4>Unassign Role (not implemented)</h4>
        <form id="assign-role-form">
            <div class="mb-3">
                <label for="roleId" class="form-label">Select Role:</label>
                <select id="roleId" class="form-control" required>
                    <option value="">Select Role</option>
                    <?php
                    $role = new Role();
                    $roles = $role->getRoles();
                    foreach ($roles as $role) {
                    ?>
                        <option value="<?php echo $role['_id']; ?>"><?php echo $role['role_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <button type="button" class="btn btn-danger" onclick="manageRoleSubmitForm('delete')">Delete</button>
        </form>
<? } else {
        $this->response($this->json(['message' => 'bad request']), 400);
    }
};
?>