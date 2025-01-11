<?php

// This API returns the template for the atttedance of students for a particular class.

${basename(__FILE__, '.php')} = function () {

    if ($this->paramsExists(['subjectCode', 'batch', 'semester', 'section', 'department'])) {
        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $subjectCode = $this->_request['subjectCode'];
        $batch = $this->_request['batch'];
        $semester = $this->_request['semester'];
        $section = $this->_request['section'];
        $department = $this->_request['department'];
        $timeSlot = $this->_request['timeslot'];
        $date = $this->_request['date'];
        $day = $this->_request['day'];


        if (isset($this->_request['facultyId'])) {
            $facultyId = $this->_request['facultyId'];
        } else {
            $facultyId = null;
        }

        $faculty = new Faculty();
        $result = $faculty->getAssignedStudents($subjectCode, $batch, $semester, $section, $department, $facultyId);


        if (!$result) { ?>
            <div class="alert alert-danger" role="alert">
                No students found.
            </div>
        <? } else { ?>

            <div class="container mt-4">
                <!-- Date, Day, and Time Slot -->
                <div class="d-flex justify-content-between mb-3">

                    <h5 class="d-inline-block">
                        Date: <?= $date ?>
                    </h5>
                    <h5 class="d-inline-block">
                        Day: <?= $day ?>
                    </h5>

                    <h5 class="d-inline-block">
                        Time Slot: <?= $timeSlot ?>
                    </h5>
                </div>

                <!-- Subject, Batch, Semester, Section, Department -->
                <div class="d-flex justify-content-between mb-3">

                    <h5 class="mr-4 d-inline-block">
                        Subject: <?= $subjectCode ?>
                    </h5>

                    <h5 class="mr-4 d-inline-block">
                        <?= $department . '- '  .$section  ?>
                    </h5>
                </div>
            </div>

            <div class="alert alert-warning" role="alert">
                Note: Present is defaultly checked. If a student is absent or on duty, then mark accordingly.
            </div>

            <div>
                <p> Total Strength: <?= count($result) ?> </p>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered rounded">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">S.No</th>
                            <th scope="col">Reg No</th>
                            <th scope="col">Name</th>
                            <th scope="col">Attendance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($result as $student) { ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= $student['reg_no'] ?></td>
                                <td><?= $student['name'] ?></td>
                                <td>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" id="attendance_<?= $student['reg_no'] ?>_present" name="attendance[<?= $student['reg_no'] ?>]" value="present" required checked>
                                        <label class="form-check-label" for="attendance_<?= $student['reg_no'] ?>_present">
                                            Present
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" id="attendance_<?= $student['reg_no'] ?>_absent" name="attendance[<?= $student['reg_no'] ?>]" value="absent" required>
                                        <label class="form-check-label" for="attendance_<?= $student['reg_no'] ?>_absent">
                                            Absent
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" id="attendance_<?= $student['reg_no'] ?>_on-duty" name="attendance[<?= $student['reg_no'] ?>]" value="on-duty" required>
                                        <label class="form-check-label" for="attendance_<?= $student['reg_no'] ?>_on-duty">
                                            On Duty
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

<? }
    } else {
        $this->response($this->json(['message' => 'Bad request']), 400);
    }
};
