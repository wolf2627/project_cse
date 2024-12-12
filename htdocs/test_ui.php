<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role-Permission Mapping</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <h2 class="mb-4">Role-Permission Mapping</h2>

        <form id="rolePermissionForm">
            <div class="mb-3">
                <label for="role" class="form-label">Select Role:</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="role1">Principal</option>
                    <option value="role2">Administrator</option>
                    <option value="role3">Instructor</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Search Permissions:</label>
                <input type="text" id="search" class="form-control" placeholder="Search permissions">
            </div>

            <div id="permissions-container">
                <!-- Permissions will be dynamically loaded here -->
            </div>

            <button type="submit" class="btn btn-primary">Save Mapping</button>
        </form>
    </div>

    <!-- Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


    <script>
        $(document).ready(function() {
            let permissionsData = [];

            // Fetch all permissions via AJAX
            $.ajax({
                url: '/api/app/permission/all',
                method: 'POST',
                success: function(response) {
                    permissionsData = response.map(permission => ({
                        id: permission._id.$oid,
                        name: permission.permission_name,
                        category: permission.permission_category,
                        description: permission.description
                    }));

                    console.log("Permissions Data:", permissionsData);

                    // Group and render permissions once data is loaded
                    generatePermissions();
                },
                error: function(error) {
                    console.error("Error fetching permissions:", error);
                }
            });


            // Function to group permissions by category and render them
            function generatePermissions() {
                // Group permissions by category
                const groupedPermissions = permissionsData.reduce((acc, permission) => {
                    if (!acc[permission.category]) acc[permission.category] = [];
                    acc[permission.category].push(permission);
                    return acc;
                }, {});

                const container = $('#permissions-container');
                container.empty(); // Clear the container

                // Loop through grouped permissions
                for (const category in groupedPermissions) {
                    const categoryGroup = groupedPermissions[category];

                    // Create category header with nested permissions
                    const categoryHeader = `
            <div class="mb-3">
                <ul class="list-unstyled">
                    <li>
                        <button type="button" class="btn w-100 text-start" data-bs-toggle="collapse" data-bs-target="#category-${category}">
                            <i class="fas fa-chevron-right" id="arrow-${category}"></i> ${category}
                        </button>

                        
                        <div id="category-${category}" class="collapse mb-3">
                            <ul class="list-unstyled ms-4">
                                ${categoryGroup.map(permission => `
                                    <li>
                                        <div class="form-check">
                                            <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" 
                                                   value="${permission.id}" id="perm-${permission.id}">
                                            <label class="form-check-label" for="perm-${permission.id}" data-bs-toggle="tooltip" 
                                                   data-bs-placement="top" title="${permission.description}">
                                                ${permission.name}
                                            </label>
                                        </div>
                                    </li>
                                `).join('')}
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        `;

                    container.append(categoryHeader);
                }

                // Reinitialize tooltips for dynamically added elements
                $('[data-bs-toggle="tooltip"]').tooltip();

                // Handle select all/deselect all button functionality
                container.on('click', '.select-all', function() {
                    const category = $(this).data('category');
                    const target = $(`#category-${category}`);
                    target.collapse('show'); // Expand the category
                    target.find('.permission-checkbox').prop('checked', true);
                });

                container.on('click', '.deselect-all', function() {
                    const category = $(this).data('category');
                    const target = $(`#category-${category}`);
                    target.find('.permission-checkbox').prop('checked', false);
                });

                // Handle arrow icon direction on collapse
                container.on('show.bs.collapse', function(event) {
                    const category = event.target.id.replace('category-', '');
                    $(`#arrow-${category}`).removeClass('fa-chevron-right').addClass('fa-chevron-down');
                });

                container.on('hide.bs.collapse', function(event) {
                    const category = event.target.id.replace('category-', '');
                    $(`#arrow-${category}`).removeClass('fa-chevron-down').addClass('fa-chevron-right');
                });
            }

            // Search functionality
            $('#search').on('input', function() {
                const query = $(this).val().toLowerCase();
                $('#permissions-container .form-check').each(function() {
                    const label = $(this).find('label').text().toLowerCase();
                    $(this).toggle(label.includes(query));
                });
            });

            // Submit form handler
            $('#rolePermissionForm').on('submit', function(event) {
                event.preventDefault();

                const role = $('#role').val();
                const selectedPermissions = $("input[name='permissions']:checked").map(function() {
                    return $(this).val();
                }).get();

                const mapping = {
                    role_id: role,
                    permissions: selectedPermissions
                };

                console.log("Role-Permission Mapping:", mapping);

                $.ajax({
                    url: '/save-role-permission-mapping',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(mapping),
                    success: function(response) {
                        alert("Mapping saved successfully!");
                    },
                    error: function(error) {
                        console.error("Error saving mapping:", error);
                        alert("Error saving mapping.");
                    }
                });
            });
        });
    </script>

</body>

</html>