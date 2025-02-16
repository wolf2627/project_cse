/* Processed on 16/2/2025 @ 4:44:12 */
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

function loadAssignRoleForms() {
    const operation = document.getElementById('assign-roles-operation').value;
    const formContainer = document.getElementById('assign-roles-dynamic-form');
    formContainer.innerHTML = ''; // Clear previous form

    if (operation === 'assign') {
        console.log('assign clicked');

        // ajax call to the backend for the form
        fetch('/api/app/template/assignrole/type?operation=assign')
            .then(response => response.text())
            .then(data => {
                formContainer.innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
                formContainer.innerHTML = `<div class="alert alert-danger">An error occurred.</div>`;
            });

    } else if (operation === 'unassign') {
        // ajax call to the backend for the form
        fetch('/api/app/template/unassignrole?operation=unassign')
            .then(response => response.text())
            .then(data => {
                formContainer.innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
                formContainer.innerHTML = `<div class="alert alert-danger">An error occurred.</div>`;
            });

    }
}

function loadAssignRoleForms2(){
    const category = document.getElementById('AssignRole-UserType').value;

    const formContainer = document.getElementById('assign-roles-other-fields-dynamic-form');

    formContainer.innerHTML = ''; // Clear previous form

    // ajax call to the backend for the form

    fetch(`/api/app/template/assignrole/otherfields?category=${category}`)
        .then(response => response.text())
        .then(data => {
            formContainer.innerHTML = data;
        })
        .catch(error => {
            console.error('Error:', error);
            formContainer.innerHTML = `<div class="alert alert-danger">An error occurred.</div>`;
        });
}


$(document).ready(function () {

    console.log('assigntutor is ready');

    $('#assign-tutor-btn').click(function (event) {

        console.log('assign-tutor-btn clicked');
        event.preventDefault();

        var faculty_id = $('#assign-faculty_id').val();
        var batch = $('#assign-batch').val();
        var department = $('#assign-department').val();
        var section = $('#assign-section').val();

        $('#assign-faculty_id').css('border-color', '');
        $('#assign-batch').css('border-color', '');
        $('#assign-department').css('border-color', '');
        $('#assign-section').css('border-color', '');

        if (faculty_id === '') {
            //highlight the field
            $('#assign-faculty_id').css('border-color', 'red');
            var errorToast = new Toast('now', 'error', 'Please fill the Faculty ID field');
            errorToast.show();
            return;
        }
        if (department === '') {
            //highlight the field
            $('#assign-department').css('border-color', 'red');
            var errorToast = new Toast('now', 'error', 'Please fill the Department field');
            errorToast.show();
            return;
        }
        if (batch === '') {
            //highlight the field
            $('#assign-batch').css('border-color', 'red');
            var errorToast = new Toast('now', 'error', 'Please fill the Batch field');
            errorToast.show();
            return;
        } if (section === '') {
            //highlight the field
            $('#assign-section').css('border-color', 'red');
            var errorToast = new Toast('now', 'error', 'Please fill the Section field');
            errorToast.show();
            return;
        }

        $('#assign-faculty_id').css('border-color', 'green');
        $('#assign-batch').css('border-color', 'green');
        $('#assign-department').css('border-color', 'green');
        $('#assign-section').css('border-color', 'green');


        console.log('faculty_id: ' + faculty_id);
        console.log('batch: ' + batch);
        console.log('department: ' + department);
        console.log('section: ' + section);

        var formdata = new FormData();

        formdata.append('faculty_id', faculty_id);
        formdata.append('batch', batch);
        formdata.append('department', department);
        formdata.append('section', section);

        $.ajax({
            url: '/api/app/tutor/assign',
            type: 'POST',
            data: formdata,
            contentType: false,
            processData: false,
            success: function (data) {
                console.log(data);
                if (data.success) {
                    var successToast = new Toast('now', 'success', 'Tutor Assigned Successfully');
                    successToast.show();
                } else {
                    var errorToast = new Toast('now', 'error', 'Tutor Assign Failed: ' + data.message);
                    errorToast.show();
                }

                document.getElementById('assign-tutor-form').reset();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                var errorMessage = 'Tutor Assign Failed: ' + textStatus;
                if (jqXHR.status === 500) {
                    errorMessage += ' - ' + jqXHR.responseJSON.message;
                    document.getElementById('assign-tutor-form').reset();
                } else if (jqXHR.status === 404) {
                    errorMessage += ' - Resource not found';
                    document.getElementById('assign-tutor-form').reset();
                }
                var errorToast = new Toast('now', 'error', errorMessage);
                errorToast.show();
            }
        });

        $('#assign-faculty_id').css('border-color', '');
        $('#assign-batch').css('border-color', '');
        $('#assign-department').css('border-color', '');
        $('#assign-section').css('border-color', '');

    });

});
$(document).ready(function () {

    console.log('assignyearincharge is ready');

    $('#assign-yearincharge-btn').click(function (event) {

        console.log('assign-yearincharge-btn clicked');
        event.preventDefault();

        var faculty_id = $('#assign-faculty_id').val();
        var batch = $('#assign-batch').val();
        var department = $('#assign-department').val();

        $('#assign-faculty_id').css('border-color', '');
        $('#assign-batch').css('border-color', '');
        $('#assign-department').css('border-color', '');

        if (faculty_id === '') {
            //highlight the field
            $('#assign-faculty_id').css('border-color', 'red');
            var errorToast = new Toast('now', 'error', 'Please fill the Faculty ID field');
            errorToast.show();
            return;
        }
        if (department === '') {
            //highlight the field
            $('#assign-department').css('border-color', 'red');
            var errorToast = new Toast('now', 'error', 'Please fill the Department field');
            errorToast.show();
            return;
        }
        if (batch === '') {
            //highlight the field
            $('#assign-batch').css('border-color', 'red');
            var errorToast = new Toast('now', 'error', 'Please fill the Batch field');
            errorToast.show();
            return;
        }

        $('#assign-faculty_id').css('border-color', 'green');
        $('#assign-batch').css('border-color', 'green');
        $('#assign-department').css('border-color', 'green');


        console.log('faculty_id: ' + faculty_id);
        console.log('batch: ' + batch);
        console.log('department: ' + department);

        var formdata = new FormData();

        formdata.append('faculty_id', faculty_id);
        formdata.append('batch', batch);
        formdata.append('department', department);

        $.ajax({
            url: '/api/app/yearincharge/assign',
            type: 'POST',
            data: formdata,
            contentType: false,
            processData: false,
            success: function (data) {
                console.log(data);
                if (data.success) {
                    var successToast = new Toast('now', 'success', 'Year Incharge Assigned Successfully');
                    successToast.show();
                } else {
                    var errorToast = new Toast('now', 'error', 'Year Incharge Assign Failed: ' + data.message);
                    errorToast.show();
                }

                document.getElementById('assign-yearincharge-form').reset();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                var errorMessage = 'Year Incharge Assign Failed: ' + textStatus;
                if (jqXHR.status === 500) {
                    errorMessage += ' - ' + jqXHR.responseJSON.message;
                    document.getElementById('assign-yearincharge-form').reset();
                } else if (jqXHR.status === 404) {
                    errorMessage += ' - Resource not found';
                    document.getElementById('assign-yearincharge-form').reset();
                }
                var errorToast = new Toast('now', 'error', errorMessage);
                errorToast.show();
            }
        });

        $('#assign-faculty_id').css('border-color', '');
        $('#assign-batch').css('border-color', '');
        $('#assign-department').css('border-color', '');

    });

});

$('#class-wise-year-report-btn').click(function () {

    var test_id = this.value;
    var test_name = document.getElementById('test_name_class_wise_year').value;
    var batch = document.getElementById('batch_class_wise_year').value;
    var department = document.getElementById('department_class_wise_year').value;
    var semester = document.getElementById('semester_class_wise_year').value;

    //remove space between words
    test_name = test_name.replace(/\s/g, '');

    var classwiseToast = new Toast("Report", "Now", 'Report is being generated.');
    classwiseToast.show();

    // Send a POST request to the server to generate the PDF
    $.ajax({
        url: '/generate_year_report_classwise',
        type: 'POST',
        data: {
            test_id: test_id
        },
        xhrFields: {
            responseType: 'blob' // Ensure the response is treated as a Blob (binary data)
        },
        success: function (response) {

            var now = new Date();
            var DateTime = now.getFullYear() +
                ('0' + (now.getMonth() + 1)).slice(-2) +
                ('0' + now.getDate()).slice(-2) +
                ('0' + now.getHours()).slice(-2) +
                ('0' + now.getMinutes()).slice(-2) +
                ('0' + now.getSeconds()).slice(-2);

            var successToast = new Toast("Report", "Success", 'Report generated successfully.');
            successToast.show();

            // Create a URL for the blob received in the response
            var fileURL = URL.createObjectURL(response);

            // Create a link element to trigger the download
            var downloadLink = document.createElement('a');
            downloadLink.href = fileURL;
            downloadLink.download = test_name + department + batch + semester + '_Report_' + DateTime + '.pdf'; // Set the default filename
            document.body.appendChild(downloadLink); // Append the link temporarily to the DOM
            downloadLink.click(); // Trigger the click to download
            document.body.removeChild(downloadLink); // Remove the link after the download is triggered
        },
        error: function (xhr, status, error) {
            var errorToast = new Toast("Report", "Error", 'An error occurred while generating the report.');
            errorToast.show();
        }
    });
});



$(document).ready(function () {
    console.log("Approve script loaded [new loaded] ");

    $(".approve-btn").click(function () {
        console.log("Approve button clicked");
        let button = $(this);
        let contestId = button.data("contest-id");
        let studentId = button.data("student-id");

        let pendingCountElement = $("#pending-count");
        let pendingCount = parseInt(pendingCountElement.data("pending-count"));

        console.log("pendingCount: " + pendingCount);

        console.log("Approving registration for student " + studentId + " in contest " + contestId);

        // Show loading state
        button.prop("disabled", true).html('<i class="fas fa-spinner fa-spin"></i> Approving...');

        $.ajax({
            url: "/api/contest/confirmregistration",
            type: "POST",
            data: { contestId: contestId, studentId: studentId },
            success: function (response) {
                $("#status-" + studentId)
                    .removeClass("bg-secondary")
                    .addClass("bg-success")
                    .text("Approved");


                button.replaceWith('<span class="text-success">Approved</span>');

                // Show success message
                var toast = new Toast("now", "success", "Registration approved successfully");
                toast.show();
                pendingCount = pendingCount - 1;
                pendingCountElement.data("pending-count", pendingCount);
                pendingCountElement.text(pendingCount + " Pending");


            },
            error: function () {
                alert("An error occurred while processing the request.");
                button.prop("disabled", false).html('<i class="fas fa-check"></i> Approve');
            }
        });
    });
});
$(document).ready(function () {
    console.log("Register script loaded successfully");
    $(".register-btn").click(function () {
        var contestId = $(this).data("contest-id");
        var button = $(this);
        button.prop("disabled", true);
        button.html("Registering...");

        $.ajax({
            url: "/api/contest/register",
            type: "POST",
            data: { contestId: contestId },
            success: function (response) {
                var toast = new Toast("now", "success", "Registration successful");
                toast.show();
                button.html("Registered");
            },
            error: function (xhr, status, error) {
                var errorMessage = xhr.responseJSON ? xhr.responseJSON.message : "Registration failed";
                var toast = new Toast("now", "error", errorMessage + " Please try again later.");
                toast.show();
                button.html("Failed");
                button.prop("disabled", false);
            }
        });
    });
});

$(document).ready(function () {

    console.log('createadmin is ready');

    $('#admin-create-account-btn').click(function () {
        console.log('create admin account clicked');
        var username = $('#admin-username').val();
        var password = $('#admin-password').val();
        var email = $('#admin-email').val();
        var confirm_password = $('#admin-confirm-password').val();

        console.log('username: ' + username);
        console.log('password: ' + password);
        console.log('email: ' + email);
        console.log('confirm_password: ' + confirm_password);

        if (password !== confirm_password) {
            alert('Passwords do not match');
            return;
        }

        var formData = new FormData();
        formData.append('user', username);
        formData.append('password', password);
        formData.append('email', email);
        formData.append('confirm_password', confirm_password);

        $.ajax({
            url: '/api/app/create/admin',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                console.log('create admin account success');
                console.log(data);
                if (data.message === 'success') {
                    var successToast = new Toast('now', 'success', 'Admin account created successfully');
                    successToast.show();
                } else {
                    var errorToast = new Toast('now', 'error', 'Error creating admin account');
                    errorToast.show();
                }
            },
            error: function (err) {
                console.log('create admin account error');
                console.log(err);
                var errorToast = new Toast('now', 'error', 'Error creating admin account');
                errorToast.show();
            }
        });


    });

});
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

$(document).ready(function () {

    if ($('#date-wise-stud-atten').length > 0) {

        // Attendance Chart Data
        const attendanceData = JSON.parse(document.getElementById('attendanceData').value);

        const attendanceChartData = {
            labels: ['Present', 'Absent', 'On-Duty', 'Not Marked'],
            datasets: [{
                data: [
                    attendanceData.summary.total_present ?? 0,
                    attendanceData.summary.total_absent ?? 0,
                    attendanceData.summary.total_on_duty ?? 0,
                    attendanceData.summary.total_not_marked ?? 0
                ],
                backgroundColor: ['#28a745', '#dc3545', '#ffc107', '#6c757d'],
                hoverOffset: 4
            }]
        };

        // Config for Attendance Chart
        const attendanceChartConfig = {
            type: 'pie',
            data: attendanceChartData,
            options: {
                responsive: true,
                maintainAspectRatio: false, // Ensure it resizes correctly
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                let label = context.label || '';
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(2);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        };

        // Render Attendance Chart
        const attendanceChart = new Chart(
            document.getElementById('attendanceChart'),
            attendanceChartConfig
        );
    }
});

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

$(document).ready(function () {

    console.log("Faculty Timetable JS loaded");

    if ($('.faculty-timetable-cont').length > 0) {

        console.log("Faculty Timetable page detected");

        // DOM Elements
        const timetableContainer = document.getElementById("weeklyTimetable");
        const showAllButton = document.getElementById("showAllButton");


        const facultyId = document.getElementById("faculty_id").value;

        // Fetch timetable data from API
        $.ajax({
            url: "/api/app/get/tt/facultytimetable",
            type: "POST",
            data: {
                faculty_id: facultyId
            },
            success: function (response) {
                if (response.success) {
                    const timetableData = response.timetable;
                    populateTimetable(timetableData);
                } else {
                    console.error("Failed to fetch timetable data");
                }
            },
            error: function (xhr, status, error) {
                console.error("Error occurred while fetching timetable:", error);
            }
        });

        // Populate timetable
        function populateTimetable(timetableData) {
            const daysOrder = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            const today = new Date().toLocaleString("en-US", {
                weekday: "long"
            });

            daysOrder.forEach((day, index) => {
                const dayClasses = timetableData[day] || [];
                let classDetails = "";

                if (dayClasses.length > 0) {
                    dayClasses.forEach((slot) => {
                        classDetails += `
                    <div class="class-details mb-2 bg-dark-light">
                        <p><strong>${slot.department} - ${slot.class} - ${slot.section}</strong></p>
                        <p>${slot.time} | Sem: ${slot.semester}</p>
                    </div>
                `;
                    });
                } else {
                    classDetails = `<p class="no-classes">No classes scheduled</p>`;
                }

                timetableContainer.innerHTML += `
            <div class="col" data-order="${index}">
                <div class="card day-card shadow-sm ${day === today ? "current-day" : ""}" data-day="${day}">
                    <div class="day-header">
                        <i class="icon fa-solid fa-calendar-day"></i>
                        <div>${day}</div>
                    </div>
                    <div class="card-body">
                        ${classDetails}
                    </div>
                </div>
            </div>
        `;
            });

            // Sort and reinitialize Masonry
            const elements = Array.from(timetableContainer.children);
            elements.sort((a, b) => a.getAttribute("data-order") - b.getAttribute("data-order"));
            elements.forEach((element) => timetableContainer.appendChild(element));

            const msnry = new Masonry(timetableContainer, {
                itemSelector: ".col",
                columnWidth: ".col",
                percentPosition: true
            });

            // Initially blur other days
            toggleShowAll(msnry);
        }

        // Toggle between showing all days and only the current day
        function toggleShowAll(msnry) {
            const dayCards = document.querySelectorAll(".day-card");
            const isShowingAll = showAllButton.textContent === "Show All";

            dayCards.forEach((card) => {
                if (!card.classList.contains("current-day")) {
                    if (isShowingAll) {
                        card.classList.remove("blurred");
                    } else {
                        card.classList.add("blurred");
                    }
                }
            });

            showAllButton.textContent = isShowingAll ? "Show Only Today" : "Show All";
            msnry.layout();
        }

        window.toggleShowAll = toggleShowAll;

    }
});
$(document).ready(function () {
    setInterval(function () {
        var text1 = $('#footer-text-1');
        var text2 = $('#footer-text-2');

        if (text1.is(':visible')) {
            text1.fadeOut('slow', function () {
                text2.fadeIn('slow');
            });
        } else {
            text2.fadeOut('slow', function () {
                text1.fadeIn('slow');
            });
        }
    }, 5000);
});
// Function to set a cookie
function setCookie(name, value, daysToExpire) {
    var expires = "";

    if (daysToExpire) {
        var date = new Date();
        date.setTime(date.getTime() + (daysToExpire * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }

    document.cookie = name + "=" + value + expires + "; path=/";
}

$(document).ready(function () {
    // Event listener for 'Sign out' button click
    $('#signOutBtn').on('click', function (e) {
        e.preventDefault(); // Prevent the default link behavior

        // Create a new Dialog instance for logout confirmation
        var d = new Dialog("Sign out Confirmation", "Are you sure you want to sign out?");

        // Set the dialog buttons
        d.setButtons([
            {
                "name": "Sign Out",
                "class": "btn-danger",
                "onClick": function (event) {
                    // Perform the logout action (AJAX request)
                    $.ajax({
                        url: '/api/auth/logout',  // Your API endpoint for logging out
                        type: 'POST',
                        success: function (response) {
                            // On success, redirect to login page
                            window.location.href = '/'; // Or wherever you want to redirect after logout
                        },
                        error: function (error) {
                            // Handle any errors during the logout process
                            alert("Error signing out. Please try again.");
                        }
                    });

                    // Close the dialog after confirming the logout action
                    $(event.data.modal).modal('hide');
                }
            },
            {
                "name": "Cancel",
                "class": "btn-success",
                "onClick": function (event) {
                    // User canceled, just hide the dialog
                    console.log('User canceled sign out.');
                    $(event.data.modal).modal('hide');
                }
            }
        ]);

        // Show the dialog
        d.show();
    });
});




$('#generateExcel-btn').click(function () {
    var formData = new FormData();
    var batch = $('#batch').val();
    var semester = $('#semester').val();
    var section = $('#section').val();
    var subject = $('#subject_code').val();
    var department = $('#department').val();
    var test_name = $('#test_name').val();
    var faculty_id = $('#faculty_id').val();

    // Append data
    formData.append('batch', batch);
    formData.append('semester', semester);
    formData.append('section', section);
    formData.append('subject_code', subject);
    formData.append('department', department);
    formData.append('test_name', test_name);
    formData.append('faculty_id', faculty_id);


    console.log(batch, semester, section, subject, department, test_name, faculty_id);

    console.log(formData);

    // Send the request
    $.ajax({
        url: '/generate_class_sub_excel', // Your endpoint
        type: 'POST',
        data: formData,
        processData: false, // Prevent jQuery from converting the data
        contentType: false, // Let the browser set the content type for FormData
        xhrFields: {
            responseType: 'blob' // Important to handle binary response
        },
        success: function (response) {
            // Create a URL for the blob (Excel file)
            var blob = response;
            var url = window.URL.createObjectURL(blob);

            // Create an anchor element for download
            var a = document.createElement('a');
            a.href = url;
            a.download = 'report_' + batch + '_' + semester + '_' + section + '_' + subject + '_' + department + '_' + test_name + '.xlsx'; // Set the download filename

            // Trigger the download
            a.click();

            // Clean up the URL object
            window.URL.revokeObjectURL(url);
        },
        error: function (xhr, status, error) {
            console.error('Error:', error);
        }
    });
});

$(document).ready(function () {
    let permissions = []; // Local cache for permissions
    let editingIndex = null;

    let $apiUrl = '/api/app/permission';

    const $form = $('#permissionForm');
    const $permissionsTable = $('#permissionsTable');


    let $editBtnClicked = false;
    let $EditingPermissionId = null;

    $($form).on('submit', function (e) {
        e.preventDefault(); // Prevent the form from submitting normally

        let $permission_name = document.getElementById('permission_name').value;
        let $description = document.getElementById('description').value;
        let $permission_category = document.getElementById('permission_category').value;

        const formData = {
            permission_name: $permission_name,
            description: $description,
            permission_category: $permission_category
        };

        if ($editBtnClicked) {
            // Update existing permission
            formData.permission_id = $EditingPermissionId || null;

            var d = new Dialog('Update Permission',
                '<div style="font-size: 16px; line-height: 1.6;">' +
                '<strong>Are you sure you want to update the permission as below?</strong><br>' +
                '<br><strong>Permission Name:</strong> ' + $permission_name + '<br>' +
                '<strong>Description:</strong> ' + $description + '<br>' +
                '<strong>Permission Category:</strong> ' + $permission_category + '<br>' +
                '</div>');

            d.setButtons([
                {
                    "name": "Update",
                    "class": "btn-primary",
                    "onClick": function (event) {

                        $.ajax({
                            url: $apiUrl + '/update', // Replace with your API URL
                            method: 'POST',
                            data: formData,
                            success: function (response) {
                                var SuccessToast = new Toast('now', 'success', response.message);
                                SuccessToast.show();

                                const data = {
                                    permission_name: $permission_name,
                                    description: $description,
                                    permission_category: $permission_category,
                                    permission_id: response.permission_id,
                                };

                                console.log(data);

                                updateTable(data);

                                resetForm();

                                //Change the button text to 'Create'
                                $('#permissionForm button[type="submit"]').text('Create');

                                $editBtnClicked = false;
                                $EditingPermissionId = null;

                                // Remove the cancel button
                                $('#cancelPermission').remove();
                            },
                            error: function (xhr) {
                                let errorMessage = 'An error occurred';

                                if (xhr.status === 409) {
                                    errorMessage = 'No changes Made.';
                                } else if (xhr.status === 500) {
                                    errorMessage = 'Please try again later.';
                                } else {
                                    errorMessage = xhr.responseJSON?.message || 'Unknown error occurred.';
                                }

                                var ErrorToast = new Toast('now', 'error', errorMessage);
                                ErrorToast.show();
                                console.error('Error creating permission:', xhr);

                                resetForm(); // Reset the form

                                //Change the button text to 'Create'
                                $('#permissionForm button[type="submit"]').text('Create');

                                $editBtnClicked = false;
                                $EditingPermissionId = null;

                                // Remove the cancel button
                                $('#cancelPermission').remove();

                            },
                        });
                        $(event.data.modal).modal('hide');
                    }
                },
                {
                    "name": "Cancel",
                    "class": "btn-secondary",
                    "onClick": function (event) {

                        resetForm();

                        //Change the button text to 'Create'
                        $('#permissionForm button[type="submit"]').text('Create');

                        $editBtnClicked = false;
                        $EditingPermissionId = null;

                        // Remove the cancel button
                        $('#cancelPermission').remove();

                        $(event.data.modal).modal('hide');
                    }
                }
            ]);

            d.show();
        } else {
            // Create a new permission

            var d = new Dialog('Create Permission',
                '<div style="font-size: 16px; line-height: 1.6;">' +
                '<strong>Are you sure you want to create the permission below?</strong><br>' +
                '<br><strong>Permission Name:</strong> ' + $permission_name + '<br>' +
                '<strong>Description:</strong> ' + $description + '<br>' +
                '<strong>Permission Category:</strong> ' + $permission_category + '<br>' +
                '</div>');

            d.setButtons([
                {
                    "name": "Create",
                    "class": "btn-primary",
                    "onClick": function (event) {
                        $.ajax({
                            url: $apiUrl + '/create', // Replace with your API URL
                            method: 'POST',
                            data: formData,
                            success: function (response) {
                                var SuccessToast = new Toast('now', 'success', response.message);
                                SuccessToast.show();

                                const data = {
                                    permission_name: $permission_name,
                                    description: $description,
                                    permission_category: $permission_category,
                                    permission_id: response.permissionId,
                                };
                                updateTable(data);
                            },
                            error: function (xhr) {
                                let errorMessage = 'An error occurred';

                                if (xhr.status === 409) {
                                    errorMessage = 'The permission already exists.';
                                } else if (xhr.status === 500) {
                                    errorMessage = 'Please try again later.';
                                } else {
                                    errorMessage = xhr.responseJSON?.message || 'Unknown error occurred.';
                                }

                                var ErrorToast = new Toast('now', 'error', errorMessage);
                                ErrorToast.show();
                                console.error('Error creating permission:', xhr);
                            },
                        });

                        resetForm();
                        $(event.data.modal).modal('hide');
                    }
                },
                {
                    "name": "Cancel",
                    "class": "btn-secondary",
                    "onClick": function (event) {

                        resetForm();

                        $(event.data.modal).modal('hide');
                    }
                }
            ]);

            d.show();


        }
    });


    function updateTable(permission) {

        // Check if the permission already exists
        $permissionsTable.find(`tr[data-id="${permission.permission_id}"]`).remove();

        const row = `
                <tr data-id="${permission.permission_id}">
                    <td>${permission.permission_name}</td>
                    <td>${permission.description}</td>
                    <td>${permission.permission_category}</td>
                    <td>
                        <button class="btn btn-warning btn-sm editPermission" data-id="${permission.permission_id}" data-name="${permission.permission_name}">Edit</button>
                        <button class="btn btn-danger btn-sm deletePermission" data-id="${permission.permission_id}" data-name="${permission.permission_name}">Delete</button> 
                    </td>
                </tr>
            `;
        $permissionsTable.prepend(row);
    }


    $(document).on('click', '.editPermission', function () {
        const permissionId = $(this).data('id'); // Retrieve permission ID from the button
        const permissionName = $(this).data('name'); // Retrieve permission name from the button
        editPermission(permissionId, permissionName);
    });

    function editPermission(permissionId) {
        fetchPermissionById(permissionId, function (permission) {
            console.log("permission_name: " + permission.permission_name);
            console.log("description: " + permission.description);

            // Prefill the form with the fetched data
            $('#permission_name').val(permission.permission_name);
            $('#description').val(permission.description);
            $('#permission_category').val(permission.permission_category);

            $editBtnClicked = true;
            $EditingPermissionId = permissionId;

            //Change the button text to 'Update'
            $('#permissionForm button[type="submit"]').text('Update');

            if (!$('#cancelPermission').length) {
                $('#permissionForm').append('<button type="button" class="btn btn-secondary" id="cancelPermission" style="display:inline-block">Cancel</button>');
            }


            // Scroll to the top of the form
            $('html, body').animate({
                scrollTop: $form.offset().top
            }, 500);

        });
    }

    $(document).on('click', '#cancelPermission', function () {
        console.log('Cancel button clicked');
        // Add your cancel logic here

        resetForm(); // Reset the form

        //Change the button text to 'Create'
        $('#permissionForm button[type="submit"]').text('Create');

        $editBtnClicked = false;
        $EditingPermissionId = null;

        // Remove the cancel button
        $('#cancelPermission').remove();

    });


    function fetchPermissionById(id, callback) {
        let data = { permission_id: id };

        $.ajax({
            url: '/api/app/permission/get/by/id',
            method: 'POST',
            data: data,
            success: function (response) {
                console.log(response);
                callback({
                    permission_name: response.permission_name,
                    description: response.description,
                    permission_category: response.permission_category,
                });
            },
            error: function () {
                var ErrorToast = new Toast('now', 'error', 'An error occurred');
                ErrorToast.show();
            },
        });
    }



    $(document).on('click', '.deletePermission', function () {
        const permissionId = $(this).data('id'); // Retrieve the permission ID from the data-id attribute
        const permissionName = $(this).data('name'); // Retrieve the permission name from the data-name attribute
        deletePermission(permissionId, permissionName);
    });

    function deletePermission(permissionId, permissionName) {
        var d = new Dialog('Delete Permission', 'Are you sure you want to delete the permission ' + permissionName + '?');

        d.setButtons([
            {
                "name": "Delete",
                "class": "btn-danger",
                "onClick": function (event) {

                    // Send a DELETE request to the server if permissions are stored on the backend
                    $.ajax({
                        url: $apiUrl + '/delete',
                        method: 'POST',
                        data: { permission_id: permissionId },  // permission_id
                        success: function () {
                            var SuccessToast = new Toast('now', 'success', 'Permission deleted successfully');
                            SuccessToast.show();

                        },
                        error: function (xhr, status, error) {
                            console.error('Error deleting permission');
                            var ErrorToast = new Toast('now', 'error', 'An error occurred');
                            ErrorToast.show();
                        }
                    });

                    removeTableRow(permissionId);

                    $(event.data.modal).modal('hide');
                }
            },
            {
                "name": "Cancel",
                "class": "btn-secondary",
                "onClick": function (event) {
                    $(event.data.modal).modal('hide');
                }
            }
        ]);

        d.show();
    }

    function removeTableRow(permissionId) {
        $permissionsTable.find(`tr[data-id="${permissionId}"]`).remove();
    }



    function resetForm() {
        $form[0].reset();
    }

});

function loadRolesForm() {
    const operation = document.getElementById('manage-roles-operation').value;
    const formContainer = document.getElementById('manage-roles-dynamic-form');
    formContainer.innerHTML = ''; // Clear previous form

    if (operation === 'create') {

        // ajax call to the backend for the form
        fetch('/api/app/template/createrole?operation=create')
            .then(response => response.text())
            .then(data => {
                formContainer.innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
                formContainer.innerHTML = `<div class="alert alert-danger">An error occurred.</div>`;
            });


    } else if (operation === 'update') {
        // ajax call to the backend for the form
        fetch('/api/app/template/updaterole?operation=update')
            .then(response => response.text())
            .then(data => {
                formContainer.innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
                formContainer.innerHTML = `<div class="alert alert-danger">An error occurred.</div>`;
            });

    } else if (operation === 'delete') {
        // ajax call to the backend for the form
        fetch('/api/app/template/deleterole?operation=delete')
            .then(response => response.text())
            .then(data => {
                formContainer.innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
                formContainer.innerHTML = `<div class="alert alert-danger">An error occurred.</div>`;
            });
    }
}

function manageRoleSubmitForm(operation) {
    const formData = new FormData(); // Create a FormData object

    let link;  // Declare link here to use it across all cases

    // Add data to FormData based on operation
    if (operation === 'create') {
        formData.append('roleName', document.getElementById('roleName').value);
        formData.append('roleCategory', document.getElementById('roleCategory').value);
        formData.append('description', document.getElementById('description').value);
        link = `/api/app/role/create`;
    } else if (operation === 'update') {
        formData.append('roleId', document.getElementById('roleId').value);
        formData.append('roleName', document.getElementById('roleName').value);
        formData.append('description', document.getElementById('description').value);
        link = `/api/app/role/update`;
    } else if (operation === 'delete') {
        formData.append('roleId', document.getElementById('roleId').value);
        link = `/api/app/role/delete`;
    }

    // Make an AJAX call to the backend
    fetch(link, {
        method: 'POST',
        body: formData, // Send FormData directly
    })
        .then(response => response.json())
        .then(data => {
            const resultDiv = document.getElementById('result');
            if (data.success) {
                const successToast = new Toast('Success', 'now', data.message); // Use const for toast
                successToast.show();
                // Clear the form by reloading its HTML structure
                loadRolesForm();
            } else {
                const failureToast = new Toast('Failure', 'now', data.message); // Fixed typo here
                failureToast.show();
                // Clear the form by reloading its HTML structure
                loadRolesForm();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const errorToast = new Toast('Error', 'now', 'An error occurred.'); // Use const for toast
            errorToast.show();
            // Clear the form and reload its HTML structure
            loadRolesForm();
        });
}



$(document).ready(function () {

    console.log('removetutor is ready');

    function loadtutors() {
        $.ajax({
            url: '/api/app/tutor/get/tutors',
            type: 'POST',
            success: function (data) {
                // console.log(data);
                var tutorTableBody = $('#tutor-table tbody');
                tutorTableBody.empty();
                for (var i = 0; i < data.tutors.length; i++) {
                    var tutor = data.tutors[i];
                    var tr = $('<tr></tr>');
                    tr.append('<td>' + tutor.faculty_id + '</td>');
                    tr.append('<td>' + tutor.faculty_name + '</td>');
                    tr.append('<td>' + tutor.department + '</td>');
                    tr.append('<td>' + tutor.batch + '</td>');
                    tr.append('<td>' + tutor.section + '</td>');
                    tr.append('<td><button class="btn btn-danger remove-tutor-btn" data-tutor-id="' + tutor.faculty_id + '"> Unassign </button></td>');
                    tutorTableBody.append(tr);
                }
            },
            error: function (data) {
                console.log(data);
                var errorToast = new Toast('now', 'error', 'Error loading Tutors or no Tutors found');
                errorToast.show();
                $('#tutor-table').hide();
                $('#remove-tutor').empty();
                $('#remove-tutor').append('<p class="alert alert-warning text-center">No Tutors found</p>');
            }
        });
    }


    $(document).on('click', '.remove-tutor-btn', function () {
        var tutor_id = $(this).data('tutor-id');
        console.log('remove-tutor-btn clicked');
        console.log('tutor_id: ' + tutor_id);

        var formdata = new FormData();
        formdata.append('faculty_id', tutor_id);

        $.ajax({
            url: '/api/app/tutor/unassign',
            type: 'POST',
            data: formdata,
            contentType: false,
            processData: false,
            success: function (data) {
                console.log(data);
                var successToast = new Toast('now', 'success', 'Tutor unassigned successfully');
                successToast.show();
                loadtutors();
            },
            error: function (data) {
                console.log(data);
                var errorToast = new Toast('now', 'error', 'Error unassigning Tutor');
                errorToast.show();
            }
        });

        loadtutors();
    });

    if ($('#remove-tutor-container').length) {
        loadtutors();
    }

});

$('#markAttendance-container').ready(function () {
    console.log('markAttendance-container ready');

    //$('#submitAttendanceBtn').hide();

    $('#classSelect-att').on('change', function () {

        $('#submitAttendanceBtn').show();

        var classId = $('#classSelect-att').val();
        console.log('classId: ' + classId);
        var section = $('#classSelect-att option:selected').data('section');
        var subject = $('#classSelect-att option:selected').data('subject');
        var batch = $('#classSelect-att option:selected').data('batch');
        var semester = $('#classSelect-att option:selected').data('semester');
        var date = $('#classSelect-att option:selected').data('date');
        var day = $('#classSelect-att option:selected').data('day');
        var timeslot = $('#classSelect-att option:selected').data('timeslot');
        var department = $('#classSelect-att option:selected').data('department');


        // if values are unundefined, return
        if (classId == undefined || section == undefined || subject == undefined || batch == undefined || semester == undefined || date == undefined || day == undefined || timeslot == undefined) {
            $('#attendanceAlert').show();
            $('#attendanceTemplate').html('');
            $('#submitAttendanceBtn').hide();
            return;
        }


        console.log('section: ' + section);
        console.log('subject: ' + subject);
        console.log('batch: ' + batch);
        console.log('semester: ' + semester);
        console.log('date: ' + date);
        console.log('day: ' + day);
        console.log('timeslot: ' + timeslot);
        console.log('department: ' + department);


        var formData = new FormData();

        formData.append('classId', classId);
        formData.append('section', section);
        formData.append('department', department);
        formData.append('subjectCode', subject);
        formData.append('batch', batch);
        formData.append('semester', semester);
        formData.append('date', date);
        formData.append('day', day);
        formData.append('timeslot', timeslot);

        $('#attendanceAlert').hide();
        console.log('classSelect-att changed');


        $.ajax({
            url: '/api/app/template/attendance/studentslist',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log('response: ' + response);
                $('#attendanceTemplate').html(response);

                $('#submitAttendanceBtn').show();
            },
            error: function (response) {
                console.log('response: ' + response);
            }
        });
    });



    $('#submitAttendanceBtn').on('click', function (event) {
        console.log('submitAttendanceBtn clicked');

        // prevent default form submission
        event.preventDefault();

        var facultyId = $('#facultyId-markatt').val();

        var classId = $('#classSelect-att').val();
        var section = $('#classSelect-att option:selected').data('section');
        var subject = $('#classSelect-att option:selected').data('subject');
        var department = $('#classSelect-att option:selected').data('department');
        var batch = $('#classSelect-att option:selected').data('batch');
        var semester = $('#classSelect-att option:selected').data('semester');
        var date = $('#classSelect-att option:selected').data('date');
        var day = $('#classSelect-att option:selected').data('day');
        var timeslot = $('#classSelect-att option:selected').data('timeslot');

        console.log('classId:', classId);
        console.log('section:', section);
        console.log('subject:', subject);
        console.log('batch:', batch);
        console.log('semester:', semester);
        console.log('date:', date);
        console.log('day:', day);
        console.log('timeslot:', timeslot);

        if (!classId) {
            var alertToast = new Toast('Error', 'now', 'Please select a class to mark attendance');
            alertToast.show();
            return;
        }

        var attendanceData = [];

        $('#attendanceTemplate tbody tr').each(function () {
            var studentName = $(this).find('td:eq(2)').text().trim();
            console.log('studentName:', studentName);
            var studentId = $(this).find('td:eq(1)').text().trim();
            var status = $(this).find('input[type="radio"]:checked').val();

            if (studentId && status) {
                attendanceData.push({
                    name: studentName,
                    id: studentId,
                    status: status
                });
            }
        });

        console.log('attendanceData:', attendanceData);


        // Check the attendance data and separate the absent students and on duty students

        var absentStudents = [];
        var onDutyStudents = [];
        var prensentStudents = [];

        attendanceData.forEach(function (student) {
            if (student.status === 'absent') {
                absentStudents.push(student.id);
            } else if (student.status === 'on-duty') {
                onDutyStudents.push(student.id);
            } else if (student.status === 'present') {
                prensentStudents.push(student.id);
            }
        });

        var absentStudentsCount = absentStudents.length;
        var onDutyStudentsCount = onDutyStudents.length;
        var prensentStudentsCount = prensentStudents.length;

        var totalCount = absentStudentsCount + onDutyStudentsCount + prensentStudentsCount;

        var ConfirmationDialog = new Dialog("Attendance",
            '<div>' +
            '<p><strong>Total Students Count:</strong> ' + totalCount + '</p>' +
            '<p><strong>Present Students Count:</strong> ' + prensentStudentsCount + '</p>' +
            '<p><strong>Absent Students Count:</strong> ' + absentStudentsCount + '</p>' +
            (absentStudentsCount > 0 ?
                '<div class="table-responsive">' +
                '<table class="table table-bordered">' +
                '<thead>' +
                '<tr>' +
                '<th>S.No</th>' +
                '<th>Student Name</th>' +
                '<th>Reg No</th>' +
                '<th>Status</th>' +
                '</tr>' +
                '</thead>' +
                '<tbody>' +
                absentStudents.map(function (id, index) {
                    var student = attendanceData.find(student => student.id === id);
                    return '<tr><td>' + (index + 1) + '</td><td>' + student.name + '</td><td>' + id + '</td><td>Absent</td></tr>';
                }).join('') +
                '</tbody>' +
                '</table>' : '') +
                '</div>' +
            '<p><strong>On Duty Students Count:</strong> ' + onDutyStudentsCount + '</p>' +
            (onDutyStudentsCount > 0 ?
                '<div class="table-responsive">' +
                '<table class="table table-bordered">' +
                '<thead>' +
                '<tr>' +
                '<th>S.No</th>' +
                '<th>Student Name</th>' +
                '<th>Student ID</th>' +
                '<th>Status</th>' +
                '</tr>' +
                '</thead>' +
                '<tbody>' +
                onDutyStudents.map(function (id, index) {
                    var student = attendanceData.find(student => student.id === id);
                    return '<tr><td>' + (index + 1) + '</td><td>' + student.name + '</td><td>' + id + '</td><td>On Duty</td></tr>';
                }).join('') +
                '</tbody>' +
                '</table>' : '') +
                '</div>' +
            '</div>'
        );

        ConfirmationDialog.setButtons([
            {
                "name": "Mark Attendance",
                "class": "btn-success",
                "onClick": function (event) {
                    console.log('Marking attendance');
                    // Send the data to the API using Fetch and FormData
                    console.log('absentStudents:', absentStudents);
                    console.log('onDutyStudents:', onDutyStudents);


                    if (attendanceData.length === 0) {
                        alert('Please mark attendance for at least one student.');
                        return;
                    }

                    var formData = new FormData();
                    formData.append('department', department);
                    formData.append('facultyId', facultyId);
                    formData.append('date', date);
                    formData.append('day', day);
                    formData.append('subjectCode', subject);
                    formData.append('section', section);
                    formData.append('timeslot', timeslot);
                    formData.append('batch', batch);
                    formData.append('semester', semester);
                    formData.append('attendanceData', JSON.stringify(attendanceData)); // Convert array to JSON string

                    console.log('Form data prepared.');

                    // Uncomment this block to make the AJAX request
                    $.ajax({
                        url: '/api/app/attendance/save', // Replace with your API endpoint
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            console.log('Response:', response);

                            // Save toast data to localStorage
                            localStorage.setItem('toastData', JSON.stringify({
                                title: 'Success',
                                message: 'Attendance marked successfully',
                                type: 'success'
                            }));

                            // Reload the page
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            console.error('Response:', xhr.responseText);

                            // Print the message alone in JSON
                            const response = JSON.parse(xhr.responseText);
                            console.log(JSON.stringify({ message: response.message }));

                            // Save toast data to localStorage
                            localStorage.setItem('toastData', JSON.stringify({
                                title: 'Failed',
                                message: response.message,
                                type: 'error'
                            }));

                            // Reload the page
                            location.reload();
                        }
                    });

                    $(event.data.modal).modal('hide');
                }
            },
            {
                "name": "Cancel",
                "class": "btn-danger",
                "onClick": function (event) {
                    console.log('Attendance marking cancelled');
                    $(event.data.modal).modal('hide');
                }
            }
        ]);

        ConfirmationDialog.show();
    });
});

$(document).ready(function () {
    var toastData = localStorage.getItem('toastData');

    if (toastData) {
        toastData = JSON.parse(toastData);

        // Display the toast
        var toast = new Toast(toastData.title, 'now', toastData.message);
        toast.show();

        // Clear the toast data from localStorage
        localStorage.removeItem('toastData');
    }
});

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

/* global bootstrap: false */
(() => {
  'use strict'
  const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  tooltipTriggerList.forEach(tooltipTriggerEl => {
    new bootstrap.Tooltip(tooltipTriggerEl)
  })
})()



// // JavaScript to toggle the sidebar visibility on mobile devices
// $(document).ready(function () {
//   const sidebar = document.querySelector('.sidebar-cus');
//   const toggleButton = document.querySelector('.sidebar-expand'); // Make sure you have a button or element to toggle the sidebar

//   // Listen for click event to toggle sidebar visibility
//   toggleButton.on('click', function () {
//     sidebar.classList.toggle('visible'); // Toggle the "visible" class to expand/collapse the sidebar
//   });

//   // Close the sidebar if clicking outside of it (optional)
//   document.addEventListener('click', function (event) {
//     if (!sidebar.contains(event.target) && !toggleButton.contains(event.target)) {
//       sidebar.classList.remove('visible'); // Hide the sidebar if clicked outside
//     }
//   });
// });



// const sidebar = document.querySelector('.sidebar-cus');
// const overlay = document.querySelector('.sidebar-overlay');
// const toggleButton = document.querySelector('.btn-collapse');

// toggleButton.on('click', () => {
//   sidebar.classList.toggle('visible');
//   overlay.classList.toggle('visible');
// });

$(document).ready(function () {
  const $sidebar = $('.sidebar-cus'); // Use jQuery for consistency
  const $toggleButton = $('.sidebar-expand'); // Button to toggle the sidebar
  const $overlay = $('.sidebar-overlay'); // Optional overlay for the sidebar

  // Listen for click event to toggle sidebar visibility
  $toggleButton.click(function () {
    $sidebar.toggleClass('visible');
    $overlay.toggleClass('visible'); // Toggle overlay visibility if needed
  });

  // Close the sidebar if clicking outside of it (optional)
  $(document).click(function (event) {
    if (
      !$sidebar.is(event.target) &&
      !$sidebar.has(event.target).length &&
      !$toggleButton.is(event.target) &&
      !$toggleButton.has(event.target).length
    ) {
      $sidebar.removeClass('visible');
      $overlay.removeClass('visible'); // Hide overlay if needed
    }
  });

  // Prevent closing the sidebar when clicking inside it
  $sidebar.click(function (event) {
    event.stopPropagation();
  });

  // Prevent closing the sidebar when clicking the toggle button
  $toggleButton.click(function (event) {
    event.stopPropagation();
  });
});


$(document).ready(function () {

    console.log('removeyearincharge is ready');

    function loadyearincharges() {
        $.ajax({
            url: '/api/app/yearincharge/get/yearincharge',
            type: 'POST',
            success: function (data) {
                // console.log(data);
                var yearinchargeTableBody = $('#yearincharge-table tbody');
                yearinchargeTableBody.empty();
                for (var i = 0; i < data.yearincharges.length; i++) {
                    var yearincharge = data.yearincharges[i];
                    var tr = $('<tr></tr>');
                    tr.append('<td>' + yearincharge.faculty_id + '</td>');
                    tr.append('<td>' + yearincharge.faculty_name + '</td>');
                    tr.append('<td>' + yearincharge.department + '</td>');
                    tr.append('<td>' + yearincharge.batch + '</td>');
                    tr.append('<td><button class="btn btn-danger remove-yearincharge-btn" data-yearincharge-id="' + yearincharge.faculty_id + '">Remove</button></td>');
                    yearinchargeTableBody.append(tr);
                }
            },
            error: function (data) {
                console.log(data);
                var errorToast = new Toast('now', 'error', 'Error loading year in charges or no year in charges found');
                errorToast.show();
                $('#yearincharge-table').hide();
                $('#remove-yearincharge').empty();
                $('#remove-yearincharge').append('<p class="alert alert-warning text-center">No year in charges found</p>');
            }
        });
    }


    $(document).on('click', '.remove-yearincharge-btn', function () {
        var yearincharge_id = $(this).data('yearincharge-id');
        console.log('remove-yearincharge-btn clicked');
        console.log('yearincharge_id: ' + yearincharge_id);

        var formdata = new FormData();
        formdata.append('faculty_id', yearincharge_id);

        $.ajax({
            url: '/api/app/yearincharge/remove',
            type: 'POST',
            data: formdata,
            contentType: false,
            processData: false,
            success: function (data) {
                console.log(data);
                var successToast = new Toast('now', 'success', 'Year in charge removed successfully');
                successToast.show();
                loadyearincharges();
            },
            error: function (data) {
                console.log(data);
                var errorToast = new Toast('now', 'error', 'Error removing year in charge');
                errorToast.show();
            }
        });

        loadyearincharges();
    });

    if ($('#remove-yearincharge-container').length) {
        loadyearincharges();
    }

});
$(document).ready(function () {
    console.log('Role Permission Manage JS Loaded [updated]');

    let permissionsData = [];
    let permissionsOfRole = [];

    // Fetch all permissions via AJAX
    async function loadPermissions(query = '') {
        console.log('Loading Permissions');
        $('#permissions-container').empty(); // Clear existing UI
        permissionsData = []; // Reset data

        return $.ajax({
            url: '/api/app/permission/get/all',
            method: 'POST',
            data: { search: query },
            success: function (response) {
                permissionsData = response.map(permission => ({
                    id: permission._id.$oid,
                    name: permission.permission_name,
                    category: permission.permission_category,
                    description: permission.description
                }));
                console.log('Permissions Data Loaded:', permissionsData);
            },
            error: function (error) {
                console.error("Error fetching permissions:", error);
            }
        });
    }

    // Fetch role-specific permissions via AJAX
    async function loadRolePermissions(role) {
        console.log('Loading Role Permissions');
        permissionsOfRole = []; // Reset data

        return $.ajax({
            url: '/api/app/permission/get/by/role',
            method: 'POST',
            data: { roleId: role },
            success: function (response) {
                if (response.success && Array.isArray(response.permission)) {
                    permissionsOfRole = response.permission.map(permission => ({
                        id: permission.id,
                        name: permission.name,
                        category: permission.category,
                        description: permission.description
                    }));
                    console.log('Role Permissions Loaded:', permissionsOfRole);
                } else {
                    console.error('Invalid response structure:', response);
                }
            },
            error: function (error) {
                console.error("Error fetching role permissions:", error);
            }
        });
    }

    // Render permissions grouped by category
    function generatePermissions() {
        console.log('Generating Permissions UI');
        const selectedPermissions = permissionsOfRole.map(permission => permission.id);
        const groupedPermissions = permissionsData.reduce((acc, permission) => {
            acc[permission.category] = acc[permission.category] || [];
            acc[permission.category].push(permission);
            return acc;
        }, {});

        const container = $('#permissions-container');
        container.empty(); // Clear existing UI

        for (const category in groupedPermissions) {
            const categoryPermissions = groupedPermissions[category];
            const categoryHeader = `
                <div class="mb-3">
                    <ul class="list-unstyled">
                        <li>
                            <button type="button" class="btn w-100 text-start" data-bs-toggle="collapse" data-bs-target="#category-${category}">
                                <i class="fas fa-chevron-right" id="arrow-${category}"></i> ${category}
                            </button>
                            <div id="category-${category}" class="collapse mb-3 show">
                                <ul class="list-unstyled ms-4">
                                    ${categoryPermissions.map(permission => {
                const isChecked = selectedPermissions.includes(permission.id) ? 'checked' : '';
                return `
                                            <li>
                                                <div class="form-check">
                                                    <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" 
                                                        value="${permission.id}" id="perm-${permission.id}" ${isChecked}>
                                                    <label class="form-check-label" for="perm-${permission.id}" data-bs-toggle="tooltip" 
                                                        data-bs-placement="right" title="${permission.description}">
                                                        ${permission.name}
                                                    </label>
                                                </div>
                                            </li>
                                        `;
            }).join('')}
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>`;
            container.append(categoryHeader);
        }

        $('[data-bs-toggle="tooltip"]').tooltip(); // Reinitialize tooltips
    }

    // Handle role changes
    $('#permission-role').on('change', function () {
        const role = $(this).val();
        console.log('Role Changed to:', role);

        resetPermissionsForm(1);

        async function loadRoleAndPermissions() {
            try {
                console.log('Loading role and permissions');
                await Promise.all([loadRolePermissions(role), loadPermissions()]);
                console.log('Permissions and Role Data Loaded');
                generatePermissions();
            } catch (error) {
                console.error('Error loading role and permissions:', error);
                loadPermissions().then(() =>
                    generatePermissions()
                );
            }
        }

        loadRoleAndPermissions();
    });

    // Search functionality
    $('#role-permission-search').on('input', function () {
        const query = $(this).val().toLowerCase();
        $('#permissions-container .mb-3').each(function () {
            const category = $(this);
            const permissions = category.find('.form-check');
            let hasVisiblePermissions = false;

            permissions.each(function () {
                const label = $(this).find('label').text().toLowerCase();
                const isVisible = label.includes(query);
                $(this).toggle(isVisible);
                if (isVisible) hasVisiblePermissions = true;
            });

            if (hasVisiblePermissions) {
                category.show();
                category.find('.collapse').collapse('show');
            } else {
                category.hide();
            }
        });
    });

    // Form submission
    $('#rolePermissionForm').on('submit', function (event) {
        event.preventDefault();
        const role = $('#permission-role').val();
        const selectedPermissions = $("input[name='permissions[]']:checked").map(function () {
            return $(this).val();
        }).get();

        const formData = new FormData();
        formData.append('roleId', role);
        selectedPermissions.forEach(permission => {
            formData.append('permissionsID[]', permission);
        });

        $.ajax({
            url: '/api/app/permission/grant',
            method: 'POST',
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
                console.log('Permissions updated successfully:', response);
                var SuccessToast = new Toast('now', ' success', 'Permissions updated successfully');

                if (response.message.length > 0) {
                    var permissionNames = response.message.map(function (permission) {
                        return permission.name;
                    });

                    // Create a dialog with the permission names
                    var successDialog = new Dialog(
                        "Success Details",
                        `<h6>Current Permissions for Role: ${$('#permission-role').find('option:selected').data('name')} </h6>
                                         <ul>
                                             <li>${permissionNames.join('</li><li>')}</li>
                                         </ul>`
                    );

                    successDialog.setButtons([
                        {
                            "name": "Close",
                            "class": "btn-primary",
                            "onClick": function (event) {
                                $(event.data.modal).modal('hide');
                            }
                        }
                    ]);
                    successDialog.show();
                }

                resetPermissionsForm();
                SuccessToast.show();
            },
            error: function (error) {
                console.error('Error saving permissions:', error);
                var ErrorToast = new Toast('now', ' error', 'Error saving permissions');
                ErrorToast.show();
            }
        });
    });

    // Reset form and UI
    function resetPermissionsForm(permissionContainer = 0) {
        if (permissionContainer == 1) {
            $('#permissions-container').empty();
            permissionsData = [];
            permissionsOfRole = [];
        } else {
            $('#rolePermissionForm').trigger('reset');
            $('#permissions-container').empty();
            permissionsData = [];
            permissionsOfRole = [];
        }
    }
});

/* global bootstrap: false */
(() => {
    'use strict'
    console.log('sidebar.js loaded')
    const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.forEach(tooltipTriggerEl => {
        new bootstrap.Tooltip(tooltipTriggerEl)
        console.log('tooltipTriggerEl:', tooltipTriggerEl)
    })
})()
$(document).ready(function () {

    console.log("student Timetable JS loaded [updated]");

    if ($('.student-timetable-cont').length > 0) {

        console.log("Student Timetable page detected");

        // DOM Elements
        const timetableContainer = document.getElementById("weeklyTimetable");
        const showAllButton = document.getElementById("showAllButton");


        const student_id = document.getElementById("student_id").value;

        // Fetch timetable data from API
        $.ajax({
            url: "/api/app/get/tt/studenttimetable",
            type: "POST",
            data: {
                student_id: student_id
            },
            success: function (response) {
                if (response.success) {
                    const timetableData = response.timetable;
                    populateTimetable(timetableData);
                } else {
                    console.error("Failed to fetch timetable data");
                }
            },
            error: function (xhr, status, error) {
                console.error("Error occurred while fetching timetable:", error);
            }
        });

        // Populate timetable
        function populateTimetable(timetableData) {
            const daysOrder = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            const today = new Date().toLocaleString("en-US", {
                weekday: "long"
            });

            daysOrder.forEach((day, index) => {
                const dayClasses = timetableData[day] || [];
                let classDetails = "";

                if (dayClasses.length > 0) {
                    dayClasses.forEach((slot) => {
                        classDetails += `
                    <div class="class-details mb-2 bg-dark-light">
                        <p><strong>${slot.subject_code} - ${slot.section}</strong></p>
                        <p><em>${slot.faculty}</em></p>
                        <p>${slot.time} | ${slot.class}</p>
                    </div>
                `;
                    });
                } else {
                    classDetails = `<p class="no-classes">No classes scheduled</p>`;
                }

                timetableContainer.innerHTML += `
            <div class="col mt-2" data-order="${index}">
                <div class="card day-card shadow-sm ${day === today ? "current-day" : ""}" data-day="${day}">
                    <div class="day-header">
                        <i class="icon fa-solid fa-calendar-day"></i>
                        <div>${day}</div>
                    </div>
                    <div class="card-body">
                        ${classDetails}
                    </div>
                </div>
            </div>
        `;
            });

            // Sort and reinitialize Masonry
            const elements = Array.from(timetableContainer.children);
            elements.sort((a, b) => a.getAttribute("data-order") - b.getAttribute("data-order"));
            elements.forEach((element) => timetableContainer.appendChild(element));

            const msnry = new Masonry(timetableContainer, {
                itemSelector: ".col",
                columnWidth: ".col",
                percentPosition: true
            });

            // Initially blur other days
            toggleShowAll(msnry);
        }

        // Toggle between showing all days and only the current day
        function toggleShowAll(msnry) {
            const dayCards = document.querySelectorAll(".day-card");
            const isShowingAll = showAllButton.textContent === "Show All";

            dayCards.forEach((card) => {
                if (!card.classList.contains("current-day")) {
                    if (isShowingAll) {
                        card.classList.remove("blurred");
                    } else {
                        card.classList.add("blurred");
                    }
                }
            });

            showAllButton.textContent = isShowingAll ? "Show Only Today" : "Show All";
            msnry.layout();
        }

        window.toggleShowAll = toggleShowAll;

    }
});
// Detailed View Pagination
$(document).ready(function () {

    console.log('summaryattstudent.js loaded');

    if ($('#summary-attendance').length > 0) {

        const rowsPerPage = 5;

        document.querySelectorAll('tbody[id^="detailsBody-"]').forEach(body => {
            const rows = Array.from(body.children);
            const subjectCode = body.id.split('-')[1];
            const pagination = document.getElementById(`pagination-${subjectCode}`);

            function renderTable(page) {
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                rows.forEach((row, index) => {
                    row.style.display = index >= start && index < end ? '' : 'none';
                });
            }

            function renderPagination() {
                const totalPages = Math.ceil(rows.length / rowsPerPage);
                pagination.innerHTML = '';
                for (let i = 1; i <= totalPages; i++) {
                    const li = document.createElement('li');
                    li.className = 'page-item';
                    li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                    li.addEventListener('click', () => {
                        renderTable(i);
                        pagination.querySelectorAll('.page-item').forEach(el => el.classList.remove('active'));
                        li.classList.add('active');
                    });
                    pagination.appendChild(li);
                }
                if (pagination.firstChild) pagination.firstChild.classList.add('active');
                renderTable(1);
            }

            renderPagination();
        });

        // Bar Chart Data
        const barChartData = {
            labels: JSON.parse($('#summ-subjects').val()),
            datasets: [{
                label: 'Attendance Percentage',
                data: JSON.parse($('#summ-percentages').val()),
                backgroundColor: '#4e73df',
                borderColor: '#375a7f',
                borderWidth: 1
            }]
        };

        // Bar Chart Config
        const barChartConfig = {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Subjects'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Attendance (%)'
                        },
                        max: 100
                    }
                }
            }
        };

        // Pie Chart Data
        const pieChartData = {
            labels: JSON.parse($('#summ-subjects').val()),
            datasets: [{
                label: 'Subject Contribution',
                data: JSON.parse($('#summ-percentages').val()),
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69']
            }]
        };

        // Pie Chart Config
        const pieChartConfig = {
            type: 'pie',
            data: pieChartData,
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: ctx => `${ctx.raw}%`
                        }
                    }
                }
            }
        };

        // Render Charts
        new Chart(document.getElementById('attendanceBarChart'), barChartConfig);
        new Chart(document.getElementById('attendancePieChart'), pieChartConfig);
    }
});
// $(document).ready(function () {
//     console.log('Role Permission Manage JS Loaded [updated]');
//     let permissionsData = [];
//     let permissionsOfRole = [];

//     // Fetch all permissions via AJAX and load them
//     function loadPermissions(callback) {
//         console.log('Loading Permissions');

//         //remove the permissions Container
//         $('#permissions-container').empty();

//         //unset the permissionsData
//         permissionsData = [];

//         $.ajax({
//             url: '/api/app/permission/get/all',
//             method: 'POST',
//             data: { search: query },
//             success: function (response) {
//                 // Process the response and group permissions by category
//                 permissionsData = response.map(permission => ({
//                     id: permission._id.$oid,
//                     name: permission.permission_name,
//                     category: permission.permission_category,
//                     description: permission.description
//                 }));

//                 // Call the callback function after loading permissions
//                 if (callback) {
//                     callback();
//                 }
//             },
//             error: function (error) {
//                 console.error("Error fetching permissions:", error);
//             }
//         });
//     }

//     function loadRolePermissions(role) {
//         $.ajax({
//             url: '/api/app/permission/get/by/role',
//             method: 'POST',
//             data: { roleId: role },
//             success: function (response) {
//                 // Check if the response indicates success and contains permissions
//                 if (response.success && Array.isArray(response.permission)) {
//                     console.log('Raw Response:', response);

//                     // Process the permissions array
//                     const newPermissions = response.permission.map(permission => ({
//                         id: permission.id,
//                         name: permission.name,
//                         category: permission.category,
//                         description: permission.description
//                     }));

//                     // Load all permissions after receiving role permissions
//                     loadPermissions(function() {
//                         // Merge new permissions with existing permissionsOfRole
//                         permissionsOfRole = [...permissionsOfRole, ...newPermissions]; 
//                         generatePermissions();
//                     });

//                 } else {
//                     console.error('Invalid response structure:', response);
//                 }
//             },
//             error: function (error) {
//                 // Handle errors

//                 switch (error.status) {
//                     case 404:
//                         new Toast("Information", "info", "No Permissions Found for the Role").show();
//                         break;
            
//                     case 500:
//                         new Toast("Error", "error", "Server Error. Please try again later.").show();
//                         break;
            
//                     default:
//                         const errorMessage = error.responseJSON && error.responseJSON.message 
//                             ? error.responseJSON.message 
//                             : "An unexpected error occurred. Please check your connection or try again.";
//                         new Toast("Error", "error", errorMessage).show();
//                         break;
//                 }
//             }
//         });
//     }

//     // Function to group permissions by category and render them
//     function generatePermissions() {
//         // Assuming `permissionsOfRole` contains an array of permission IDs
//         const selectedPermissions = permissionsOfRole.map(permission => permission.id);

//         // Group permissions by category
//         const groupedPermissions = permissionsData.reduce((acc, permission) => {
//             if (!acc[permission.category]) acc[permission.category] = [];
//             acc[permission.category].push(permission);
//             return acc;
//         }, {});

//         const container = $('#permissions-container');
//         container.empty(); // Clear the container

//         // Loop through grouped permissions
//         for (const category in groupedPermissions) {
//             const categoryGroup = groupedPermissions[category];

//             // Create category header with nested permissions
//             const categoryHeader = `
//                 <div class="mb-3">
//                     <ul class="list-unstyled">
//                         <li>
//                             <button type="button" class="btn w-100 text-start" data-bs-toggle="collapse" data-bs-target="#category-${category}">
//                                 <i class="fas fa-chevron-right" id="arrow-${category}"></i> ${category}
//                             </button>
                            
//                             <div id="category-${category}" class="collapse mb-3 show">
//                                 <ul class="list-unstyled ms-4">
//                                     ${categoryGroup.map(permission => {
//                 const isChecked = selectedPermissions.includes(permission.id) ? 'checked' : '';
//                 return `
//                                             <li>
//                                                 <div class="form-check">
//                                                     <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" 
//                                                         value="${permission.id}" id="perm-${permission.id}" ${isChecked}>
//                                                     <label class="form-check-label" for="perm-${permission.id}" data-bs-toggle="tooltip" 
//                                                         data-bs-placement="right" title="${permission.description}">
//                                                         ${permission.name}
//                                                     </label>
//                                                 </div>
//                                             </li>
//                                         `;
//             }).join('')}
//                                 </ul>
//                             </div>
//                         </li>
//                     </ul>
//                 </div>
//             `;

//             container.append(categoryHeader);
//         }

//         // Reinitialize tooltips for dynamically added elements
//         $('[data-bs-toggle="tooltip"]').tooltip();

//         container.on('click', '.select-all', function () {
//             const category = $(this).data('category');
//             const target = $(`#category-${category}`);
//             target.collapse('show'); // Expand the category
//             target.find('.permission-checkbox').prop('checked', true);
//         });

//         container.on('click', '.deselect-all', function () {
//             const category = $(this).data('category');
//             const target = $(`#category-${category}`);
//             target.find('.permission-checkbox').prop('checked', false);
//         });

//         // Handle arrow icon direction on collapse
//         container.on('show.bs.collapse', function (event) {
//             const category = event.target.id.replace('category-', '');
//             $(`#arrow-${category}`).removeClass('fa-chevron-right').addClass('fa-chevron-down');
//         });

//         container.on('hide.bs.collapse', function (event) {
//             const category = event.target.id.replace('category-', '');
//             $(`#arrow-${category}`).removeClass('fa-chevron-down').addClass('fa-chevron-right');
//         });
//     }

//     // Search functionality
//     $('#role-permission-search').on('input', function () {
//         const query = $(this).val().toLowerCase();

//         // Iterate through each category
//         $('#permissions-container .mb-3').each(function () {
//             const category = $(this);
//             const permissions = category.find('.form-check');

//             // Check visibility of each permission
//             let hasVisiblePermissions = false;
//             permissions.each(function () {
//                 const label = $(this).find('label').text().toLowerCase();
//                 const isVisible = label.includes(query);
//                 $(this).toggle(isVisible); // Show or hide the permission
//                 if (isVisible) hasVisiblePermissions = true; // Mark if at least one permission is visible
//             });

//             // Show or hide the entire category based on its permissions
//             if (hasVisiblePermissions) {
//                 category.show(); // Show category
//                 category.find('.collapse').collapse('show'); // Expand category
//             } else {
//                 category.hide(); // Hide category
//             }
//         });
//     });

//     // Submit form handler
//      // Submit form handler
//      $('#rolePermissionForm').on('submit', function (event) {
//         event.preventDefault();

//         const role = $('#permission-role').val();

//         console.log(role);

//         // Retrieve all selected permissions
//         let selectedPermissions = $("input[name='permissions[]']:checked").map(function () {
//             return $(this).val(); // Get the value of the checked checkbox
//         }).get();

//         if (selectedPermissions.length === 0) {
//             selectedPermissions = [];
//         }

//         // Log or use the selected permissions
//         console.log("Selected Permissions:", selectedPermissions);

//         // Create FormData object
//         const formData = new FormData();
//         formData.append('roleId', role);

//         selectedPermissions.forEach(permission => {
//             formData.append('permissionsID[]', permission); // Use 'permissions[]' to match array structure
//         });

//         console.log(formData);

//         $.ajax({
//             url: '/api/app/permission/grant',
//             method: 'POST',
//             processData: false, // Prevent jQuery from transforming the data
//             contentType: false, // Allow FormData to set the correct Content-Type
//             data: formData,
//             success: function (response) {
//                 console.log("Permission mapping saved successfully:", response);
//                 // Display response JSON in a dialog

//                 if (response.message.length == 0) {
//                     var successDialog = new Dialog("Success Details", "Permission Removed"); // Pretty print JSON
//                     successDialog.setButtons([
//                         {
//                             "name": "Close",
//                             "class": "btn-primary",
//                             "onClick": function (event) {
//                                 $(event.data.modal).modal('hide');
//                             }
//                         }
//                     ]);
//                     successDialog.show();

//                     resetPermissionsForm();

//                     var successToast = new Toast("Now", "success", "Permissions removed successfully.");
//                     successToast.show();
//                     return;
//                 }


//                 var permissionNames = response.message.map(function (permission) {
//                     return permission.name;
//                 });

//                 // Create a dialog with the permission names
//                 var successDialog = new Dialog(
//                     "Success Details",
//                     `<h6>Current Permissions</h6>
//                      <ul>
//                          <li>${permissionNames.join('</li><li>')}</li>
//                      </ul>`
//                 );

//                 successDialog.setButtons([
//                     {
//                         "name": "Close",
//                         "class": "btn-primary",
//                         "onClick": function (event) {
//                             $(event.data.modal).modal('hide');
//                         }
//                     }
//                 ]);
//                 successDialog.show();

//                 resetPermissionsForm();

//                 // Show success toast
//                 var successToast = new Toast("Now", "success", "Permissions granted successfully.");
//                 successToast.show();
//             },
//             error: function (error) {
//                 console.error("Error saving mapping:", error);

//                 // Display error JSON in a dialog
//                 var errorDialog = new Dialog("Error Details", JSON.stringify(error.responseJSON || error, null, 2)); // Pretty print JSON
//                 errorDialog.setButtons([
//                     {
//                         "name": "Close",
//                         "class": "btn-primary",
//                         "onClick": function (event) {
//                             $(event.data.modal).modal('hide');
//                         }
//                     }
//                 ]);
//                 errorDialog.show();

//                 resetPermissionsForm()

//                 // Show error toast
//                 var errorToast = new Toast("Error", "error", "Error saving permission mapping.");
//                 errorToast.show();
//             }
//         });
//     });

//     // Load permissions on page load
//     $('#permission-role').on('change', function () {

//         console.log('Role Changed');

//         const role = $(this).val();
//         console.log('Role Changed to :' + role);

//         //remove the permissions Container
//         $('#permissions-container').empty();

//         // Load permissions for the selected role first
//         loadRolePermissions(role);
//     });

//     // Reset permissions form
//     function resetPermissionsForm() {
//         $('#rolePermissionForm').trigger('reset');

//         //remove the permissions Container
//         $('#permissions-container').empty();

//         //unset the permissionsOfRole
//         permissionsOfRole = [];
//         //unset the permissionsData
//         permissionsData = [];
//     }

// });
// Initialize Bootstrap popover for the kural-container
$(document).ready(function () {
    $('#kural-container').popover({
        trigger: 'hover',
        placement: 'top',
    });
});

// Fetch and update the Thirukkural dynamically
function fetchNewKural() {
    fetch('/api/app/addons/thirukkural')
        .then(response => response.json())
        .then(data => {
            // Update the lines inside the kural-container
            $('#line1').text(data.Line1);
            $('#line2').text(data.Line2);

            // Update popover content dynamically
            $('#kural-container').attr('data-bs-content', `
                <div class="thil-explanation">
                    <div class="text-center mb-2">
                        <strong class="text-primary">திருக்குறள்</strong>
                    </div>
                    <div class="mb-2">
                        <strong>குறள் எண்:</strong> ${data.Number}
                    </div>
                    <div class="mb-2">
                        <strong>அதிகாரம்:</strong> ${data.adikaram_name}
                    </div>
                    <div class="mb-2">
                        <strong>குறள்:</strong><br> ${data.Line1}<br> ${data.Line2}
                    </div>
                    <div class="mb-2">
                        <strong>குறள் விளக்கம்:</strong><br> ${data.mk}.
                    </div>
                    <div class="mb-2">
                        <strong>Translation:</strong><br> ${data.Translation}.
                    </div>
                    <div>
                        <strong>Explanation:</strong><br> ${data.explanation}.
                    </div>
                </div>
            `);

            // Reinitialize popover after updating content
            $('#kural-container').popover('dispose').popover({
                trigger: 'hover',
                placement: 'bottom',
            });
        })
        .catch(error => console.error('Error fetching new Kural:', error));
}

// Fetch a new Thirukkural immediately and every minute
fetchNewKural();
// Detect hover over the kural-container
let isHovered = false;

const kuralContainer = document.getElementById('kural-container');
kuralContainer.addEventListener('mouseenter', () => {
    isHovered = true;
});

kuralContainer.addEventListener('mouseleave', () => {
    isHovered = false;
});

// Fetch new Thirukkural only when not hovered
setInterval(() => {
    if (!isHovered) {
        fetchNewKural();
    }
}, 60000); // 60000 ms = 1 minute
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
            url: '/api/app/get/tt/facultysections',
            data: { faculty_id: faculty_id, subject_code: subject_code, batch: batch, semester: semester },
            type: 'POST',
            success: function (data) {

            console.log(data);

            var classToast = new Toast("now", "success", "Class Loaded");
            classToast.show();

            $('#tt-section').val(data.Sections[0]);
            var sectionSelect = $('#tt-section');
            sectionSelect.empty();

            sectionSelect.append($('<option>', {
                value: '',
                text: 'Select the Section'
            }));

            $.each(data.Sections, function (_, section) {
                sectionSelect.append($('<option>', {
                value: section,
                text: section
                }));
            });

            },
            error: function (xhr, error) {
            var errorMessage = xhr.responseJSON ? xhr.responseJSON.message : "No Class Found. Please Assign the Subject to the Class";
            var classToast = new Toast("now", "error", errorMessage);
            classToast.show();
            console.error("Error:", errorMessage);
            }
        });

    });


    $('#tt-section').on('change', function () {

        var subject_code = $('#tt-subject_code').val();
        var batch = $('#tt-batch').val();
        var semester = $('#tt-semester').val();
        var faculty_id = $('#tt-faculty').val();
        var section = $(this).val();
        var department = $('#tt-department').val();

        var formData = new FormData();

        formData.append('faculty_id', faculty_id);
        formData.append('department', department);
        formData.append('subject_code', subject_code);
        formData.append('batch', batch);
        formData.append('semester', semester);
        formData.append('section', section);

        $.ajax({
            url: '/api/app/get/tt/classid',
            data: formData,
            type: 'POST',
            processData: false,
            contentType: false,
            success: function (data) {
            console.log(data);

            var classToast = new Toast("now", "success", "Class ID Loaded");
            classToast.show();

            $('#tt-class_id').val(data.class_id);
            },
            error: function (xhr, error) {
            // Error feedback
            const errorMessage = xhr.responseJSON ? xhr.responseJSON.message : "Error Loading Class ID";
            const classToast = new Toast("now", "error", errorMessage);
            classToast.show();
            console.error("Error:", error);
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

$(document).ready(function () {
    console.log('User Role Manage JS Loaded');

    $('#user-role-category').on('change', function () {
        console.log('Category Changed:', $(this).val());
        resetUserRoleForm();
    });

    // Handle user selection
    $('#role-fetch-user').on('click', function () {
        console.log("User Role Fetch Button Clicked");

        $('#user-info').html('');
        $('#roles-container').html('');

        //removeSubmitButton();

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
                                <div class="col-md-12">
                                    <p><strong>Name:</strong> <em>${userDetails.name}</em></p>
                                    <p><strong>Email:</strong> <em>${userDetails.email}</em></p>
                                    <p><strong>Department:</strong> <em>${userDetails.department || 'N/A'}</em></p>
                                    <p><strong>Designation:</strong> <em>${userDetails.designation || 'N/A'}</em></p>
                                    <p><strong>Role:</strong> <em>${userDetails.role || 'N/A'}</em></p>
                                </div>
                    `;

                    const displayHtml = `
                <strong>Name: </strong> ${userDetails.name}</em>, 
                <strong>Department: </strong> ${userDetails.department || 'N/A'}</em>, 
                <strong>Role: </strong> ${userDetails.role || 'N/A'}</em>
                `;

                    var userDialog = new Dialog("User Details", userInfoHtml);
                    userDialog.setButtons([
                        {
                            name: "Confirm",
                            class: "btn-success",
                            onClick: function (event) {

                                $('#user-info').html(displayHtml);

                                loadRoles(category, userId);

                                enableSubmitButton();

                                $(event.data.modal).modal('hide');
                            }

                        },
                        {
                            name: "Cancel",
                            class: "btn-secondary",
                            onClick: function (event) {
                                resetUserRoleForm();
                                $(event.data.modal).modal('hide');
                            }
                        }
                    ]);

                    userDialog.show();
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

                $('[data-bs-toggle="tooltip"]').tooltip(); // Enable tooltips

                console.log('Roles Loaded');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Error fetching roles:', errorThrown);
                showToast("Error", "Failed to fetch roles.");
            }
        });
    }

    $('#user-role-submit').on('click', function (event) {
        // Prevent form submission
        // event.preventDefault();

        console.log("User Role Submit Button Clicked");

        const category = $('#user-role-category').val();
        const userId = $('#role-user-id').val();

        // Validate category
        if (!category) {
            console.error('Please select a category first.');
            return;
        }

        // Validate user ID
        if (!userId) {
            console.error('User ID/Registration No cannot be empty.');
            return;
        }

        // Collect selected roles
        var selectedRoles = $("input[name='role[]']:checked").map(function () {
            return $(this).val();
        }).get();

        // if no roles selected, log an error and set an empty array
        if (selectedRoles.length === 0) {
            console.error('No roles selected.');
            selectedRoles = [];
        }

        console.log('Selected Category:', category);
        console.log('Selected User ID:', userId);
        console.log('Selected Roles:', selectedRoles);

        // Submit role assignment (implement AJAX or other submission logic here)
        const formData = new FormData();
        formData.append('category', category);
        formData.append('user_id', userId);

        selectedRoles.forEach(role => {
            formData.append('roles_id[]', role);
        });

        console.log('Submitting form data:', formData);

        $.ajax({
            url: '/api/app/role/set', // Replace with your API endpoint
            method: 'POST',
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {
                // Handle empty roles case
                if (!response || !response.message || response.message.length === 0) {
                    showToast('Success', 'No roles assigned');
                    return;
                }

                console.log('Roles assigned successfully:', response);
                showToast("Success", "Roles assigned successfully!");

                // Ensure response.message is an array
                if (Array.isArray(response.result) && response.result.length > 0) {
                    var roleNames = response.result.map(function (role) {
                        return role.name;
                    });

                    // Generate dialog with assigned roles
                    var successDialog = new Dialog(
                        "Success",
                        `<h6>Current Roles for User: ${$('#role-user-id').val()} </h6>
                        <ul>
                            ${roleNames.map(role => `<li>${role}</li>`).join('')}
                        </ul>`
                    );

                    // Set dialog buttons
                    successDialog.setButtons([
                        {
                            name: "Close",
                            class: "btn-info",
                            onClick: function (event) {
                                $(event.data.modal).modal('hide');
                            }
                        }
                    ]);

                    successDialog.show();

                    // refresh the roles
                    loadRoles(category, userId);


                } else {
                    // No roles to display
                    var noRolesDialog = new Dialog(
                        "Success",
                        `<h6>Current Roles for User: ${$('#role-user-id').val()} </h6>
                        <p>No Roles Assigned</p>`
                    );

                    noRolesDialog.setButtons([
                        {
                            name: "Close",
                            class: "btn-secondary",
                            onClick: function (event) {
                                $(event.data.modal).modal('hide');

                            }
                        }
                    ]);

                    noRolesDialog.show();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Error assigning roles:', errorThrown);
                showToast("Error", "Failed to assign roles. Please try again.");
            }
        });
    });

    function enableSubmitButton() {
        console.log('Enabling Submit Button');
        //change d-none class to d-block
        $('#user-role-submit').removeClass('d-none');
        $('#user-role-submit').addClass('d-block');
    }

    function removeSubmitButton() {
        console.log('Removing Submit Button');
        $('#user-role-submit').removeClass('d-block');
        $('#user-role-submit').addClass('d-none');
    }

    // Reusable Toast Function
    function showToast(title, message) {
        const toast = new Toast("now", title, message);
        toast.show();
    }

    function resetUserRoleForm() {
        $('#role-user-id').val('');
        $('#user-info').html('');
        $('#roles-container').html('');
        removeSubmitButton();
    }

});

$(document).ready(function () {

    console.log("viewattendance.js loaded edited");

    const sessionSelect = $('#session-select');
    let attendanceData = []; // To store the entire API response for use

    let classId = '';

    $(document).on('click', '#viewattendancebtn', function () {

        // $('#attendance-view').show();

        console.log('viewattendance button clicked');
        classId = $(this).data('class-id');
        console.log('Class ID:', classId);

        // $('#att-cont-viewatt').show(); // Clear the container

        var faculty_id = $('#facultyId-view-atten').val();

        var getDetailsDialog = new Dialog('Choose the Date to view or edit the attendance', `
            <div class="form-group">
            <label for="attendanceDate">Choose Date:</label>
            <input type="date" id="attendanceDate" class="form-control" max="${new Date().toISOString().split('T')[0]}" min="2025-01-01">
            <p id="Day"></p>
            </div>
        `);

        // Update the selected day when a date is chosen
        $(document).on('change', '#attendanceDate', function () {
            const date = new Date($(this).val());
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const dayName = days[date.getDay()];
            $('#selectedDay').text('Selected Day: ' + dayName);
        });


        getDetailsDialog.setButtons([
            {
                'name': 'View Attendance',
                'class': 'btn-success',
                'onClick': function (event) {


                    sessionSelect.empty(); // Clear the session dropdown
                    $('#attendance-table').empty(); // Clear the attendance table


                    var date = $('#attendanceDate').val();
                    // var timeSlot = $('#timeSlot').val();

                    console.log('Date:', date);
                    // console.log('Time Slot:', timeSlot);

                    var formData = new FormData();

                    formData.append('faculty_id', faculty_id);
                    formData.append('class_id', classId);
                    formData.append('date', date);


                    // Fetch the attendance data from the API
                    $.ajax({
                        url: '/api/app/attendance/get/attendance',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            console.log('Attendance Data:', response);
                            // $('#att-cont-viewatt').html(''); // Clear the container
                            // $('#att-cont-viewatt').append(response);

                            var SuccessToast = new Toast('Success', 'now', 'Attendance Fetched Successfully');
                            SuccessToast.show();

                            attendanceData = response.message;

                            $("#attendance-view").show();

                            // Populate the session dropdown
                            attendanceData.forEach(session => {
                                sessionSelect.append(new Option(
                                    `${session.session.day} ${session.session.timeslot}`,
                                    session.session._id
                                ));
                            });

                            // Render attendance for the first session by default
                            if (attendanceData.length > 0) {
                                renderAttendance(attendanceData[0].session._id);
                            }


                        },
                        error: function (xhr, status, error) {
                            console.error('Error:', error);
                            console.error('Response:', xhr.responseText);

                            // Print the message alone in JSON
                            const response = JSON.parse(xhr.responseText);
                            console.log(JSON.stringify({ message: response.message }));

                            // Save toast data to localStorage Showing this toast is defined in markattendance.js
                            localStorage.setItem('toastData', JSON.stringify({
                                title: 'Failed',
                                message: response.message,
                                type: 'error'
                            }));

                            // Reload the page
                            location.reload();
                        }
                    });

                    $(event.data.modal).modal('hide');
                }
            }, {

                'name': 'Cancel',
                'class': 'btn-secondary',
                'onClick': function (event) {
                    var t = new Toast('Cancelled', 'now', 'View Attendance Cancelled');
                    t.show();

                    $(event.data.modal).modal('hide');
                    location.reload();
                }
            }

        ]);

        getDetailsDialog.show();
    });


    function renderAttendance(sessionId) {
        const sessionData = attendanceData.find(session => session.session._id === sessionId);
        if (!sessionData) return;

        const tbody = $('#attendance-table');
        tbody.empty(); // Clear existing rows

        var count = 1;
        sessionData.attendance.forEach(student => {
            tbody.append(`
                    <tr data-student-id="${student.student_id}">
                        <td>${count++}</td>
                        <td>${student.student_id}</td>
                        <td>${student.student_name}</td>
                        <td>
                            <span class="read-only">${student.status}</span>
                            <select class="edit-mode form-control" style="display:none;">
                                <option value="present" ${student.status === 'present' ? 'selected' : ''}>Present</option>
                                <option value="absent" ${student.status === 'absent' ? 'selected' : ''}>Absent</option>
                                <option value="on-duty" ${student.status === 'on-duty' ? 'selected' : ''}>On-Duty</option>
                            </select>
                        </td>
                    </tr>
                `);
        });

        // Reset action buttons
        $('#edit-all').show();
        $('#save-all').hide();
    }

    // Handle session selection change
    sessionSelect.change(function () {
        const sessionId = this.value;
        renderAttendance(sessionId);
    });

    // Handle "Edit All" button click
    $('#edit-all').click(function () {
        $('.read-only').hide();
        $('.edit-mode').show();

        $(this).hide();
        $('#save-all').show();
    });

    // Handle "Save All" button click
    $('#save-all').click(function () {
        const sessionId = sessionSelect.val();
        const updates = [];

        // $('#attendance-table tr').each(function () {
        //     const studentId = $(this).data('student-id');
        //     //const studentId = row.dataset.studentId;
        //     const status = $(this).find('.edit-mode').val();

        //     updates.push({ id: studentId, status: status });
        // });

        document.querySelectorAll('#attendance-table tr').forEach(row => {
            const studentId = row.dataset.studentId;
            const status = row.querySelector('.edit-mode').value;

            updates.push({
                id: studentId,
                status: status
            });
        });



        var formData = new FormData();
        formData.append('sessionId', sessionId);
        formData.append('attendanceData', JSON.stringify(updates));
        formData.append('classId', classId);
        formData.append('edit', true);

        console.log('Session ID:', sessionId);
        console.log('Updates:', updates);
        console.log('Class ID:', classId);


        $.ajax({
            url: '/api/app/attendance/saveedit', // Replace with your API endpoint
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function () {

                var SuccessToast = new Toast('Success', 'now', 'Attendance Saved Successfully');
                SuccessToast.show();

                // Save toast data to localStorage
                localStorage.setItem('toastData', JSON.stringify({
                    title: 'Success',
                    message: 'Attendance Saved Successfully.',
                    type: 'success'
                }));

                // Reload the page
                location.reload();
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);

                // Print the message alone in JSON
                const response = JSON.parse(xhr.responseText);
                console.log(JSON.stringify({ message: response.message }));

                // Save toast data to localStorage
                localStorage.setItem('toastData', JSON.stringify({
                    title: 'Failed',
                    message: response.message,
                    type: 'error'
                }));

                // Reload the page
                // location.reload();
            }
        });
    });

});
$(document).ready(function () {
    console.log("viewstudentdetails-tutor.js loaded");

    $('.view-student-detail-tutor').on('click', function () {
        var studentId = $(this).data('student_id');
        console.log("Student ID: " + studentId);

        $.ajax({
            url: '/api/app/student/details',
            type: 'POST',
            data: {
                student_id: studentId
            },
            success: function (response) {
                var studentDetails = response.studentDetails;
                var enrolledClasses = response.enrolledClasses;

                var detailsHtml = `
    <div class="container">
        <h3 class="mb-3 text-primary">Student Details</h3>
        <div class="row">
            <div class="col-md-6">
                <p><strong>Name:</strong> ${studentDetails.name}</p>
                <p><strong>Registration No:</strong> ${studentDetails.reg_no}</p>
                <p><strong>Email:</strong> ${studentDetails.email}</p>
                <p><strong>Roll No:</strong> ${studentDetails.roll_no}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Semester:</strong> ${studentDetails.semester}</p>
                <p><strong>Section:</strong> ${studentDetails.section}</p>
            </div>
        </div>

        <h3 class="mt-4 mb-3 text-primary">Enrolled Classes</h3>
        <div class="list-group">
`;

                enrolledClasses.forEach(function (classInfo) {
                    detailsHtml += `
        <div class="list-group-item">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Faculty:</strong> ${classInfo.faculty}</p>
                    <p><strong>Department:</strong> ${classInfo.department}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Subject Code:</strong> ${classInfo.subject_code}</p>
                    <p><strong>Semester:</strong> ${classInfo.semester}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Section:</strong> ${classInfo.section}</p>
                     <p><strong>Batch:</strong> ${classInfo.batch}</p>
                </div>
            </div>
        </div>
    `;
                });

                detailsHtml += `</div></div>`;


                var d = new Dialog("Student Details ("+ studentId + ")" , detailsHtml, {'size': 'large'});
                d.show();
            }
        });


    });
});

//# sourceMappingURL=app.js.map