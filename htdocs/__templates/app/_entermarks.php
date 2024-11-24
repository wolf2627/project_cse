<div class="container mt-5">
    <?php

    $faculty = new Faculty();
    $faculty_class = $faculty->getClass($data['1']);

    $batch = $faculty_class['batch'];
    $semester = $faculty_class['semester'];
    $subject_code = $faculty_class['subject_code'];
    $testname = $data['0'];
    $section = $faculty_class['section'];
    $department = $faculty_class['department'];


    $code = base64_decode($_GET['code']);
    $testname = base64_decode($_GET['testname']);

    $register_marks = $faculty->getMarks($batch, $semester, $subject_code, $testname, $section, $department);

    if ($register_marks) {
        //TODO: There is a bug in update marks form in fetching marks. Fix it
        if (isset($_GET['edit'])) {
            echo "<h2>Edit Marks for {$testname} ({$code}) - {$department} (Sem: {$semester}) </h2>";
    ?>
            <div class="container mt-5">
                <!-- Dropdown for Students -->
                <div class="mb-4">

                    <label for="student-select" class="form-label">Select Student</label>
                    <select id="student-select" class="form-select" aria-label="Student Selection">
                        <option value="" disabled selected>Select Student</option>
                        <?php foreach ($register_marks['marks'] as $index => $student): ?>
                            <option value="<?= $index ?>" data-regno="<?= $student['reg_no'] ?>" data-name="<?= $student['studentname'] ?>" data-marks="<?= $student['marks'] ?>">
                                <?= $student['reg_no'] ?> - <?= $student['studentname'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Current Marks and Update Form -->
                <div id="update-form" class="d-none"
                    data-batch="<?= $batch ?>"
                    data-semester="<?= $faculty_class['semester'] ?>"
                    data-subject_code="<?= $faculty_class['subject_code'] ?>"
                    data-testname="<?= $testname ?>"
                    data-section="<?= $faculty_class['section'] ?>"
                    data-department="<?= $faculty_class['department'] ?>">
                    <div class="mb-3">
                        <label for="reg-no" class="form-label">Registration Number</label>
                        <input type="text" id="reg-no" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="student-name" class="form-label">Student Name</label>
                        <input type="text" id="student-name" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="current-marks" class="form-label">Current Marks</label>
                        <input type="text" id="current-marks" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="updated-marks" class="form-label">New Marks</label>
                        <input type="number" id="updated-marks" class="form-control" placeholder="Enter new marks">
                    </div>

                    <button id="update-btn" class="btn btn-primary">Update Marks</button>
                    <button id="reset-btn" class="btn btn-info">Edit Another Student</button>
                    <!-- <div id="back-btn" class="btn btn-secondary">Back</div> -->

                </div>

                <div>
                    <hr>
                    <button id="edit-back-btn" class="btn btn-secondary">Back</button>
                </div>

            <? } else {
            echo "<h2>Marks already entered for {$testname} ({$code}) - {$department} (Sem: {$semester})</h2>";

            ?>

                <form id="excelForm">
                    <input type="text" id="batch" name="batch" value="<?= $batch ?>" hidden>
                    <input type="text" id="semester" name="semester" value="<?= $semester ?>" hidden>
                    <input type="text" id="subject_code" name="subject_code" value="<?= $subject_code ?>" hidden>
                    <input type="text" id="test_name" name="test_name" value="<?= $testname ?>" hidden>
                    <input type="text" id="section" name="section" value="<?= $section ?>" hidden>
                    <input type="text" id="department" name="department" value="<?= $department ?>" hidden>
                    <button type="button" class="btn btn-warning" id="generateExcel-btn">Generate Excel</button>
                </form>
                <div class="table-responsive small">

                    <table class="table table-hover table-bordered mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>S.No</th>
                                <th>Reg. No.</th>
                                <th>Student Name</th>
                                <th>Marks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $index = 1;

                            // Check if $register_marks contains valid data
                            if (isset($register_marks->marks)) {
                                foreach ($register_marks->marks as $student) {
                                    // Each student is a BSONDocument, so access its fields correctly
                                    $reg_no = $student['reg_no'];
                                    $student_name = $student['studentname'];
                                    $marks = $student['marks'];

                                    echo "<tr>
                        <td>{$index}</td>
                        <td>{$reg_no}</td>
                        <td>{$student_name}</td>
                        <td>{$marks}</td>
                    </tr>";
                                    $index++;
                                }
                            } else {
                                echo "<tr><td colspan='4'>No data available</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <button class="btn btn-warning" onclick="window.location.href='/markentry?edit&code=<?= base64_encode($data['1']) ?>&testname=<?= base64_encode($data['0']) ?>&maxmark=<?= $data['4'] ?>'">Edit Marks</button>
                </div>
            <?php
        }
    } else {
        if (isset($_POST['student_marks'])) {
            echo "<h2>Entered Marks for {$testname} ({$code}) - {$department} (Sem: {$semester})</h2>";
            // print_r($_POST['student_marks']); 
            $student_marks = $_POST['student_marks'];
            $_POST = array(); //clearing the values of the form
            echo "<h6>Note : Refresh the page to generate Excel File if needed. You can also generate the excel file later.<h6>";
            // to refresh the page
            echo "<button class='btn btn-primary' onclick='window.location.href=\"/markentry?code=" . base64_encode($data['1']) . "&testname=" . base64_encode($data['0']) . "\"'>Refresh</button>";

            $result = $faculty->enterMark($batch, $semester, $subject_code, $testname, $section, $student_marks, $department);
            ?>
                <div class="table-responsive small">
                    <table class="table table-hover table-bordered mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>S.No</th>
                                <th>Reg. No.</th>
                                <th>Student Name</th>
                                <th>Marks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $index = 1;
                            foreach ($student_marks as $student) {
                                echo "<tr>
                            <td>{$index}</td>
                            <td>{$student['reg_no']}</td>
                            <td>{$student['studentname']}</td>
                            <td>{$student['marks']}</td>
                          </tr>";
                                $index++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>


            <?php
        } else {
            echo "<h2>Enter Marks for {$testname} ({$code}) - {$department} (Sem: {$semester})</h2>";
            ?>

                <form id="studentMarksForm" method="POST" action="/markentry?code=<?= base64_encode($data['1']) ?>&testname=<?= base64_encode($data['0']) ?>">
                    <div class="table-responsive small">
                        <table class="table table-hover table-bordered mt-3">
                            <thead class="table-dark">
                                <tr>
                                    <th>S.No</th>
                                    <th>Reg. No.</th>
                                    <th>Student Name</th>
                                    <th>Marks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // print_r($data);
                                // Fetch student data from the database
                                $students = $faculty->getAssignedStudents($data[1], $data[2], $data[3]);

                                if (!empty($students)) {
                                    $index = 1;
                                    foreach ($students as $student) {
                                        echo "<tr>
                                    <td>{$index}</td>
                                    <td>
                                        <input type='text' name='student_marks[{$index}][reg_no]' class='form-control' value='{$student['reg_no']}' hidden>
                                        {$student['reg_no']}
                                    </td>
                                    <td>
                                        <input type='text' name='student_marks[{$index}][studentname]' class='form-control' value='{$student['name']}' hidden>
                                        {$student['name']}
                                    </td>
                                    <td>
                                        <input type='number' min='1' max='{$data['4']}' name='student_marks[{$index}][marks]' class='form-control' placeholder='Enter Marks' required>
                                    </td>
                                  </tr>";
                                        $index++;
                                    }
                                } else {
                                    echo "<tr>
                                <td colspan='4' class='text-center'>No students found</td>
                              </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary" id="markentry-btn">Submit</button>
                </form>
            </div>

    <?php }
    } ?>