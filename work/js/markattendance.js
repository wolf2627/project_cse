
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
