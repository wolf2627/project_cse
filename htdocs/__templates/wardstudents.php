<?php

$tutor = new Tutor(Session::getUser()->getFacultyId());

$assignedClass = $tutor->getAssingedClass();

$assignedStudents = $tutor->getTutorshipStudents();

?>

<!-- Display the Ward Students With Their Details -->

<div class="ward-students-dis container mt-4">

    <h2 class="text-center mb-2">Ward Students</h2>

    <!-- Assigned Class Details -->
    <div class="assigned-class-details mb-3 p-2 row">
        <div class="col">
            <p><strong>Batch:</strong> <?= htmlspecialchars($assignedClass['batch']) ?></p>
        </div>
        <div class="col text-end">
            <p><strong>Section:</strong> <?= htmlspecialchars($assignedClass['section']) ?></p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover text-center align-middle">
            <thead class="table-primary">
                <tr>
                    <th> S.No </th>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Roll No</th>
                    <th>Attendance %</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="ward-students-list">
                <?php $i = 1; ?>
                <?php foreach ($assignedStudents as $student) : ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($student['reg_no']) ?></td>
                        <td><?= htmlspecialchars($student['name']) ?></td>
                        <td><?= htmlspecialchars($student['roll_no']) ?></td>
                        <?php $attendance = new Attendance(); ?>
                        <td><?= htmlspecialchars($attendance->calculateAttendanceSubjectWise($student['reg_no'])['overallAttendancePercentage']) ?></td>
                        <td>
                            <a href="/attendance?atye=<?= base64_encode('sw') ?>&student_id=<?= $student['reg_no'] ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                View Attendance
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-primary view-student-detail-tutor"
                                data-student_id="<?= $student['reg_no'] ?>">
                                View Details
                            </button>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>