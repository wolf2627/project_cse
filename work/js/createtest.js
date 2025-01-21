$(document).ready(function () {
    console.log('create test js loaded [new]');
    let subjectIndex = 1;

    $('#subjectsContainer').on('click', function (event) {
        if ($(event.target).hasClass('addSubjectBtn')) {
            event.preventDefault();

            const subjectTemplate = $('#subjectTemplate').html();
            const newSubject = $('<div>').addClass('input-group mb-2');

            // Build the new subject row
            newSubject.html(`
                <select class="form-select" name="subjects[${subjectIndex}][code]" required>
                    ${subjectTemplate}
                </select>
                <input type="date" class="form-control" name="subjects[${subjectIndex}][date]" required>
                <button type="button" class="btn btn-danger removeSubjectBtn">-</button>
            `);

            $('#subjectsContainer').append(newSubject);
            subjectIndex++;
        } else if ($(event.target).hasClass('removeSubjectBtn')) {
            $(event.target).closest('.input-group').remove();
        }
    });
});

$(document).ready(function () {
    console.log('create test js loaded [form]');

    $('#createTestForm').on('submit', function (e) {
        e.preventDefault(); // Prevent the form from submitting normally

        // Gather all form values
        const formData = {
            testname: $('#testname').val(),
            month: $('#month').val(),
            batch: $('#batch').val(),
            semester: $('#semester').val(),
            year: $('#year').val(),
            department: $('#department').val(),
            subjects: [],
            duration: $('#duration').val(),
            totalmarks: $('#totalmarks').val(),
            passmarks: $('#passmarks').val(),
            instructions: $('#instructions').val(),
        };

        // Loop through all subject input groups and collect their values
        $('#subjectsContainer .input-group').each(function (index) {
            const subjectCode = $(this).find('select[name^="subjects"]').val(); // Get the subject code
            const subjectDate = $(this).find('input[type="date"]').val(); // Get the subject date

            // Push the values into the subjects array
            if (subjectCode && subjectDate) {
                formData.subjects.push({
                    subject_code: subjectCode,
                    date: subjectDate
                });
            }
        });

        // Log the result for debugging
        console.log(formData);

        var d = new Dialog("Creating Test", "Are you sure you want to create this test? " + { formData });
        d.setButtons([
            {
                "name": "Create Test",
                "class": "btn-success",
                "onClick": function (event) {
                    // Make the API call to create the test
                    $.ajax({
                        url: '/api/app/create/createtest',  // Your API endpoint for creating test
                        type: 'POST',
                        data: formData,
                        success: function (response) {
                            console.log('response', response['message']);

                            // Do something after the test is created, like refreshing the test list or showing a success message
                            var t = new Toast('Status', 'now', response['message']);
                            t.show();

                            // Clear the form after successful submission
                            $('#createTestForm').trigger('reset');
                        },
                        error: function (xhr, status, error) {
                            console.error('Error creating test:', error);
                            var t = new Toast('Error', 'now', 'Error creating test');
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
                    $(event.data.modal).modal('hide');
                }
            }
        ]);

        // Show the confirmation dialog
        d.show();
    });
});
