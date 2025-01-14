<?php

${basename(__FILE__, '.php')} = function () {

    if ($this->paramsExists('faculty_id', 'class_id', 'date')) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $faculty_id = $this->_request['faculty_id'];
        $class_id = $this->_request['class_id'];
        $date = $this->_request['date'];

        try {
            $att = new Attendance();
            $data = $att->getMarkedFacultyAttendance($faculty_id, $class_id, $date); ?>
            
            <div>
                <!-- Session Selection Dropdown -->
                <label for="session-select">Select Session:</label>
                <select id="session-select" class="form-control">
                    <?php foreach ($data as $session) { ?>
                        <option value="<?= $session['session']['_id'] ?>">
                            <?= $session['session']['day'] . ' ' . $session['session']['timeslot'] ?>
                        </option>
                    <?php } ?>
                </select>

                <!-- Action Buttons -->
                <div class="mt-3 bd-grid gap-2 d-md-flex justify-content-md-end">
                    <button id="edit-all" class="btn btn-primary">Edit</button>
                    <button id="save-all" class="btn btn-success" style="display:none;">Save</button>
                </div>

                <table class="table table-bordered table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Attendance</th>
                        </tr>
                    </thead>
                    <tbody id="attendance-table">
                        <!-- Attendance rows will be dynamically added here -->
                    </tbody>
                </table>
            </div>

            <script>
                const data = <?= json_encode($data) ?>; // PHP variable converted to JSON

                // Function to render attendance data for a selected session
                function renderAttendance(sessionId) {
                    const sessionData = data.find(session => session.session._id === sessionId);
                    const attendance = sessionData.attendance;

                    const tbody = document.getElementById('attendance-table');
                    tbody.innerHTML = ''; // Clear the existing rows

                    attendance.forEach(student => {
                        tbody.innerHTML += `
                <tr data-student-id="${student.student_id}">
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
            `;
                    });

                    // Ensure "Save All" is hidden and "Edit All" is visible
                    document.getElementById('edit-all').style.display = 'inline';
                    document.getElementById('save-all').style.display = 'none';
                }

                $('#session-select').change(function() {
                    renderAttendance(this.value);
                });

                // Initial render
                renderAttendance(document.getElementById('session-select').value);

                // Handle "Edit All" button click

                $('#edit-all').click(function() {
                    document.querySelectorAll('.read-only').forEach(el => el.style.display = 'none');
                    document.querySelectorAll('.edit-mode').forEach(el => el.style.display = 'inline');

                    this.style.display = 'none';
                    document.getElementById('save-all').style.display = 'inline';
                });

                // Handle "Save All" button click

                $('#save-all').click(function() {

                    const sessionId = document.getElementById('session-select').value;
                    const sessionData = data.find(session => session.session._id === sessionId);

                    const updates = [];
                    document.querySelectorAll('#attendance-table tr').forEach(row => {
                        const studentId = row.dataset.studentId;
                        const status = row.querySelector('.edit-mode').value;

                        updates.push({
                            id: studentId,
                            status: status
                        });
                    });


                    var formData = new FormData();
                    formData.append('attendanceData', JSON.stringify(updates));
                    formData.append('sessionId', sessionData.session._id);
                    formData.append('classId', sessionData.session.class_id);
                    formData.append('edit', true);

                    console.log('Form Data:', formData);


                    // Call API to save all changes
                    $.ajax({
                        url: '/api/app/attendance/saveedit', // Replace with your bulk API endpoint
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            var SuccessToast = new Toast('now', 'success', 'Attendance updated successfully');
                            SuccessToast.show();

                            // Save toast data to localStorage
                            localStorage.setItem('toastData', JSON.stringify({
                                title: 'success',
                                message: 'Attendance updated successfully',
                                type: 'now'
                            }));
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            console.error('Response:', xhr.responseText);

                            // Print the message alone in JSON
                            const response = JSON.parse(xhr.responseText);
                            console.log(JSON.stringify({
                                message: response.message
                            }));

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
                });
            </script>

<? } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            if (preg_match('/\b(?:not|no)\b/i', $errorMessage)) {
                $this->response($this->json([
                    'success' => false,
                    'message' => $errorMessage
                ]), 404);
            } else {
                $this->response($this->json([
                    'success' => false,
                    'message' => $errorMessage
                ]), 500);
            }
        }
    } else {
        $this->response($this->json(['message' => 'Bad request']), 400);
    }
};
