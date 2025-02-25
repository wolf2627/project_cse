<!-- <h2>Assign Role</h2> -->

<div class="container mt-5">
    <h2 class="text-center">Role Assignment</h2>

    <!-- Select Operation -->
    <div class="mb-4">
        <label for="assign-roles-operation" class="form-label">Select Operation:</label>
        <select id="assign-roles-operation" class="form-select" onclick="loadAssignRoleForms()">
            <option value="">-- Select an Operation --</option>
            <option value="assign">Assign Role</option>
            <option value="unassign">Unassign Role</option>
        </select>
    </div>

    <!-- Dynamic Form -->
    <div id="assign-roles-dynamic-form">
        <!-- The form fields will load here dynamically -->
    </div>

</div>