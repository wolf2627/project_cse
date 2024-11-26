
    <h2>Create Test</h2>
    <div class="container mt-5">
        <form id="createTestForm" method="POST" action="/test">
            <!-- Test Name -->
            <div class="mb-3">
                <label for="testname" class="form-label">Test Name</label>
                <input type="text" class="form-control" id="testname" name="testname" placeholder="Enter Test Name" required>
            </div>

            <!-- Month -->
            <div class="mb-3">
                <label for="month" class="form-label">Month</label>
                <select class="form-select" id="month" name="month" required>
                    <option value="">Select Month</option>
                    <?php
                    foreach (range(1, 12) as $m) {
                        $monthName = date('F', mktime(0, 0, 0, $m, 10)); // Get month name
                        echo "<option value='$monthName'>$monthName</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Batch -->
            <div class="mb-3">
                <label for="batch" class="form-label">Batch</label>
                <select class="form-select" id="batch" name="batch" required>
                    <option value="">Select Batch</option>
                    <?php
                    $batches = essentials::loadBatches(); // Replace with your function
                    foreach ($batches as $batch) {
                        echo "<option value='$batch'>$batch</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Semester -->
            <div class="mb-3">
                <label for="semester" class="form-label">Semester</label>
                <select class="form-select" id="semester" name="semester" required>
                    <option value="">Select Semester</option>
                    <?php
                    $semesters = essentials::loadSemesters(); // Replace with your function
                    foreach ($semesters as $semester) {
                        echo "<option value='$semester'>$semester</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Year -->
            <div class="mb-3">
                <label for="year" class="form-label">Year</label>
                <select class="form-select" id="year" name="year" required>
                    <option value="">Select Year</option>
                    <?php
                    $currentYear = date("Y");
                    foreach (range($currentYear, $currentYear + 5) as $year) {
                        echo "<option value='$year'>$year</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Department -->
            <div class="mb-3">
                <label for="department" class="form-label">Department</label>
                <select class="form-select" id="department" name="department" required>
                    <option value="">Select Department</option>
                    <?php
                    $departments = essentials::loadDepartments(); // Replace with your function
                    foreach ($departments as $department) {
                        echo "<option value='$department'>$department</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Subjects -->
            <div class="mb-3">
                <label for="subjects" class="form-label">Subjects</label>
                <div id="subjectsContainer">
                    <div class="input-group mb-2">
                        <!-- Subject Dropdown -->
                        <select class="form-select" name="subjects[0][code]" required>
                            <option value="">Select Subject</option>
                            <?php
                            $subjects = essentials::loadSubjects(); // Fetch subjects dynamically
                            foreach ($subjects as $subject) {
                                echo "<option value='{$subject['subject_code']}'>{$subject['subject_code']} - {$subject['subject_name']}</option>";
                            }
                            ?>
                        </select>
                        <!-- Date Input -->
                        <input type="date" class="form-control" name="subjects[0][date]" required>
                        <!-- Add Subject Button -->
                        <button type="button" class="btn btn-secondary addSubjectBtn">+</button>
                    </div>
                </div>
                <!-- Pre-rendered Subject Options -->
                <select id="subjectTemplate" class="d-none">
                    <option value="">Select Subject</option>
                    <?php
                    foreach ($subjects as $subject) {
                        echo "<option value='{$subject['subject_code']}'>{$subject['subject_code']} - {$subject['subject_name']}</option>";
                    }
                    ?>
                </select>
            </div>



            <!-- Duration -->
            <div class="mb-3">
                <label for="duration" class="form-label">Duration (in minutes)</label>
                <input type="number" class="form-control" id="duration" name="duration" placeholder="Enter Duration" required>
            </div>

            <!-- Total Marks and Pass Marks in a Single Row -->
            <div class="row">
                <!-- Total Marks -->
                <div class="col-md-6 mb-3">
                    <label for="totalmarks" class="form-label">Total Marks</label>
                    <input type="number" class="form-control" id="totalmarks" name="totalmarks" placeholder="Enter Total Marks" required>
                </div>

                <!-- Pass Marks -->
                <div class="col-md-6 mb-3">
                    <label for="passmarks" class="form-label">Pass Marks</label>
                    <input type="number" class="form-control" id="passmarks" name="passmarks" placeholder="Enter Pass Marks" required>
                </div>
            </div>

            <!-- Instructions -->
            <div class="mb-3">
                <label for="instructions" class="form-label">Instructions</label>
                <textarea class="form-control" id="instructions" name="instructions" placeholder="Enter Instructions" required></textarea>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Create Test</button>
        </form>
    </div>
