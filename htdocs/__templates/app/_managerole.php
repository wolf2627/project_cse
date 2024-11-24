<h2>Manage Role</h2>

<div class="container mt-5">
    <h2 class="text-center">Role Management</h2>

    <!-- Select Operation -->
    <div class="mb-4">
        <label for="roles-operation" class="form-label">Select Operation:</label>
        <select id="roles-operation" class="form-select" onchange="loadRolesForm()">
            <option value="">-- Select an Operation --</option>
            <option value="create">Create Role</option>
            <option value="update">Update Role</option>
            <option value="delete">Delete Role</option>
        </select>
    </div>

    <!-- Dynamic Form -->
    <div id="roles-dynamic-form">
        <!-- The form fields will load here dynamically -->
    </div>

</div>