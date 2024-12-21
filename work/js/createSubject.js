// Event listener for 'Create User' button click
$('#create-subjects').on('click', function () {
    // Create a new FormData object to hold form data
    var formData = new FormData();

    // Get the uploaded file
    var fileInput = $('#subjectFile')[0];
    var file = fileInput.files[0];

    // Validate form data

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


    // Prepare the dialog for confirmation
    var d = new Dialog("Creating Subjects", "Are you sure you want to create new subjects? ");
    d.setButtons([
        {
            "name": "Create Subjects",
            "class": "btn-primary",
            "onClick": function (event) {
                // Append the form data to the FormData object after confirmation
                formData.append('subjects_file', file);

                // Optionally, display a toast or alert to confirm form submission
                var t = new Toast('New', 'now', 'Creating subjects');
                t.show();

                // Make the API call to create the user (sending the form data)
                $.ajax({
                    url: '/api/app/create/subjects',  // Your API endpoint for creating the user
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        console.log('Subjects created successfully:', response);

                        // Do something after the user is created, like refreshing the user list or showing a success message
                        t = new Toast('Success', 'now', response.successCount + ' Subjects created successfully');
                        t.show();
                        // alert("User created successfully!");
                    },
                    error: function (error) {
                        console.log('Error creating subjects:', error);
                        alert("An error occurred while creating subjects. Please try again.");
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
                console.log('Subject creation cancelled.');
                $(event.data.modal).modal('hide');
            }
        }
    ]);

    // Show the confirmation dialog
    d.show();
});
