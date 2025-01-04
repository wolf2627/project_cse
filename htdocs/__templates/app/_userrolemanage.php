<?php

$role = new Role();
$roles = $role->getRoles();

?>

<div class="container mt-5">
    <h2 class="text-center">Role Management</h2>

    <form id="userrolesform" method="POST">

        <!-- Category of User faculty, student, Admin for retrival of user data -->

        <div class="mb-3">
            <label for="user-role-category" class="form-label">Select User Category:</label>
            <select class="form-select" id="user-role-category" name="role_category" required>
                <!-- TODO: Load the User Category from Essentials -->
                <option value="">Select User Category</option>
                <option value="faculty">Faculty</option>
                <option value="student">Student</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="role-user-id" class="form-label">Enter Register No/Faculty ID:</label>
            <input type="text" id="role-user-id" class="form-control" placeholder="Register No/Faculty ID" name="role-user-id" required>
        </div>

        <!-- Button to fetch user -->
        <button type="button" class="btn mb-3 btn-info" id="role-fetch-user">Fetch User</button>

        <div id="user-info"></div>

        <div id="roles-container" class="mb-3">
            <!-- Permissions will be dynamically loaded here -->
        </div>

        <div id="userrole-submit-btn" class="mb-3">
        </div>

    </form>
</div>