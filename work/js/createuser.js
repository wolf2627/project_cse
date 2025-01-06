// Event listener for 'Create User' button click
$('#create-users').on('click', function () {
    // Create a new FormData object to hold form data
    var formData = new FormData();

    // Get the selected user type (single or multiple)
    var userType = $('input[name="userType"]:checked').val();

    // Get the selected role (student or faculty)
    var role = $('input[name="role"]:checked').val();

    // Get the uploaded file
    var fileInput = $('#formFile-usercreate')[0];
    var file = fileInput.files[0];

    // Validate form data
    if (userType === "multiple") {
        if (file) {
            // Validate file extension (Excel files)
            var allowedExtensions = ['.xls', '.xlsx'];
            var fileExtension = file.name.split('.').pop().toLowerCase();

            if (allowedExtensions.indexOf('.' + fileExtension) === -1) {
                alert('Please upload a valid Excel file (.xls or .xlsx)');
                return; // Stop further execution
            }
        } else {
            alert('Please select a file to upload.');
            return; // Stop further execution
        }
    }

    // Prepare the dialog for confirmation
    var d = new Dialog("Creating User Profiles", "Are you sure you want to create new users");
    d.setButtons([
        {
            "name": "Create User",
            "class": "btn-primary",
            "onClick": function (event) {
                // Append the form data to the FormData object after confirmation
                formData.append('userType', userType);
                formData.append('role', role);
                formData.append('users_file', file);

                // Optionally, display a toast or alert to confirm form submission
                var t = new Toast('New', 'now', 'Creating profiles');
                t.show();

                // Make the API call to create the user (sending the form data)
                $.ajax({
                    url: '/api/app/create/users',  // Your API endpoint for creating the user
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        console.log('User created successfully:', response);

                        // Do something after the user is created, like refreshing the user list or showing a success message
                        t = new Toast('Success', 'now', response.successCount + ' Users created successfully');
                        t.show();
                        // alert("User created successfully!");
                    },
                    error: function (error) {
                        console.log('Error creating user:', error);
                        alert("An error occurred while creating the user. Please try again.");
                    }
                });

                // Hide the dialog after action is confirmed
                $(event.data.modal).modal('hide');
            }
        },
        {
            "name": "Cancel",
            "class": "btn-secondary",
            "onClick": function (event) {
                console.log('User creation cancelled.');
                $(event.data.modal).modal('hide');
            }
        }
    ]);

    // Show the confirmation dialog
    d.show();


    $('#formFile-usercreate').val(''); // Clear the file input after submission
});

$('#create-users-clear').on('click', function () {
    $('#formFile-usercreate').val('');
});