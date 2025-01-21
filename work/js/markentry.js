$(document).ready(function () {
    // Use jQuery to bind the event
    $('#student-select').on('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const regNo = selectedOption.dataset.regno;
        const name = selectedOption.dataset.name;
        const marks = selectedOption.dataset.marks;

        // Populate the form fields
        $('#reg-no').val(regNo);
        $('#student-name').val(name);
        $('#current-marks').val(marks);

        // Show the update form
        $('#update-form').removeClass('d-none');
    });

    $('#reset-btn').on('click', function () {
        // Reset the form for another student
        $('#student-select').val('');
        $('#update-form').addClass('d-none');
    });

    $('#update-btn').on('click', function () {
        // Get the values from the form
        const regNo = $('#reg-no').val();
        const newMarks = $('#updated-marks').val();

        // Retrieve PHP values from data attributes of the update-form element
        const form = $('#update-form');
        const batch = form.data('batch');
        const semester = form.data('semester');
        const subjectCode = form.data('subject_code');
        const testname = form.data('testname');
        const section = form.data('section');
        const department = form.data('department');

        console.log(regNo, newMarks, batch, semester, subjectCode, testname, section, department);

        // Check if new marks are provided
        if (!newMarks.trim()) {
            alert('Please enter new marks.');
            return;
        }

        updatingToast = new Toast('Updating', 'now', 'Updating marks');
        updatingToast.show();

        // Create a new FormData object and append form data
        const formData = new FormData();
        formData.append('reg_no', regNo);
        formData.append('new_mark', newMarks);
        formData.append('batch', batch);
        formData.append('semester', semester);
        formData.append('subject_code', subjectCode);
        formData.append('testname', testname);
        formData.append('section', section);
        formData.append('department', department);

        var d = new Dialog("Update Marks", "Are you sure to update mark as " + newMarks + "?");

        d.setButtons([
            {
                "name": "Update Mark",
                "class": "btn-success",
                "onClick": function (event) {
                    // Send the data to the API using Fetch and FormData

                    $.ajax({
                        url: '/api/app/update/updatemark',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            console.log('Marks updated successfully:', response);

                            // Show a success toast
                            var successtoast = new Toast('Success', 'now', 'Marks updated successfully');
                            successtoast.show();

                            // Reset all input fields in the form
                            $('#update-form').find('input').val('');

                            // Reset the select dropdown
                            $('#student-select').val('').trigger('change'); // Reset and trigger change for the dropdown

                            // Hide the update form
                            $('#update-form').addClass('d-none');
                        },
                        error: function (error) {
                            console.error('Error updating marks:', error);
                            errortoast = new Toast('Failed', 'now', 'Marks not updated');
                            errortoast.show();
                        }
                    });

                    $(event.data.modal).modal('hide');
                }
            },
            {
                "name": "Cancel",
                "class": "btn-secondary",
                "onClick": function (event) {
                    var t = new Toast('Cancelled', 'now', 'Update Marks Cancelled');
                    t.show();

                    $(event.data.modal).modal('hide');
                }
            }
        ]);

        d.show();
    });

    $('#back-btn').on('click', function () {
        window.history.back(); // Navigate to the previous page
    });

    $('#edit-back-btn').on('click', function () {
        window.history.back(); // Navigate to the previous page
    });
});
