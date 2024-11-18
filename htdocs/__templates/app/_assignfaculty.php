<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <h2 class="mb-4">Assign Faculty</h2>

    <div class="container mt-5">

        <form id="classForm" method="POST">
            <!-- Faculty ID -->
            <div class="mb-3">
                <label for="facultyId" class="form-label">Faculty</label>
                <select class="form-select" id="facultyId" name="faculty_id" required>
                    <option value="">Select Faculty</option>
                    <?php
                    $result = essentials::loadFaculties();
                    foreach ($result as $faculty) { ?>
                        <option value='<?= $faculty['faculty_id'] ?>'><?= $faculty['faculty_id'] . " - " . $faculty['name'] . " - " . $faculty['department'] ?></option>
                    <?php }
                    ?>
                </select>
            </div>

            <!-- Subject Code -->
            <div class="mb-3">
                <label for="subjectCode" class="form-label">Subject Code</label>
                <select class="form-select" id="subjectCode" name="subject_code" required>
                    <option value="">Select Subject Code</option>
                    <?
                    $result = essentials::loadSubjects();
                    foreach ($result as $subject) { ?>
                        <option value='<?= $subject['subject_code'] ?>'><?= $subject['subject_code'] . " - " . $subject['subject_name'] ?></option>

                    <? } ?>
                </select>
            </div>

            <!-- Batch -->
            <div class="mb-3">
                <label for="batch" class="form-label">Batch</label>
                <select class="form-select" id="batch" name="batch" required>
                    <option value="">Select Batch</option>
                    <?php
                    $result = essentials::loadBatches();
                    foreach ($result as $batch) { ?>
                        <option value='<?= $batch?>'><?= $batch?></option>
                    <?php } ?>
                </select>
            </div>


            <!-- Department -->
            <div class="mb-3">
                <label for="department" class="form-label">Department</label>
                <select class="form-select" id="department" name="department" required>
                    <option value="">Select Department</option>
                    <?php
                    $result = essentials::loadDepartments();
                    foreach ($result as $department) { ?>
                        <option value='<?= $department?>'><?= $department?></option>
                    <?php } ?>
                </select>
            </div>
            
            <!-- Semester -->
            <div class="mb-3">
                <label for="semester" class="form-label">Semester</label>
                <select class="form-select" id="semester" name="semester" required>
                    <option value="">Select Semester</option>
                    <?php
                    $result = essentials::loadSemesters();
                    foreach ($result as $semester) { ?>
                        <option value='<?= $semester?>'><?= $semester?></option>
                    <?php } ?>
                </select>
            </div>

            <!-- Section -->
            <div class="mb-3">
                <label for="section" class="form-label">Section</label>
                <select class="form-select" id="section" name="section" required>
                    <option value="">Select Section</option>
                    <?php
                    $result = essentials::loadSections();
                    foreach ($result as $section) { ?>
                        <option value='<?= $section?>'><?= $section?></option>
                    <?php } ?>
                </select>
            </div>

            <!-- Student Sections -->
            <div class="mb-3">
                <label for="studentSections" class="form-label">Student Sections</label>
                <select class="form-select" id="studentSections" name="student_sections[]" multiple='multiple' required>
                <?php
                    $result = essentials::loadSections();
                    foreach ($result as $section) { ?>
                        <option value='<?= $section?>'><?= $section?></option>
                    <?php } ?>
                </select>
                <small class="form-text text-muted">select based on instruction given for common and elective subjects</small>
            </div>

            <!-- Year -->
            <div class="mb-3">
                <label for="year" class="form-label">Year</label>
                <select class="form-select" id="year" name="year" required>
                    <option value="">Select Year</option>
                    <option value="<?=Date('Y')?>"><?=Date('Y')?></option>
                </select>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="assign-faculty-btn" class="btn btn-primary">Assign Faculty</button>
        </form>
    </div>
</main>