$(document).ready(function () {

    $('#tt-subject_code').on('change', function () {
        var subject_code = $(this).val();

        $.ajax({
            url: '/api/app/get/tt/batch',
            data: { subject_code: subject_code },
            type: 'POST',
            success: function (data) {
                $('#tt-subject_name').val(data.subject_name);
                var batchSelect = $('#tt-batch');

                var batchToast = new Toast("now", "success", "Batches Loaded");

                batchToast.show();

                console.log(data.batches);

                //enable the batch select
                $('#tt-batch').prop('disabled', false);

                batchSelect.empty();

                batchSelect.append($('<option>', {
                    value: '',
                    text: 'Select the Batch'
                }));

                $.each(data.batches, function (_, batch) {
                    batchSelect.append($('<option>', {
                        value: batch,
                        text: batch
                    }));
                });
            },
            // handle error and show taost for assign the subject to the batch

            error: function (data) {
                var batchToast = new Toast("now", "error", "No Batch Found. Please Assign the Subject to the Batch");
                batchToast.show();
                //disable the batch select
                $('#tt-batch').prop('disabled', true);
            }
        });

    });


    $('#tt-batch').on('change', function () {

        console.log("Batch Changed");

        var subject_code = $('#tt-subject_code').val();
        var batch = $(this).val();

        console.log(subject_code);
        console.log(batch);

        $.ajax({
            url: '/api/app/get/tt/faculty',
            data: { subject_code: subject_code, batch: batch },
            type: 'POST',
            success: function (data) {

                console.log(data);

                $('#tt-faculty').empty();

                //enable the faculty select
                $('#tt-faculty').prop('disabled', false);

                //enable the semester select
                $('#tt-semester').prop('disabled', false);

                var facultyToast = new Toast("now", "success", "Faculties Loaded");
                facultyToast.show();

                $('#tt-faculty').append($('<option>', {
                    value: '',
                    text: 'Select the Faculty'
                }));

                $.each(data.faculties, function (_, faculty) {
                    $('#tt-faculty').append($('<option>', {
                        value: faculty.id,
                        text: faculty.name + ' (' + faculty.department + ')'
                    }));
                });
            },
            error: function (data) {
                var facultyToast = new Toast("now", "error", "No faculty Found. Please Assign the Subject to the faculty");
                facultyToast.show();
            }
        });

    });


    $('#tt-faculty').on('change', function () {

        var subject_code = $('#tt-subject_code').val();
        var batch = $('#tt-batch').val();
        var semester = $('#tt-semester').val();
        var faculty_id = $(this).val();


        $.ajax({
            url: '/api/app/get/tt/class',
            data: { faculty_id: faculty_id, subject_code: subject_code, batch: batch, semester: semester },
            type: 'POST',
            success: function (data) {

                console.log(data);

                var classToast = new Toast("now", "success", "Class Loaded");
                classToast.show();

                $('#tt-section').val(data.classes.section);
                $('#tt-class_id').val(data.classes.class_id);


            },
            error: function (data) {
                var classToast = new Toast("now", "error", "No Class Found. Please Assign the Subject to the Class");
                classToast.show();
            }
        });

    });

    $('#tt-submit-btn').on('click', function (event) {
        event.preventDefault();

        // Form validation
        const form = document.getElementById('timetable-form');
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }

        // Gather form data
        var department = $('#tt-department').val();
        var subject_code = $('#tt-subject_code').val();
        var batch = $('#tt-batch').val();
        var semester = $('#tt-semester').val();
        var faculty_id = $('#tt-faculty').val();
        var class_id = $('#tt-class_id').val();
        var section = $('#tt-section').val();
        var day = $('#tt-day').val();
        var department = $('#tt-class-department').val();
        var room = $('#tt-room').val();
        var slot = $('#tt-slot').val();

        var class_room = department + room;

        console.log(subject_code, batch, semester, faculty_id, class_id, section, day, slot, class_room);

        // Prepare form data for submission


        var formData = new FormData();
        formData.append('department', department);
        formData.append('subject_code', subject_code);
        formData.append('batch', batch);
        formData.append('semester', semester);
        formData.append('faculty_id', faculty_id);
        formData.append('class_id', class_id);
        formData.append('section', section);
        formData.append('day', day);
        formData.append('slot', slot);
        formData.append('class_room', class_room);


        console.log("Form Data Loaded");

        // AJAX request
        $.ajax({
            url: '/api/app/get/tt/assignslot', // Update the endpoint as needed
            data: formData,
            type: 'POST',
            processData: false, // Prevent jQuery from processing the FormData object
            contentType: false, // Prevent jQuery from setting the content type
            success: function (response) {
                // Success feedback
                var ttToast = new Toast("now", "success", "Timetable Slot Added");
                ttToast.show();

                console.log("Response:", response);
            },
            error: function (xhr, error) {
                // Error feedback
                var errorMessage = xhr.responseJSON ? xhr.responseJSON.message : "Error Adding Timetable Slot";
                var ttToast = new Toast("now", "error", errorMessage);
                ttToast.show();

                console.error("Error:", error);
            }
        });

    });

});
