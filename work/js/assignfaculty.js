$(document).ready(function () {
    // Initialize Select2 on the subjects dropdown
    console.log('Assign Faculty js loaded');
    $('#studentSections').select2({
        placeholder: "Student Sections",
        allowClear: true,
        width: '100%'  // Set the width to 100% for full container width
    });
});


$(document).ready(function () {
    $('#classForm').on('submit', function (e) {
        e.preventDefault(); // Prevent the form from submitting normally

        // Gather all form values
        const formData = {
            faculty_id: $('#facultyId').val(),
            subject_code: $('#subjectCode').val(),
            batch: $('#batch').val(),
            department: $('#department').val(),
            semester: $('#semester').val(),
            section: $('#section').val(),
            student_sections: $('#studentSections').val(), // Array for multiple select
            year: $('#year').val()
        };

        // Print values in the console
        console.log('Form Data:', formData);


        var d = new Dialog("Assigning Faculty", "Are you sure you want to assign faculty to the selected sections? " + { formData });
        d.setButtons([
            {
                "name": "Assign Faculty",
                "class": "btn-success",
                "onClick": function (event) {
                    // Make the API call to assign faculty to the selected sections
                    $.ajax({
                        url: '/api/app/create/assignfaculty',  // Your API endpoint for assigning faculty
                        type: 'POST',
                        data: formData,
                        success: function (response) {
                            console.log('response', response['message']);

                            // Do something after the faculty is assigned, like refreshing the faculty list or showing a success message
                            var t = new Toast('Status', 'now', response['message']);
                            t.show();

                            // Clear the form after successful submission
                            $('#classForm').trigger('reset');
                        },
                        error: function (xhr, status, error) {
                            console.error('Error assigning faculty:', error);
                            var t = new Toast('Error', 'now', 'Error assigning faculty');
                            t.show();
                        }
                    });
                    // Hide the dialog after action is confirmed
                    $(event.data.modal).modal('hide');
                }

            },
            {
                "name": "Cancel",
                "class": "btn-danger",
                "onClick": function (event) {
                    // Hide the dialog after action is confirmed
                    $(event.data.modal).modal('hide');
                }
            }
        ]);

        // Show the confirmation dialog
        d.show();
    });
});
