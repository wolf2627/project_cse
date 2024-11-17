<?php

// https://domain/api/template/enrollsubstud
${basename(__FILE__, '.php')} = function () {
    if ($this->paramsExists(['semester', 'section', 'batch', 'department'])) {
        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }
        $semester = $this->_request['semester'];
        $section = $this->_request['section'];
        $batch = $this->_request['batch'];
        $subjects = $this->_request['subjects'];
        $dept = $this->_request['department'];

        // Load students based on the criteria
        $result = essentials::loadStudents($semester, $section, $batch, $dept);

        if ($result) { ?>

            <div>
                <ul class="list-inline">
                    <?
                    $count = 1;
                    foreach ($subjects as $subject) {
                    ?>

                        <li class="list-inline-item">
                            <?= $count . " - " . $subject . "" ?>
                        </li>
                    <?
                        $count++;
                    }
                    ?>
                </ul>
            </div>
            <hr>
            <div>
                <!-- Display Students Table -->
                <h3 class="mt-5">Students List</h3>
                <form method="POST" action="/enrollsubjects?enrolling_students" id="enroll-students-form">
                    <!-- Include hidden inputs for common data -->
                    <input type="hidden" name="semester" value="<?= $semester ?>">
                    <input type="hidden" name="section" value="<?= $section ?>">
                    <input type="hidden" name="batch" value="<?= $batch ?>">
                    <input type="hidden" name="year" value="<?= Date('Y') ?>">

                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Registration No</th>
                                <th>Student Name</th>
                                <th>Select Subjects</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            $student_count = 1;
                            foreach ($result as $student) {
                            ?>
                                <tr>
                                    <td><?= $student_count ?></td>
                                    <td><?= $student['reg_no'] ?></td>
                                    <td><?= $student['name'] ?></td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                                            <? foreach ($subjects as $subject) { ?>
                                                <!-- Checkbox for each subject -->
                                                <input
                                                    type="checkbox"
                                                    class="btn-check"
                                                    id="<?= $student['reg_no'] . "-" . $subject . "-check-box" ?>"
                                                    name="students[<?= $student['reg_no'] ?>][subjects][]"
                                                    value="<?= $subject ?>"
                                                    autocomplete="off"
                                                    checked>
                                                <label
                                                    class="btn btn-outline-success"
                                                    for="<?= $student['reg_no'] . "-" . $subject . "-check-box" ?>">
                                                    <?= $subject ?>
                                                </label>
                                            <? } ?>
                                        </div>
                                    </td>
                                </tr>
                            <?
                                $student_count++;
                            } ?>
                        </tbody>
                    </table>
                    <button type="submit" id="enroll-students-btn" class="btn btn-success">Enroll Selected Subjects</button>
                </form>
            </div>

        <?
        } else {
            $this->response($this->json(['message' => 'No students found']), 404);
        }

        ?>

<? } else {
        $this->response($this->json(['message' => 'bad request']), 400);
    }
};
