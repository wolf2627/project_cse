$(document).ready(function () {

    console.log("viewattendance.js loaded");

    $(document).on('click', '#viewattendancebtn', function () {

        console.log('viewattendance button clicked');
        var classId = $(this).data('class-id');
        console.log('Class ID:', classId);

        $('#att-cont-viewatt').html(''); // Clear the container

        var faculty_id = $('#facultyId-view-atten').val();

        var getDetailsDialog = new Dialog('Choose the Date to view or edit the attendance', `
            <div class="form-group">
            <label for="attendanceDate">Choose Date:</label>
            <input type="date" id="attendanceDate" class="form-control" max="${new Date().toISOString().split('T')[0]}">
            <p id="Day"></p>
            </div>
        `);

        // Event listener to update the day when a date is selected
        $(document).on('change', '#attendanceDate', function () {
            var date = new Date($(this).val());
            var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            var dayName = days[date.getDay()];
            $('#selectedDay').text('Selected Day: ' + dayName);
        });


        getDetailsDialog.setButtons([
            {
                'name': 'View Attendance',
                'class': 'btn-success',
                'onClick': function (event) {
                    var date = $('#attendanceDate').val();
                    var timeSlot = $('#timeSlot').val();

                    console.log('Date:', date);
                    console.log('Time Slot:', timeSlot);

                    // Fetch the attendance data from the API
                    $.ajax({
                        url: '/api/app/template/attendance/markedlist',
                        type: 'GET',
                        data: {
                            'faculty_id': faculty_id,
                            'class_id': classId,
                            'date': date,
                        },
                        success: function (response) {
                            console.log('Attendance Data:', response);
                            $('#att-cont-viewatt').html(''); // Clear the container
                            $('#att-cont-viewatt').append(response);

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

});