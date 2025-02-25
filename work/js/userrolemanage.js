$(document).ready(function () {
    console.log('User Role Manage JS Loaded');

    $('#user-role-category').on('change', function () {
        console.log('Category Changed:', $(this).val());
        resetUserRoleForm();
    });

    // Handle user selection
    $('#role-fetch-user').on('click', function () {
        console.log("User Role Fetch Button Clicked");

        $('#user-info').html('');
        $('#roles-container').html('');

        //removeSubmitButton();

        const userId = $('#role-user-id').val();
        const category = $('#user-role-category').val();

        if (!category) {
            console.error('Please select a category first.');
            return;
        }

        if (!userId) {
            console.error('User ID/Registration No cannot be empty.');
            return;
        }

        let formData = new FormData();
        let apiLink = '';

        formData.append('category', category);

        if (category === 'faculty') {
            formData.append('facultyId', userId);
            apiLink = '/api/app/get/faculty';
        } else if (category === 'student') {
            formData.append('regNo', userId);
            apiLink = '/api/app/get/student';
        } else {
            console.error('Invalid category selected.');
            return;
        }

        // Fetch User Details
        fetch(apiLink, {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        console.error('Error fetching user details:', errorData.message || 'Unknown error occurred');
                        showToast("Error", errorData.message || "Error fetching user details");
                        throw new Error(`HTTP Error ${response.status}: ${response.statusText}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data._id) {
                    const userDetails = data;

                    showToast("User Details", "User Fetched Successfully");

                    const userInfoHtml = `
                                <div class="col-md-12">
                                    <p><strong>Name:</strong> <em>${userDetails.name}</em></p>
                                    <p><strong>Email:</strong> <em>${userDetails.email}</em></p>
                                    <p><strong>Department:</strong> <em>${userDetails.department || 'N/A'}</em></p>
                                    <p><strong>Designation:</strong> <em>${userDetails.designation || 'N/A'}</em></p>
                                    <p><strong>Role:</strong> <em>${userDetails.role || 'N/A'}</em></p>
                                </div>
                    `;

                    const displayHtml = `
                <strong>Name: </strong> ${userDetails.name}</em>, 
                <strong>Department: </strong> ${userDetails.department || 'N/A'}</em>, 
                <strong>Role: </strong> ${userDetails.role || 'N/A'}</em>
                `;

                    var userDialog = new Dialog("User Details", userInfoHtml);
                    userDialog.setButtons([
                        {
                            name: "Confirm",
                            class: "btn-success",
                            onClick: function (event) {

                                $('#user-info').html(displayHtml);

                                loadRoles(category, userId);

                                enableSubmitButton();

                                $(event.data.modal).modal('hide');
                            }

                        },
                        {
                            name: "Cancel",
                            class: "btn-secondary",
                            onClick: function (event) {
                                resetUserRoleForm();
                                $(event.data.modal).modal('hide');
                            }
                        }
                    ]);

                    userDialog.show();
                } else {
                    throw new Error("Unexpected response structure");
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
            });
    });

    function loadRoles(category, userId) {
        console.log('Loading Roles');

        let formData = new FormData();
        formData.append('category', category);
        formData.append('user_id', userId);

        $.ajax({
            url: '/api/app/template/userrole',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (html) {
                console.log('Roles fetched successfully');
                showToast("Roles", "Roles Fetched Successfully");

                $('#roles-container').html(html);

                $('[data-bs-toggle="tooltip"]').tooltip(); // Enable tooltips

                console.log('Roles Loaded');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Error fetching roles:', errorThrown);
                showToast("Error", "Failed to fetch roles.");
            }
        });
    }

    $('#user-role-submit').on('click', function (event) {
        // Prevent form submission
        // event.preventDefault();

        console.log("User Role Submit Button Clicked");

        const category = $('#user-role-category').val();
        const userId = $('#role-user-id').val();

        // Validate category
        if (!category) {
            console.error('Please select a category first.');
            return;
        }

        // Validate user ID
        if (!userId) {
            console.error('User ID/Registration No cannot be empty.');
            return;
        }

        // Collect selected roles
        var selectedRoles = $("input[name='role[]']:checked").map(function () {
            return $(this).val();
        }).get();

        // if no roles selected, log an error and set an empty array
        if (selectedRoles.length === 0) {
            console.error('No roles selected.');
            selectedRoles = [];
        }

        console.log('Selected Category:', category);
        console.log('Selected User ID:', userId);
        console.log('Selected Roles:', selectedRoles);

        // Submit role assignment (implement AJAX or other submission logic here)
        const formData = new FormData();
        formData.append('category', category);
        formData.append('user_id', userId);

        selectedRoles.forEach(role => {
            formData.append('roles_id[]', role);
        });

        console.log('Submitting form data:', formData);

        $.ajax({
            url: '/api/app/role/set', // Replace with your API endpoint
            method: 'POST',
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {
                // Handle empty roles case
                if (!response || !response.message || response.message.length === 0) {
                    showToast('Success', 'No roles assigned');
                    return;
                }

                console.log('Roles assigned successfully:', response);
                showToast("Success", "Roles assigned successfully!");

                // Ensure response.message is an array
                if (Array.isArray(response.result) && response.result.length > 0) {
                    var roleNames = response.result.map(function (role) {
                        return role.name;
                    });

                    // Generate dialog with assigned roles
                    var successDialog = new Dialog(
                        "Success",
                        `<h6>Current Roles for User: ${$('#role-user-id').val()} </h6>
                        <ul>
                            ${roleNames.map(role => `<li>${role}</li>`).join('')}
                        </ul>`
                    );

                    // Set dialog buttons
                    successDialog.setButtons([
                        {
                            name: "Close",
                            class: "btn-info",
                            onClick: function (event) {
                                $(event.data.modal).modal('hide');
                            }
                        }
                    ]);

                    successDialog.show();

                    // refresh the roles
                    loadRoles(category, userId);


                } else {
                    // No roles to display
                    var noRolesDialog = new Dialog(
                        "Success",
                        `<h6>Current Roles for User: ${$('#role-user-id').val()} </h6>
                        <p>No Roles Assigned</p>`
                    );

                    noRolesDialog.setButtons([
                        {
                            name: "Close",
                            class: "btn-secondary",
                            onClick: function (event) {
                                $(event.data.modal).modal('hide');

                            }
                        }
                    ]);

                    noRolesDialog.show();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Error assigning roles:', errorThrown);
                showToast("Error", "Failed to assign roles. Please try again.");
            }
        });
    });

    function enableSubmitButton() {
        console.log('Enabling Submit Button');
        //change d-none class to d-block
        $('#user-role-submit').removeClass('d-none');
        $('#user-role-submit').addClass('d-block');
    }

    function removeSubmitButton() {
        console.log('Removing Submit Button');
        $('#user-role-submit').removeClass('d-block');
        $('#user-role-submit').addClass('d-none');
    }

    // Reusable Toast Function
    function showToast(title, message) {
        const toast = new Toast("now", title, message);
        toast.show();
    }

    function resetUserRoleForm() {
        $('#role-user-id').val('');
        $('#user-info').html('');
        $('#roles-container').html('');
        removeSubmitButton();
    }

});
