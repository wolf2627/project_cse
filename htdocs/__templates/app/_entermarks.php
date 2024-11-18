<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="container mt-5">
        <?php

        $faculty = new Faculty();
        $faculty_class = $faculty->getClass($data['1']);

        $batch = $faculty_class['batch'];
        $semester = $faculty_class['semester'];
        $subject_code = $faculty_class['subject_code'];
        $testname = $data['0'];
        $section = $faculty_class['section'];


        $code = base64_decode($_GET['code']);
        $testname = base64_decode($_GET['testname']);

        $register_marks = $faculty->getMarks($batch, $semester, $subject_code, $testname, $section);

        if ($register_marks) {
            echo "<h2>Marks already entered for {$testname} ({$code})</h2>";
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
            </div>
            <?php
        } else {
            if (isset($_POST['student_marks'])) {
                echo "<h2>Entered Marks</h2>";
                // print_r($_POST['student_marks']); 
                $student_marks = $_POST['student_marks'];

                // echo "Form : " . $batch . " " . $semester . " " . $subject_code . " " . $testname . " " . $section . " " . $student_marks;

                $result = $faculty->enterMark($batch, $semester, $subject_code, $testname, $section, $student_marks);
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
                echo "<h2>Enter Marks for {$testname} ({$code})</h2>";
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
                                        <input type='number' name='student_marks[{$index}][marks]' class='form-control' placeholder='Enter Marks' required>
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
</main>