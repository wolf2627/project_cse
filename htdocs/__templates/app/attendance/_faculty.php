<?php

$facultyId = Session::getUser()->getFacultyId();
$classes = Classes::getClasses($facultyId);

?>

<div class='container mt-5'>

    <!-- Faculty Id -->

    <input type="hidden" id="facultyId-view-atten" value="<?php echo $facultyId; ?>">

    <div class='row'>
        <div class='col-md-12'>
            <h2 class="text-center mb-5"> Handling Classes</h2>
            <table class='table table-bordered'>
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Section</th>
                        <!-- <th>Batch</th> -->
                        <th>Semester</th>
                        <th>Department</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($classes as $class): ?>
                        <tr>
                            <td><?php echo $class['subject_code']; ?></td>
                            <td><?php echo $class['section']; ?></td>
                            <!-- <td><?php echo $class['batch']; ?></td> -->
                            <td><?php echo $class['semester']; ?></td>
                            <td><?php echo $class['department']; ?></td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm" data-class-id="<?php echo $class['class_id']; ?>" id="viewattendancebtn">View Attendance</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Attendance Container -->

    <div id="att-cont-viewatt">

    </div>

</div>