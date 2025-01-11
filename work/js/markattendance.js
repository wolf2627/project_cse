
$('#markAttendance-container').ready(function () {
    console.log('markAttendance-container ready');

    $('#submitAttendanceBtn').hide();

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
            url: '/api/app/template/attendance/students',
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
            alert('Please select a slot.');
            return;
        }

        var attendanceData = [];

        $('#attendanceTemplate tbody tr').each(function () {
            var studentId = $(this).find('td:eq(1)').text().trim();
            var status = $(this).find('input[type="radio"]:checked').val();

            if (studentId && status) {
                attendanceData.push({
                    id: studentId,
                    status: status
                });
            }
        });

        console.log('attendanceData:', attendanceData);

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
                $('#attendanceAlert').show();
                $('#attendanceAlert').html('<strong>Success:</strong> Attendance marked successfully!');
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);
                $('#attendanceAlert').show();
                $('#attendanceAlert').html('<strong>Error:</strong> Unable to mark attendance. Please try again.');
            }
        });

    });



});