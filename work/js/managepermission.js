$(document).ready(function () {
    let permissions = []; // Local cache for permissions
    let editingIndex = null;

    let $apiUrl = '/api/app/permission';

    const $form = $('#permissionForm');
    const $permissionsTable = $('#permissionsTable');


    let $editBtnClicked = false;
    let $EditingPermissionId = null;

    $($form).on('submit', function (e) {
        e.preventDefault(); // Prevent the form from submitting normally

        let $permission_name = document.getElementById('permission_name').value;
        let $description = document.getElementById('description').value;
        let $permission_category = document.getElementById('permission_category').value;

        const formData = {
            permission_name: $permission_name,
            description: $description,
            permission_category: $permission_category
        };

        if ($editBtnClicked) {
            // Update existing permission
            formData.permission_id = $EditingPermissionId || null;

            var d = new Dialog('Update Permission',
                '<div style="font-size: 16px; line-height: 1.6;">' +
                '<strong>Are you sure you want to update the permission as below?</strong><br>' +
                '<br><strong>Permission Name:</strong> ' + $permission_name + '<br>' +
                '<strong>Description:</strong> ' + $description + '<br>' +
                '<strong>Permission Category:</strong> ' + $permission_category + '<br>' +
                '</div>');

            d.setButtons([
                {
                    "name": "Update",
                    "class": "btn-primary",
                    "onClick": function (event) {

                        $.ajax({
                            url: $apiUrl + '/update', // Replace with your API URL
                            method: 'POST',
                            data: formData,
                            success: function (response) {
                                var SuccessToast = new Toast('now', 'success', response.message);
                                SuccessToast.show();

                                const data = {
                                    permission_name: $permission_name,
                                    description: $description,
                                    permission_category: $permission_category,
                                    permission_id: response.permission_id,
                                };

                                console.log(data);

                                updateTable(data);

                                resetForm();

                                //Change the button text to 'Create'
                                $('#permissionForm button[type="submit"]').text('Create');

                                $editBtnClicked = false;
                                $EditingPermissionId = null;

                                // Remove the cancel button
                                $('#cancelPermission').remove();
                            },
                            error: function (xhr) {
                                let errorMessage = 'An error occurred';

                                if (xhr.status === 409) {
                                    errorMessage = 'No changes Made.';
                                } else if (xhr.status === 500) {
                                    errorMessage = 'Please try again later.';
                                } else {
                                    errorMessage = xhr.responseJSON?.message || 'Unknown error occurred.';
                                }

                                var ErrorToast = new Toast('now', 'error', errorMessage);
                                ErrorToast.show();
                                console.error('Error creating permission:', xhr);

                                resetForm(); // Reset the form

                                //Change the button text to 'Create'
                                $('#permissionForm button[type="submit"]').text('Create');

                                $editBtnClicked = false;
                                $EditingPermissionId = null;

                                // Remove the cancel button
                                $('#cancelPermission').remove();

                            },
                        });
                        $(event.data.modal).modal('hide');
                    }
                },
                {
                    "name": "Cancel",
                    "class": "btn-secondary",
                    "onClick": function (event) {

                        resetForm();

                        //Change the button text to 'Create'
                        $('#permissionForm button[type="submit"]').text('Create');

                        $editBtnClicked = false;
                        $EditingPermissionId = null;

                        // Remove the cancel button
                        $('#cancelPermission').remove();

                        $(event.data.modal).modal('hide');
                    }
                }
            ]);

            d.show();
        } else {
            // Create a new permission

            var d = new Dialog('Create Permission',
                '<div style="font-size: 16px; line-height: 1.6;">' +
                '<strong>Are you sure you want to create the permission below?</strong><br>' +
                '<br><strong>Permission Name:</strong> ' + $permission_name + '<br>' +
                '<strong>Description:</strong> ' + $description + '<br>' +
                '<strong>Permission Category:</strong> ' + $permission_category + '<br>' +
                '</div>');

            d.setButtons([
                {
                    "name": "Create",
                    "class": "btn-primary",
                    "onClick": function (event) {
                        $.ajax({
                            url: $apiUrl + '/create', // Replace with your API URL
                            method: 'POST',
                            data: formData,
                            success: function (response) {
                                var SuccessToast = new Toast('now', 'success', response.message);
                                SuccessToast.show();

                                const data = {
                                    permission_name: $permission_name,
                                    description: $description,
                                    permission_category: $permission_category,
                                    permission_id: response.permissionId,
                                };
                                updateTable(data);
                            },
                            error: function (xhr) {
                                let errorMessage = 'An error occurred';

                                if (xhr.status === 409) {
                                    errorMessage = 'The permission already exists.';
                                } else if (xhr.status === 500) {
                                    errorMessage = 'Please try again later.';
                                } else {
                                    errorMessage = xhr.responseJSON?.message || 'Unknown error occurred.';
                                }

                                var ErrorToast = new Toast('now', 'error', errorMessage);
                                ErrorToast.show();
                                console.error('Error creating permission:', xhr);
                            },
                        });

                        resetForm();
                        $(event.data.modal).modal('hide');
                    }
                },
                {
                    "name": "Cancel",
                    "class": "btn-secondary",
                    "onClick": function (event) {

                        resetForm();

                        $(event.data.modal).modal('hide');
                    }
                }
            ]);

            d.show();


        }
    });


    function updateTable(permission) {

        // Check if the permission already exists
        $permissionsTable.find(`tr[data-id="${permission.permission_id}"]`).remove();

        const row = `
                <tr data-id="${permission.permission_id}">
                    <td>${permission.permission_name}</td>
                    <td>${permission.description}</td>
                    <td>${permission.permission_category}</td>
                    <td>
                        <button class="btn btn-warning btn-sm editPermission" data-id="${permission.permission_id}" data-name="${permission.permission_name}">Edit</button>
                        <button class="btn btn-danger btn-sm deletePermission" data-id="${permission.permission_id}" data-name="${permission.permission_name}">Delete</button> 
                    </td>
                </tr>
            `;
        $permissionsTable.prepend(row);
    }


    $(document).on('click', '.editPermission', function () {
        const permissionId = $(this).data('id'); // Retrieve permission ID from the button
        const permissionName = $(this).data('name'); // Retrieve permission name from the button
        editPermission(permissionId, permissionName);
    });

    function editPermission(permissionId) {
        fetchPermissionById(permissionId, function (permission) {
            console.log("permission_name: " + permission.permission_name);
            console.log("description: " + permission.description);

            // Prefill the form with the fetched data
            $('#permission_name').val(permission.permission_name);
            $('#description').val(permission.description);
            $('#permission_category').val(permission.permission_category);

            $editBtnClicked = true;
            $EditingPermissionId = permissionId;

            //Change the button text to 'Update'
            $('#permissionForm button[type="submit"]').text('Update');

            if (!$('#cancelPermission').length) {
                $('#permissionForm').append('<button type="button" class="btn btn-secondary" id="cancelPermission" style="display:inline-block">Cancel</button>');
            }


            // Scroll to the top of the form
            $('html, body').animate({
                scrollTop: $form.offset().top
            }, 500);

        });
    }

    $(document).on('click', '#cancelPermission', function () {
        console.log('Cancel button clicked');
        // Add your cancel logic here

        resetForm(); // Reset the form

        //Change the button text to 'Create'
        $('#permissionForm button[type="submit"]').text('Create');

        $editBtnClicked = false;
        $EditingPermissionId = null;

        // Remove the cancel button
        $('#cancelPermission').remove();

    });


    function fetchPermissionById(id, callback) {
        let data = { permission_id: id };

        $.ajax({
            url: '/api/app/permission/get/by/id',
            method: 'POST',
            data: data,
            success: function (response) {
                console.log(response);
                callback({
                    permission_name: response.permission_name,
                    description: response.description,
                    permission_category: response.permission_category,
                });
            },
            error: function () {
                var ErrorToast = new Toast('now', 'error', 'An error occurred');
                ErrorToast.show();
            },
        });
    }



    $(document).on('click', '.deletePermission', function () {
        const permissionId = $(this).data('id'); // Retrieve the permission ID from the data-id attribute
        const permissionName = $(this).data('name'); // Retrieve the permission name from the data-name attribute
        deletePermission(permissionId, permissionName);
    });

    function deletePermission(permissionId, permissionName) {
        var d = new Dialog('Delete Permission', 'Are you sure you want to delete the permission ' + permissionName + '?');

        d.setButtons([
            {
                "name": "Delete",
                "class": "btn-danger",
                "onClick": function (event) {

                    // Send a DELETE request to the server if permissions are stored on the backend
                    $.ajax({
                        url: $apiUrl + '/delete',
                        method: 'POST',
                        data: { permission_id: permissionId },  // permission_id
                        success: function () {
                            var SuccessToast = new Toast('now', 'success', 'Permission deleted successfully');
                            SuccessToast.show();

                        },
                        error: function (xhr, status, error) {
                            console.error('Error deleting permission');
                            var ErrorToast = new Toast('now', 'error', 'An error occurred');
                            ErrorToast.show();
                        }
                    });

                    removeTableRow(permissionId);

                    $(event.data.modal).modal('hide');
                }
            },
            {
                "name": "Cancel",
                "class": "btn-secondary",
                "onClick": function (event) {
                    $(event.data.modal).modal('hide');
                }
            }
        ]);

        d.show();
    }

    function removeTableRow(permissionId) {
        $permissionsTable.find(`tr[data-id="${permissionId}"]`).remove();
    }



    function resetForm() {
        $form[0].reset();
    }

});
