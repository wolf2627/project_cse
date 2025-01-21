<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="text-center mb-4">Assign Year in Charge</h1>
            <form id="assign-yearincharge-form">
                <!-- Faculty ID -->
                <div class="form-group row mb-3">
                    <label for="assign-faculty_id" class="col-sm-3 col-form-label">Faculty ID</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="assign-faculty_id" name="faculty_id" required>
                            <option value="">Select Faculty ID</option>
                            <?php
                            $faculties = Essentials::loadFaculties();
                            foreach ($faculties as $faculty) {
                                echo "<option value=\"{$faculty['faculty_id']}\">{$faculty['faculty_id']} - {$faculty['name']}</option>";
                            } 
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Department -->
                <div class="form-group row mb-3">
                    <label for="assign-department" class="col-sm-3 col-form-label">Department</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="assign-department" name="department" required>
                            <option value="">Select Department</option>
                            <?php
                            $departments = Essentials::loadDepartments();
                            foreach ($departments as $department) {
                                echo "<option value=\"{$department}\">{$department}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Batch -->
                <div class="form-group row mb-3">
                    <label for="assign-batch" class="col-sm-3 col-form-label">Batch</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="assign-batch" name="batch" required>
                            <option value="">Select Batch</option>
                            <?php
                            $batches = Essentials::loadBatches();
                            foreach ($batches as $batch) {
                                echo "<option value=\"{$batch}\">{$batch}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" id="assign-yearincharge-btn" class="btn btn-primary btn-lg px-5">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

