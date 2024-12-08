<?
// Initialize the class
$classReport = new ClassReport();

// Get the section-wise report for a given test (use an actual test_id here)
$test_id = $data['0'];  // Replace with your test ID
$testname = $data['1'];
$department = $data['2'];
$sectionWiseReport = $classReport->getSectionWiseReport($test_id);

$testDetails = Test::getTestDetails($test_id);


$overall = $classReport->calculateOverallReport($sectionWiseReport);

?>


<div class="container mt-5">
    <h1 class="text-center mb-4"><?= htmlspecialchars($testname) ?> Report</h1>

    <h4 class="d-flex justify-content-between align-items-center">
        <span>Department: <?= htmlspecialchars($testDetails->department) ?></span>
        <span>Batch: <?= htmlspecialchars($testDetails->batch) ?></span>
        <span>Semester : <?= htmlspecialchars($testDetails->semester) ?></span>
    </h4>


    <?php if (!empty($sectionWiseReport)) : ?>
        <input type="hidden" id="test_name_class_wise_year" value="<?= $testname ?>">
        <input type="hidden" id="department_class_wise_year" value="<?= $department ?>">
        <input type="hidden" id="batch_class_wise_year" value="<?= $testDetails->batch ?>">
        <input type="hidden" id="semester_class_wise_year" value="<?= $testDetails->semester ?>">
        <button class="btn btn-primary" id="class-wise-year-report-btn" value="<?= $test_id ?>">Download PDF</button>

        <div class="overall-report">
            <div class="section-header">
                <h3>Year Analysis</h3>
            </div>
            <div class="section-table table-responsive small">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Appeared Students</th>
                            <th>Pass Count</th>
                            <th>Fail Count</th>
                            <th>Pass Percentage</th>
                            <th>View Section</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($overall as $subject_code) : ?>
                            <tr>
                                <td><?php echo $subject_code['Subject Code']; ?></td>
                                <td id="special-algin"><?php echo $subject_code['Subject Name']; ?></td>
                                <td><?php echo $subject_code['Total Appeared Students']; ?></td>
                                <td><?php echo $subject_code['Total Pass Count']; ?></td>
                                <td><?php echo $subject_code['Total Fail Count']; ?></td>
                                <td><?php echo $subject_code['Pass Percentage']; ?>%</td>
                                <td>
                                    <div class="btn-group">
                                        <?php foreach ($sectionWiseReport as $section => $data) : ?>
                                            <a href="#<?= $section ?>-report-table" class="btn btn-info"><?= $section ?></a>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php foreach ($sectionWiseReport as $section => $data) : ?>
            <div class="section-header" id="<?=$section?>-report-table">
                <h3>Section: <?php echo $section; ?></h3>
            </div>

            <div class="section-table table-responsive small">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Faculty</th>
                            <th>Appeared Students</th>
                            <th>Pass Count</th>
                            <th>Fail Count</th>
                            <th>Pass Percentage</th>
                            <th>Student Marks</th>
                            <th>Failed Students</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['Subjects'] as $subject_code => $subjectData) : ?>
                            <tr>
                                <td><?php echo $subjectData['Subject Code']; ?></td>
                                <td id="special-algin"><?php echo $subjectData['Subject Name']; ?></td>
                                <td id="special-algin"><?php echo $subjectData['Faculty Name']; ?></td>
                                <td><?php echo $subjectData['Appeared Students']; ?></td>
                                <td><?php echo $subjectData['Pass Count']; ?></td>
                                <td><?php echo $subjectData['Fail Count']; ?></td>
                                <td><?php echo $subjectData['Pass Percentage']; ?>%</td>
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