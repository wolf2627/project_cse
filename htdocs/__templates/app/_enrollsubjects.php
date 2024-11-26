<?php
if (isset($_POST['semester']) && isset($_POST['section']) && isset($_POST['batch']) && isset($_POST['year']) && isset($_POST['students'])) {

    $semester = $_POST['semester'];
    $section = $_POST['section'];
    $batch = $_POST['batch'];
    $year = $_POST['year'];
    $students = $_POST['students'];

    $enroll_result = Creator::enrollStudent($students, $semester, $batch, $section, $year);

    if ($enroll_result) { ?>
        <h2>Subject Selection Results</h2>
        <div class='alert alert-success' role='alert'>
            Students enrolled successfully <br>
        </div>

        <div class="table-responsive small">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">Student Reg No</th>
                        <th scope="col">Status</th>
                        <th scope="col">Success Subjects</th>
                        <th scope="col">Failed Subjects</th>
                        <th scope="col">Updated Subjects</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($enroll_result as $student) { ?>
                        <tr>
                            <td><?= htmlspecialchars($student['student_id']) ?></td>
                            <td>
                                Success: <?= $student['status']['success'] ?>,
                                Failure: <?= $student['status']['failure'] ?>,
                                Updated: <?= $student['status']['updated'] ?>
                            </td>
                            <td><?= htmlspecialchars(implode(', ', $student['details']['success_subjects']) ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars(implode(', ', $student['details']['failed_subjects']) ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars(implode(', ', $student['details']['updated_subjects']) ?? 'N/A') ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
<?php } else { ?>
    <h2>Subject Selection</h2>
    <div class="container mt-5">
        <form action="?gettingStudents" id="subject-selection-form" method="POST">
            <div class="form-row mb-4">
                <div class="col mb-4">
                    <label for="year">Present Year</label>
                    <select class="form-select" id="year" name="year" required>
                        <option value="">Select Year</option>
                        <option value="<?= date("Y") ?>"><?= date("Y") ?></option>
                    </select>
                </div>

                <div class="col mb-4">
                    <label for="department">Department</label>
                    <select class="form-select" id="department" name="department">
                        <option value="">Select Department</option>
                        <?php
                        $departments = essentials::loadDepartments();
                        foreach ($departments as $department) {
                            echo "<option value='$department'>$department</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col mb-4">
                    <label for="semester">Semester</label>
                    <select class="form-select" id="semester" name="semester">
                        <option value="">Select Semester</option>
                        <?php
                        $semsters = essentials::loadSemesters();
                        foreach ($semsters as $semester) {
                            echo "<option value='$semester'>$semester</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col mb-4">
                    <label for="section">Section</label>
                    <select class="form-select" id="section" name="section">
                        <option value="">Select Section</option>
                        <?php
                        $sections = essentials::loadSections();
                        foreach ($sections as $section) {
                            echo "<option value='$section'>$section</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col mb-4">
                    <label for="batch">Batch</label>
                    <select class="form-select" id="batch" name="batch">
                        <option value="">Select Batch</option>
                        <?php
                        $batches = essentials::loadBatches();
                        foreach ($batches as $batch) {
                            echo "<option value='$batch'>$batch</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Available Subjects to Enroll -->
            <div class="form-group mb-4">
                <label for="subjects">Select Subjects</label>
                <select class="form-select" id="subjects" name="subjects[]" multiple="multiple">
                    <?php
                    $subjects = essentials::loadSubjects();
                    foreach ($subjects as $subject): ?>
                        <option value="<?= $subject['subject_code']; ?>">
                            <?= $subject['subject_code'] . " - " . $subject['subject_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary" id="fetch-students-btn">Fetch Students</button>
        </form>


        <!-- This container will hold the horizontally aligned subjects -->
        <div id="subjects-list-container" style="display: none;"></div>

    </div>
<?php } ?>