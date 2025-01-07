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
        const form = $('#timetable-form')[0];
        if (!form.checkValidity()) {
            $(form).addClass('was-validated');
            return;
        }

        // Gather form data
        const formData = new FormData(form);

        // Get dynamically added days and slots as key-value pairs
        const daySlotPairs = [];
        $('#day-slot-container .input-group').each(function () {
            const day = $(this).find('select[name="days[]"]').val();
            const slot = $(this).find('select[name="slots[]"]').val();
            if (day && slot) {
                daySlotPairs.push({ day: day, slot: slot });
            }
        });

        // Append day-slot pairs to the formData
        formData.append('daySlotPairs', JSON.stringify(daySlotPairs));

        // Add combined class_room value
        const classRoom = $('#tt-class-department').val() + $('#tt-room').val();
        formData.append('class_room', classRoom);

        console.log("Form Data [new] :", formData);

        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }

        //AJAX request
        $.ajax({
            url: '/api/app/get/tt/assignslot', // Update with your API endpoint
            data: formData,
            type: 'POST',
            processData: false, // Prevent jQuery from processing the FormData object
            contentType: false, // Prevent jQuery from setting the content type
            success: function (response) {
                // Success feedback
                const ttToast = new Toast("now", "success", "Timetable Slot Added");
                ttToast.show();
                console.log("Response:", response);
            },
            error: function (xhr, error) {
                // Error feedback
                const errorMessage = xhr.responseJSON ? xhr.responseJSON.message : "Error Adding Timetable Slot";
                const ttToast = new Toast("now", "error", errorMessage);
                ttToast.show();
                console.error("Error:", error);
            }
        });
    });





    $('#add-slot-btn').on('click', function () {
        const container = document.getElementById('day-slot-container');

        // Create a new row
        const newRow = document.createElement('div');
        newRow.className = 'input-group mb-2';

        // Clone day select template
        const daySelectTemplate = document.getElementById('day-slot-template');
        const daySelect = daySelectTemplate.cloneNode(true);
        daySelect.className = 'form-select';
        daySelect.name = 'days[]';
        daySelect.required = true;
        daySelect.classList.remove('d-none');

        // Clone slot select template
        const slotTemplate = document.getElementById('slot-template');
        const slotSelect = slotTemplate.cloneNode(true);
        slotSelect.className = 'form-select';
        slotSelect.name = 'slots[]';
        slotSelect.required = true;
        slotSelect.classList.remove('d-none');

        // Add remove button
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-danger';
        removeBtn.innerHTML = '<i class="bi bi-dash-circle"></i>';
        removeBtn.addEventListener('click', function () {
            container.removeChild(newRow);
        });

        // Append elements to the new row
        newRow.appendChild(daySelect);
        newRow.appendChild(slotSelect);
        newRow.appendChild(removeBtn);

        // Append the new row to the container
        container.appendChild(newRow);
    });
});
