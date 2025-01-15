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