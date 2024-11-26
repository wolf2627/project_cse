<?php
// Include the ClassReport class file
include 'libs/load.php';

// Initialize the class
$classReport = new ClassReport();

// Get the section-wise report for a given test (use an actual test_id here)
$test_id = '673b3494bf7730ef8c0612b3';  // Replace with your test ID
$sectionWiseReport = $classReport->getSectionWiseReport($test_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Report Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .section-header {
            background-color: #f8f9fa;
            padding: 10px 20px;
            margin-top: 20px;
        }
        .section-table {
            margin-top: 15px;
        }
        .table th, .table td {
            text-align: center;
        }
        .modal-body {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center mb-4">Test Report Dashboard</h1>

    <?php if (!empty($sectionWiseReport)) : ?>
        <?php foreach ($sectionWiseReport as $section => $data) : ?>
            <div class="section-header">
                <h3>Section: <?php echo $section; ?></h3>
            </div>

            <div class="section-table">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Appeared Students</th>
                            <th>Pass Count</th>
                            <th>Fail Count</th>
                            <th>Student Marks</th>
                            <th>Failed Students</th> <!-- New column -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['Subjects'] as $subject_code => $subjectData) : ?>
                            <tr>
                                <td><?php echo $subjectData['Subject Code']; ?></td>
                                <td><?php echo $subjectData['Subject Name']; ?></td>
                                <td><?php echo $subjectData['Appeared Students']; ?></td>
                                <td><?php echo $subjectData['Pass Count']; ?></td>
                                <td><?php echo $subjectData['Fail Count']; ?></td>
                                <td>
                                    <?php
                                    if ($subjectData['Student Marks'] == 'nil') {
                                        echo 'No marks available';
                                    } else {
                                        echo '<button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#studentModal" 
                                        data-subject="' . $subjectData['Subject Name'] . '" 
                                        data-studentlist="' . htmlspecialchars(json_encode($subjectData['Student Marks'])) . '">
                                        View Students</button>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if ($subjectData['Fail Count'] > 0) {
                                        echo '<button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#failedStudentModal" 
                                        data-subject="' . $subjectData['Subject Name'] . '" 
                                        data-failedlist="' . htmlspecialchars(json_encode($subjectData['Failed Students'])) . '">
                                        View Failed Students</button>';
                                    } else {
                                        echo 'No Failed Students';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p>No data available for the given test.</p>
    <?php endif; ?>
</div>

<!-- Bootstrap Modal for Student List -->
<div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="studentModalLabel">Student List</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h5 id="subjectName"></h5>
        <table id="studentTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Registration Number</th>
                    <th>Marks</th>
                </tr>
            </thead>
            <tbody id="studentList"></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap Modal for Failed Students List -->
<div class="modal fade" id="failedStudentModal" tabindex="-1" aria-labelledby="failedStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="failedStudentModalLabel">Failed Students List</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h5 id="failedSubjectName"></h5>
        <table id="failedStudentTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Registration Number</th>
                    <th>Marks</th>
                </tr>
            </thead>
            <tbody id="failedStudentList"></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS (Optional, for enhanced UI interactivity) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
