
$(document).ready(function () {
    // Initialize Select2 on the subjects dropdown
    console.log('enrollmennt js loaded');
    $('#enroll-subjects').select2({
        placeholder: "Select subjects",
        allowClear: true,
        width: '100%',  // Set the width to 100% for full container width
    });
});


$('#fetch-students-btn').on('click', function (event) {
    event.preventDefault(); // Prevent default form submission
    console.log('fetch-students-btn clicked');

    // Get all form data
    var formData = new FormData($('#subject-selection-form')[0]);

    if (!formData.getAll('subjects[]').length) {
        var t = new Toast('Error', 'now', 'Please select subjects');
        t.show();
        return;
    }
    
    var d = new Dialog("Fetching Students", "Are you sure you want to fetch students for the selected subjects?");
    d.setButtons([
        {
            "name": "Fetch Students",
            "class": "btn-primary",
            "onClick": function (event) {
                var t = new Toast('Fetching', 'now', 'Fetching students');
                t.show();

                $.ajax({
                    url: '/api/app/template/enrollsubstud',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        console.log('Students fetched successfully:', response);

                        var successtoast = new Toast('Success', 'now', 'Students fetched successfully');
                        successtoast.show();

                        // Clear the previous content before appending new content
                        $('#subjects-list-container').empty();  // Empty the div

                        // Check if the response is a string or HTML content
                        if (typeof response === 'string') {
                            // If response is a string or HTML, directly append
                            $('#subjects-list-container').append(response);
                        } else if (response && response.html) {
                            // If response contains HTML as a property
                            $('#subjects-list-container').append(response.html);
                        } else {
                            console.error('Unexpected response format:', response);
                        }

                        // Show the #subjects-list-container div if it was hidden
                        $('#subjects-list-container').show();
                    },
                    error: function (error) {
                        errortoast = new Toast('Failed', 'now', 'Students not found');
                        errortoast.show();
                        console.error('Error fetching students:', error);
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
                var t = new Toast('Cancelled', 'now', 'Fetching students cancelled');
                t.show();
                // Explicitly reference the modal to hide it
                $(event.target).closest('.modal').modal('hide');
            }
        }
    ]);

    d.show();
});
