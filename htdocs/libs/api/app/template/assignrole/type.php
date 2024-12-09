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
                <select id="AssignRole-UserType" class="form-control" required onchange="loadAssignRoleForms2()">
                    <option value="">Select User Type</option>
                    <option value="faculty">Faculty</option>
                    <option value="student">Student</option>
                </select>
            </div>

            <div id="assign-roles-other-fields-dynamic-form">
                <!-- The form fields will load here dynamically -->
            </div>
        </form>
<? } else {
        $this->response($this->json(['message' => 'bad request']), 400);
    }
};
?>