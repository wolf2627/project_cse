<?php

$role = new Role();
$roles = $role->getRoles();

$permission = new Permission();
$permissions = $permission->getPermissions();

?>

<div class="container mt-5">
    <h2 class="text-center">Role Permission Management</h2>

    <form id="rolePermissionForm" method="POST">
        <div class="mb-3">
            <label for="permission-role" class="form-label">Select Role:</label>
            <select class="form-select" id="permission-role" name="role" required>
                <option value="">Select Role</option>
                <?php foreach ($roles as $role) : ?>
                    <option value="<?= $role->_id ?>" data-name="<?= $role['role_name'] ?>"><?= $role['role_name'] ?></option>
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