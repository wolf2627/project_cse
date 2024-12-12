<?php

$role = new Role();
$roles = $role->getRoles();

$permission = new Permission();
$permissions = $permission->getPermissions();

?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="container mt-5">
        
    <form id="rolePermissionForm" action="\test2" method="POST">
        <div class="mb-3">
            <label for="permission-role" class="form-label">Select Role:</label>
            <select class="form-select" id="permission-role" name="role" required>
                <option value="">Select Role</option>
                <?php foreach ($roles as $role) : ?>
                    <option value="<?= $role->_id ?>"><?= $role['role_name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label" for="role-permission-search">Search Permissions:</label>
            <input type="text" id="role-permission-search" class="form-control" placeholder="Search permissions">
        </div>


        <div id="permissions-container">
            <!-- Permissions will be dynamically loaded here -->
        </div>

        <button type="submit" class="btn btn-primary">Save Permissions</button>
    </form>
</div>