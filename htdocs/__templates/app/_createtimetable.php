<div class="container mt-5">
    <h2 class="text-center mb-5">Assign Timetable Slot</h2>
    <form id="timetable-form" novalidate>


        <!-- Department -->
        <div class="mb-3">
            <label for="tt-department" class="form-label">Department</label>
            <select class="form-select" id="tt-department" name="department" required>
                <option value="">Select the Department</option>
                <?php foreach (Essentials::loadDepartments() as $department) : ?>
                    <option value="<?php echo $department; ?>"><?php echo $department; ?></option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Please select a department.</div>
        </div>

        <!-- Subject Code -->
        <div class="mb-3">
            <label for="tt-subject_code" class="form-label">Subject Code</label>
            <select class="form-select" id="tt-subject_code" name="subject_code" required>
                <option value="">Select a Subject</option>
                <?php foreach (Essentials::loadSubjects() as $subject) : ?>
                    <option value="<?php echo $subject['subject_code']; ?>"><?php echo $subject['subject_code'] . " - " . $subject['subject_name']; ?></option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Please select a subject.</div>
        </div>

        <!-- Batch -->
        <div class="mb-3">
            <label for="tt-batch" class="form-label">Batch</label>
            <select class="form-select" id="tt-batch" name="batch" required>
                <option value="">Select Batch</option>
            </select>
            <div class="invalid-feedback">Please select a batch.</div>
        </div>

        <!-- Semester -->
        <div class="mb-3">
            <label for="tt-semester" class="form-label">Semester</label>
            <select class="form-select" id="tt-semester" name="semester" required>
                <option value="">Select Semester</option>
                <?php foreach (Essentials::loadSemesters() as $semester) : ?>
                    <option value="<?php echo $semester; ?>"><?php echo $semester; ?></option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Please select a semester.</div>
        </div>

        <!-- Faculty Name -->
        <div class="mb-3">
            <label for="tt-faculty" class="form-label">Faculty Name</label>
            <select class="form-select" id="tt-faculty" name="faculty_id" required>
                <option value="">Select Faculty</option>
            </select>
            <div class="invalid-feedback">Please select a faculty.</div>
        </div>

        <!-- Section -->
        <div class="mb-3">
            <label for="tt-section" class="form-label">Section</label>
            <input type="text" class="form-control" id="tt-section" name="section" placeholder="Enter Section" required readonly>
            <div class="invalid-feedback">Please enter a section.</div>
        </div>

        <!-- Class ID -->
        <div class="mb-3" style="display: none;">
            <label for="tt-class_id" class="form-label">Class ID</label>
            <input type="text" class="form-control" id="tt-class_id" name="class_id" placeholder="Enter Class ID" required>
            <div class="invalid-feedback">Please enter a class ID.</div>
        </div>

        <div id="class-room-container" class="mb-3">
            <label class="form-label">Class Room</label>
            <div class="input-group mb-2">
                <select class="form-select" id="tt-class-department" name="department" required>
                    <option value="">Select the Department</option>
                    <?php foreach (Essentials::loadDepartments() as $department) : ?>
                        <option value="<?php echo $department; ?>"><?php echo $department; ?></option>
                    <?php endforeach; ?>
                </select>
                <select class="form-select" id="tt-room" name="room" required>
                    <option value="">Select the Room</option>
                    <?php foreach (Essentials::loadClassPlace() as $room) : ?>
                        <option value="<?php echo $room; ?>"><?php echo $room; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="invalid-feedback">Please select a department and room.</div>
        </div>

        
        <!-- Days and Slots -->
        <div id="day-slot-container" class="mb-3">
            <label class="form-label">Days and Slots</label>
            <div class="input-group mb-2">
                <select class="form-select" name="days[]" required>
                    <option value="">Select Day</option>
                    <?php $days = Essentials::loadDays(); ?>
                    <?php foreach ($days as $day) : ?>
                        <option value="<?php echo $day; ?>"><?php echo $day; ?></option>
                    <?php endforeach; ?>
                </select>
                <select class="form-select" name="slots[]" required>
                    <option value="">Select Time Slot</option>
                    <?php $slots = Essentials::loadtimeTableSlots(); ?>
                    <?php foreach ($slots as $slot_key => $slot_value) : ?>
                        <option value="<?php echo $slot_value; ?>"><?php echo $slot_key . ' - '. $slot_value; ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="btn btn-success" id="add-slot-btn">
                    <i class="bi bi-plus-circle"></i>
                </button>
            </div>
        </div>

        <!-- Pre Rendered Days and Slots Option -->
        <select id="day-slot-template" class="d-none">
            <option value="">Select Day</option>
            <?php foreach ($days as $day) : ?>
                <option value="<?php echo $day; ?>"><?php echo $day; ?></option>
            <?php endforeach; ?>
        </select>
        <select id="slot-template" class="d-none">
            <option value="">Select Time Slot</option>
            <?php foreach ($slots as $slot_key => $slot_value) : ?>
                <option value="<?php echo $slot_value; ?>"><?php echo $slot_key . ' - '. $slot_value; ?></option>
            <?php endforeach; ?>
        </select>


        <!-- Submit Button -->
        <button type="submit" id="tt-submit-btn" class="btn btn-primary">Assign Slot</button>
    </form>
</div>