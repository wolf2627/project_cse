<?php
$faculty = new Faculty();
$facultyId = $faculty->getFacultyId(); // Assume this fetches the logged-in faculty's ID.

$att = new Attendance();

try {
    $pending = $att->getPendingAttendance($facultyId);
} catch (Exception $e) {
    echo "<div class='alert alert-danger text-center' role='alert'>{$e->getMessage()}</div>";
    return;
}
$combinedData = [];

foreach ($pending as $class) {
    $cls = new Classes();
    $details = $cls->getClassDetails($class['class_id']);
    $class['class_details'] = $details;
    $combinedData[] = $class;
}

?>

<div class="container mt-5" id="markAttendance-container">
    <h3 class="text-center">Mark Attendance</h3>

    <!-- readonly input for faculty id -->
    <input type="hidden" id="facultyId-markatt" value="<?php echo $facultyId; ?>">

    <form id="attendanceForm">
        <!-- Class Selection -->
        <div class="form-group">
            <label for="classSelect-att" class="font-weight-bold">Select Slot:</label>
            <select id="classSelect-att" name="class_id" class="form-control" required>
                <option value="">-- Select Slot --</option>
                <?php foreach ($combinedData as $class) : ?>
                    <option value="<?php echo $class['class_id']; ?>" data-department=<?= $class['class_details']['department'] ?> data-section=<?= $class['class_details']['section'] ?> data-subject=<?= $class['class_details']['subject_code'] ?> data-batch=<?= $class['class_details']['batch'] ?> data-semester=<?= $class['class_details']['semester'] ?> data-date=<?= $class['date'] ?> data-day=<?= $class['day'] ?> data-timeslot=<?= $class['timeslot'] ?>>
                        <?php echo $class['class_details']['subject_code'] . ' - ' . $class['class_details']['batch'] . ' - ' . $class['class_details']['semester'] . ' - ' . $class['date'] . ' - ' . $class['day'] . ' - ' . $class['timeslot']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="alert alert-info mt-3" role="alert" id="attendanceAlert">
            Please select a slot from the dropdown above to mark attendance.
        </div>

        <!-- Attendance Template -->
        <div id="attendanceTemplate" class="mt-4">
            <!-- Attendance Template will be inserted here -->
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary mt-3" id="submitAttendanceBtn" style="display: none;">Mark Attendance</button>
    </form>
</div>