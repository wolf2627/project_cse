$(document).ready(function () {
    console.log('Role Permission Manage JS Loaded [updated]');

    let permissionsData = [];
    let permissionsOfRole = [];

    // Fetch all permissions via AJAX
    async function loadPermissions(query = '') {
        console.log('Loading Permissions');
        $('#permissions-container').empty(); // Clear existing UI
        permissionsData = []; // Reset data

        return $.ajax({
            url: '/api/app/permission/get/all',
            method: 'POST',
            data: { search: query },
            success: function (response) {
                permissionsData = response.map(permission => ({
                    id: permission._id.$oid,
                    name: permission.permission_name,
                    category: permission.permission_category,
                    description: permission.description
                }));
                console.log('Permissions Data Loaded:', permissionsData);
            },
            error: function (error) {
                console.error("Error fetching permissions:", error);
            }
        });
    }

    // Fetch role-specific permissions via AJAX
    async function loadRolePermissions(role) {
        console.log('Loading Role Permissions');
        permissionsOfRole = []; // Reset data

        return $.ajax({
            url: '/api/app/permission/get/by/role',
            method: 'POST',
            data: { roleId: role },
            success: function (response) {
                if (response.success && Array.isArray(response.permission)) {
                    permissionsOfRole = response.permission.map(permission => ({
                        id: permission.id,
                        name: permission.name,
                        category: permission.category,
                        description: permission.description
                    }));
                    console.log('Role Permissions Loaded:', permissionsOfRole);
                } else {
                    console.error('Invalid response structure:', response);
                }
            },
            error: function (error) {
                console.error("Error fetching role permissions:", error);
            }
        });
    }

    // Render permissions grouped by category
    function generatePermissions() {
        console.log('Generating Permissions UI');
        const selectedPermissions = permissionsOfRole.map(permission => permission.id);
        const groupedPermissions = permissionsData.reduce((acc, permission) => {
            acc[permission.category] = acc[permission.category] || [];
            acc[permission.category].push(permission);
            return acc;
        }, {});

        const container = $('#permissions-container');
        container.empty(); // Clear existing UI

        for (const category in groupedPermissions) {
            const categoryPermissions = groupedPermissions[category];
            const categoryHeader = `
                <div class="mb-3">
                    <ul class="list-unstyled">
                        <li>
                            <button type="button" class="btn w-100 text-start" data-bs-toggle="collapse" data-bs-target="#category-${category}">
                                <i class="fas fa-chevron-right" id="arrow-${category}"></i> ${category}
                            </button>
                            <div id="category-${category}" class="collapse mb-3 show">
                                <ul class="list-unstyled ms-4">
                                    ${categoryPermissions.map(permission => {
                const isChecked = selectedPermissions.includes(permission.id) ? 'checked' : '';
                return `
                                            <li>
                                                <div class="form-check">
                                                    <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" 
                                                        value="${permission.id}" id="perm-${permission.id}" ${isChecked}>
                                                    <label class="form-check-label" for="perm-${permission.id}" data-bs-toggle="tooltip" 
                                                        data-bs-placement="right" title="${permission.description}">
                                                        ${permission.name}
                                                    </label>
                                                </div>
                                            </li>
                                        `;
            }).join('')}
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>`;
            container.append(categoryHeader);
        }

        $('[data-bs-toggle="tooltip"]').tooltip(); // Reinitialize tooltips
    }

    // Handle role changes
    $('#permission-role').on('change', function () {
        const role = $(this).val();
        console.log('Role Changed to:', role);

        resetPermissionsForm(1);

        async function loadRoleAndPermissions() {
            try {
                console.log('Loading role and permissions');
                await Promise.all([loadRolePermissions(role), loadPermissions()]);
                console.log('Permissions and Role Data Loaded');
                generatePermissions();
            } catch (error) {
                console.error('Error loading role and permissions:', error);
                loadPermissions().then(() =>
                    generatePermissions()
                );
            }
        }

        loadRoleAndPermissions();
    });

    // Search functionality
    $('#role-permission-search').on('input', function () {
        const query = $(this).val().toLowerCase();
        $('#permissions-container .mb-3').each(function () {
            const category = $(this);
            const permissions = category.find('.form-check');
            let hasVisiblePermissions = false;

            permissions.each(function () {
                const label = $(this).find('label').text().toLowerCase();
                const isVisible = label.includes(query);
                $(this).toggle(isVisible);
                if (isVisible) hasVisiblePermissions = true;
            });

            if (hasVisiblePermissions) {
                category.show();
                category.find('.collapse').collapse('show');
            } else {
                category.hide();
            }
        });
    });

    // Form submission
    $('#rolePermissionForm').on('submit', function (event) {
        event.preventDefault();
        const role = $('#permission-role').val();
        const selectedPermissions = $("input[name='permissions[]']:checked").map(function () {
            return $(this).val();
        }).get();

        const formData = new FormData();
        formData.append('roleId', role);
        selectedPermissions.forEach(permission => {
            formData.append('permissionsID[]', permission);
        });

        $.ajax({
            url: '/api/app/permission/grant',
            method: 'POST',
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
                console.log('Permissions updated successfully:', response);
                var SuccessToast = new Toast('now', ' success', 'Permissions updated successfully');

                if (response.message.length > 0) {
                    var permissionNames = response.message.map(function (permission) {
                        return permission.name;
                    });

                    // Create a dialog with the permission names
                    var successDialog = new Dialog(
                        "Success Details",
                        `<h6>Current Permissions for Role: ${$('#permission-role').find('option:selected').data('name')} </h6>
                                         <ul>
                                             <li>${permissionNames.join('</li><li>')}</li>
                                         </ul>`
                    );

                    successDialog.setButtons([
                        {
                            "name": "Close",
                            "class": "btn-primary",
                            "onClick": function (event) {
                                $(event.data.modal).modal('hide');
                            }
                        }
                    ]);
                    successDialog.show();
                }

                resetPermissionsForm();
                SuccessToast.show();
            },
            error: function (error) {
                console.error('Error saving permissions:', error);
                var ErrorToast = new Toast('now', ' error', 'Error saving permissions');
                ErrorToast.show();
            }
        });
    });

    // Reset form and UI
    function resetPermissionsForm(permissionContainer = 0) {
        if (permissionContainer == 1) {
            $('#permissions-container').empty();
            permissionsData = [];
            permissionsOfRole = [];
        } else {
            $('#rolePermissionForm').trigger('reset');
            $('#permissions-container').empty();
            permissionsData = [];
            permissionsOfRole = [];
        }
    }
});
