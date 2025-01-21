<?php

$permission = new Permission();

$exisingPermissions = $permission->getPermissions();

?>
<div class="container my-5">
    <h2 class="mb-4 text-center">Permission Management</h2>

    <!-- Form for creating and updating -->
    <form id="permissionForm" class="mb-4">
        <input type="hidden" id="permissionId">
        <div class="mb-3">
            <label for="permission_name" class="form-label">Permission Name</label>
            <input type="text" class="form-control" id="permission_name" placeholder="Enter permission name" required>
        </div>
        <div class="mb-3">
            <label for="permission_category" class="form-label">Permission Category:</label>
            <select id="permission_category" class="form-control" required>
                <option value="">Select Permission Category</option>
                <option value="admin">Admin</option>
                <option value="faculty">Faculty</option>
                <option value="student">Student</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <input type="text" class="form-control" id="description" placeholder="Enter description" required>
        </div>
        <button type="submit" class="btn btn-primary" id="managepermission-button">Create</button>
    </form>

    <h4 class="text-left">Existing Permissions</h4>
    <!-- Table for displaying permissions -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Permission Name</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="permissionsTable">
                <?php foreach ($exisingPermissions as $permission) : ?>
                    <tr data-id="<?=$permission->_id?>">
                        <td><?= $permission->permission_name ?></td>
                        <td><?= $permission->description ?></td>
                        <td><?= $permission->permission_category ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning editPermission" data-id="<?= $permission->_id ?>" data-name="<?=$permission->permission_name?>">Edit</button>
                            <button class="btn btn-sm btn-danger deletePermission" data-id="<?= $permission->_id ?>" data-name="<?=$permission->permission_name?>">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <!-- Dynamic rows will appear here -->
            </tbody>
        </table>
    </div>
</div>