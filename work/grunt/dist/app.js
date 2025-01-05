/* Processed on 5/1/2025 @ 3:44:6 */
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
    var fileInput = $('#formFile')[0];
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
                    success: function(response) {
                        console.log('User created successfully:', response);

                        // Do something after the user is created, like refreshing the user list or showing a success message
                        t = new Toast('Success', 'now', response.successCount + ' Users created successfully');
                        t.show();
                        // alert("User created successfully!");
                    },
                    error: function(error) {
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

//# sourceMappingURL=app.js.map