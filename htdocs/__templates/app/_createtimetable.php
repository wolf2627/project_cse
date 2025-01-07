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
                    <option value="<?php echo $subject['subject_code']; ?>"><?php echo $subject['subject_code'] . "-" . $subject['subject_name']; ?></option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Please select a subject.</div>
        </div>

        <!-- Batch -->
        <div class="mb-3">
            <label for="tt-batch" class="form-label">Batch</label>
            <select class="form-select" id="tt-batch" name="batch" required disabled>
                <option value="">Select the Batch</option>
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
            <select class="form-select" id="tt-faculty" name="tt-faculty" required disabled>
                <option value="">Select Faculty</option>
            </select>
            <div class="invalid-feedback">Please select a faculty.</div>
        </div>

        <!-- Section -->
        <div class="mb-3">
            <label for="tt-section" class="form-label">Section</label>
            <input type="text" class="form-control" id="tt-section" name="section" placeholder="Enter Section" required disabled>
            <div class="invalid-feedback">Please enter a section.</div>
        </div>

        <!-- Class ID -->
        <div class="mb-3" style="display: none;">
            <label for="tt-class_id" class="form-label">Class ID</label>
            <input type="text" class="form-control" id="tt-class_id" name="class_id" placeholder="Enter Class ID" required>
            <div class="invalid-feedback">Please enter a class ID.</div>
        </div>

        <!-- Day -->
        <div class="mb-3">
            <label for="tt-day" class="form-label">Day</label>
            <select class="form-select" id="tt-day" name="day" required>
                <option value="">Select a Day</option>
                <?php foreach (Essentials::loadDays() as $day) : ?>
                    <option value="<?php echo $day; ?>"><?php echo $day; ?></option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Please select a day.</div>
        </div>

        <!-- Slot -->
        <div class="mb-3">
            <label for="tt-time-slot" class="form-label">Slot</label>
            <select class="form-select" id="tt-time-slot" name="time slot" required>
                <option value="">Select a Slot</option>
                <?php foreach (Essentials::timeTableSlots() as $key => $slot) : ?>
                    <option value="<?php echo $slot; ?>"><?php echo $key . " - " . $slot; ?></option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Please select a slot.</div>
        </div>

        <div class="input-group mb-3">
            <span class="input-group-text">Class Room</span>
            <select class="form-select" id="tt-class-department" name="department" required>
                <option value="">Select the Department</option>
                <?php foreach (Essentials::loadDepartments() as $department) : ?>
                    <option value="<?php echo $department; ?>"><?php echo $department; ?></option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Please select a department.</div>
            <select class="form-select" id="tt-room" name="room" required>
                <option value="">Select the Room</option>
                <?php foreach (Essentials::loadClassPlace() as $room) : ?>
                    <option value="<?php echo $room; ?>"><?php echo $room; ?></option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Please select a room.</div>
        </div>

        <!-- Submit Button -->
        <button type="submit" id="tt-submit-btn" class="btn btn-primary">Assign Slot</button>
    </form>
</div>