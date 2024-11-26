<?
// Initialize the class
$classReport = new ClassReport();

// Get the section-wise report for a given test (use an actual test_id here)
$test_id = $data['0'];  // Replace with your test ID
$testname = $data['1'];
$department = $data['2'];
$sectionWiseReport = $classReport->getSectionWiseReport($test_id);
?>
<style>
    .section-header {
        background-color: #f8f9fa;
        padding: 10px 20px;
        margin-top: 20px;
    }

    [data-bs-theme="dark"] .section-header {
        background-color: #343a40;
        color: white;
    }

    .section-table {
        margin-top: 15px;
    }

    .table th,
    .table td {
        text-align: center;
    }

    .modal-body {
        max-height: 600px;
        overflow-y: auto;
    }
</style>


<div class="container mt-5">
    <h1 class="text-center mb-4"><?= htmlspecialchars($testname) ?> Report</h1>

    <h4>Department: <?= htmlspecialchars($department) ?></h6>
        <?php if (!empty($sectionWiseReport)) : ?>
            <?php foreach ($sectionWiseReport as $section => $data) : ?>
                <div class="section-header">
                    <h3>Section: <?php echo $section; ?></h3>
                </div>

                <div class="section-table">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Subject Code</th>
                                <th>Subject Name</th>
                                <th>Appeared Students</th>
                                <th>Pass Count</th>
                                <th>Fail Count</th>
                                <th>Student Marks</th>
                                <th>Failed Students</th> <!-- New column -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['Subjects'] as $subject_code => $subjectData) : ?>
                                <tr>
                                    <td><?php echo $subjectData['Subject Code']; ?></td>
                                    <td><?php echo $subjectData['Subject Name']; ?></td>
                                    <td><?php echo $subjectData['Appeared Students']; ?></td>
                                    <td><?php echo $subjectData['Pass Count']; ?></td>
                                    <td><?php echo $subjectData['Fail Count']; ?></td>
                                    <td>
                                        <?php
                                        if ($subjectData['Student Marks'] == 'nil') {
                                            echo 'No marks available';
                                        } else {
                                            echo '<button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#studentModal" 
                                        data-subject="' . $subjectData['Subject Name'] . '" 
                                        data-studentlist="' . htmlspecialchars(json_encode($subjectData['Student Marks'])) . '">
                                        View Students</button>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($subjectData['Fail Count'] > 0) {
                                            echo '<button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#failedStudentModal" 
                                        data-subject="' . $subjectData['Subject Name'] . '" 
                                        data-failedlist="' . htmlspecialchars(json_encode($subjectData['Failed Students'])) . '">
                                        View Failed Students</button>';
                                        } else {
                                            echo 'No Failed Students';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>No data available for the given test.</p>
        <?php endif; ?>
</div>

<!-- Bootstrap Modal for Student List -->
<div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentModalLabel">Student List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5 id="subjectName"></h5>
                <table id="studentTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Student Name</th>
                            <th>Registration Number</th>
                            <th>Marks</th>
                        </tr>
                    </thead>
                    <tbody id="studentList"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Modal for Failed Students List -->
<div class="modal fade" id="failedStudentModal" tabindex="-1" aria-labelledby="failedStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="failedStudentModalLabel">Failed Students List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5 id="failedSubjectName"></h5>
                <table id="failedStudentTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Student Name</th>
                            <th>Registration Number</th>
                            <th>Marks</th>
                        </tr>
                    </thead>
                    <tbody id="failedStudentList"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
    // JavaScript to handle the modal and populate student details dynamically
    var studentModal = document.getElementById('studentModal');
    studentModal.addEventListener('show.bs.modal', function(event) {
        // Extract data attributes from the button that triggered the modal
        var button = event.relatedTarget;
        var subjectName = button.getAttribute('data-subject');
        var studentList = JSON.parse(button.getAttribute('data-studentlist'));

        // Update the modal content
        var modalTitle = studentModal.querySelector('#subjectName');
        modalTitle.textContent = 'Subject: ' + subjectName;

        var studentListElement = studentModal.querySelector('#studentList');
        studentListElement.innerHTML = ''; // Clear any existing data

        var i = 1;
        // Populate the student table
        if (studentList.length > 0) {
            studentList.forEach(function(student) {
                var row = document.createElement('tr');
                row.innerHTML = '<td>' + i + '</td><td>' + student['Student Name'] + '</td><td>' + student['Reg No'] + '</td><td>' + student['Marks'] + '</td>';
                studentListElement.appendChild(row);
                i++;
            });
        } else {
            var row = document.createElement('tr');
            row.innerHTML = '<td colspan="3">No students available for this subject.</td>';
            studentListElement.appendChild(row);
        }
    });

    // JavaScript to handle the modal for failed students
    var failedStudentModal = document.getElementById('failedStudentModal');
    failedStudentModal.addEventListener('show.bs.modal', function(event) {
        // Extract data attributes from the button that triggered the modal
        var button = event.relatedTarget;
        var subjectName = button.getAttribute('data-subject');
        var failedStudentList = JSON.parse(button.getAttribute('data-failedlist'));

        // Update the modal content
        var modalTitle = failedStudentModal.querySelector('#failedSubjectName');
        modalTitle.textContent = 'Subject: ' + subjectName;

        var failedStudentListElement = failedStudentModal.querySelector('#failedStudentList');
        failedStudentListElement.innerHTML = ''; // Clear any existing data

        // Populate the failed student table
        var i = 1;
        if (failedStudentList.length > 0) {
            failedStudentList.forEach(function(student) {
                var row = document.createElement('tr');
                row.innerHTML = '<td>' + i + '</td><td>' + student['Student Name'] + '</td><td>' + student['Reg No'] + '</td><td>' + student['Marks'] + '</td>';
                failedStudentListElement.appendChild(row);
                i++;
            });
        } else {
            var row = document.createElement('tr');
            row.innerHTML = '<td colspan="3">No failed students available for this subject.</td>';
            failedStudentListElement.appendChild(row);
        }
    });
</script>