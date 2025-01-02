$(document).ready(function () {
    console.log('User Role Manage JS Loaded new');

    // Handle user selection
    $('#role-fetch-user').on('click', function () {
        console.log("User Role Fetch Button Clicked");

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
                if (!response.ok) { // Check HTTP status code
                    // Handle HTTP errors
                    return response.json().then(errorData => {
                        // Clear previous user info (if any)
                        $('#user-info').html('');

                        console.error('Error fetching user details:', errorData.message || 'Unknown error occurred');

                        // Display error toast
                        var errorToast = new Toast("now", "Error", errorData.message || "Error fetching user details",);
                        errorToast.show();

                        // Explicitly throw an error to stop further processing
                        throw new Error(`HTTP Error ${response.status}: ${response.statusText}`);
                    });
                }

                return response.json(); // Parse and return JSON for successful responses
            })
            .then(data => {
                if (data._id) { // Check for a valid response structure
                    const userDetails = data; // Save user details

                    // Display success toast
                    var successToast = new Toast("now", "User Details", "User Fetched Successfully");
                    successToast.show();

                    // Display user details in UI
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

                    // Load user roles
                    // loadUserRoles(userDetails._id.$oid);
                } else {
                    throw new Error("Unexpected response structure");
                }
            })
            .catch(error => {
                // Catch and log fetch or parsing errors
                console.error('Fetch error:', error);
            });

    });



});