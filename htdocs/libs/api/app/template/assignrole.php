<?php

${basename(__FILE__, '.php')} = function () {

    if (!Session::isAuthenticated()) {
        $this->response($this->json(['message' => 'Unauthorized']), 401);
    }
    if ($_GET['operation'] == 'assign') {
?>
        <h4>Assign Role</h4>
        <form id="assign-role-form">
            <div class="mb-3">
                <label for="AssignRole-UserType" class="form-label">Select User Type:</label>
                <select id="AssignRole-UserType" class="form-control" required onchange="">
                    <option value="">Select User Type</option>
                    <option value="faculty">Faculty</option>
                    <option value="student">Student</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="ReferenceNumber" class="form-label">Reference Number</label>
                <input type="text" class="form-control" id="ReferenceNumber" placeholder="Reference Number" aria-describedby="ReferenceNumberHelp">
                <div id="ReferenceNumberHelp" class="form-text">For Faculty Reference Number is Faculty Id, and For Student Reference Number is Register Number.</div>
            </div>
            <div class="mb-3">
                <label for="roleId" class="form-label">Select Role:</label>
                <select id="roleId" class="form-control" required>
                    <option value="">Select Role</option>
                    <?php
                    $roleCategory = null;
                    $role = new Role();
                    $roles = $role->getRoles($roleCategory);
                    foreach ($roles as $role) {
                    ?>
                        <option value="<?php echo $role['_id']; ?>"><?php echo $role['role_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <button type="button" class="btn btn-primary" onclick="manageRoleSubmitForm('')">Assign</button>
        </form>
<? } else {
        $this->response($this->json(['message' => 'bad request']), 400);
    }
};
?>