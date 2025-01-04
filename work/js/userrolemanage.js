$(document).ready(function () {
    console.log('User Role Manage JS Loaded');

    // Handle user selection
    $('#role-fetch-user').on('click', function () {
        console.log("User Role Fetch Button Clicked");

        $('#user-info').html('');
        $('#roles-container').html('');

        removeSubmitButton();

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
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5>User Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Name:</strong> ${userDetails.name}</p>
                                        <p><strong>Email:</strong> ${userDetails.email}</p>
                                        <p><strong>Department:</strong> ${userDetails.department || 'N/A'}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Designation:</strong> ${userDetails.designation || 'N/A'}</p>
                                        <p><strong>Role:</strong> ${userDetails.role || 'N/A'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#user-info').html(userInfoHtml);

                    loadRoles(category, userId);
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

                appendSubmitButton();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Error fetching roles:', errorThrown);
                showToast("Error", "Failed to fetch roles.");
            }
        });
    }

    $('#user-role-submit').on('click', function () {
        console.log("User Role Submit Button Clicked");
        // Handle role submission logic here
    });

    function appendSubmitButton() {
        console.log('Appending Submit Button');
        const submitButtonHtml = `
            <button type="button" id="user-role-submit" class="btn btn-primary">Save Roles</button>
        `;
        $('#userrole-submit-btn').html(submitButtonHtml);
    }

    function removeSubmitButton() {
        console.log('Removing Submit Button');
        $('#userrole-submit-btn').html('');
    }

    function showToast(title, message) {
        var toast = new Toast("now", title, message);
        toast.show();
    }
});
