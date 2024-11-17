<?php
// Fetch subjects from database (for example, from the "subjects" collection)
$subjects = [
    ['subject_code' => 'GE2C25', 'subject_name' => 'Problem Solving and Python Programming'],
    ['subject_code' => 'CS3C12', 'subject_name' => 'Data Structures'],
    ['subject_code' => 'CS4C15', 'subject_name' => 'Database Management Systems']
];

// Fetch students based on selected filters (year, semester, batch)
$students = [
    ['name' => 'John Doe', 'reg_no' => '9213245'],
    ['name' => 'Jane Smith', 'reg_no' => '9213246'],
    ['name' => 'Samuel Green', 'reg_no' => '9213247']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll Students</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Enroll Students in Subjects</h2>

    <!-- Form for selecting Year, Semester, Batch, and Subjects -->
    <form method="POST" action="enroll.php">
        <!-- Filters -->
        <div class="form-row mb-4">
            <div class="col">
                <label for="year">Year</label>
                <select class="form-control" id="year" name="year">
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                </select>
            </div>
            <div class="col">
                <label for="semester">Semester</label>
                <select class="form-control" id="semester" name="semester">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                </select>
            </div>
            <div class="col">
                <label for="batch">Batch</label>
                <select class="form-control" id="batch" name="batch">
                    <option value="2022-2026">2022-2026</option>
                    <option value="2023-2027">2023-2027</option>
                </select>
            </div>
        </div>

        <!-- Available Subjects to Enroll -->
        <div class="form-group mb-4">
            <label for="subjects">Select Subjects</label>
            <select class="form-control" id="subjects" name="subjects[]" multiple>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?= $subject['subject_code']; ?>"><?= $subject['subject_name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Submit Button to Filter Students -->
        <button type="submit" class="btn btn-primary">Filter Students</button>
    </form>

    <hr>

    <!-- Display Students Table -->
    <h3 class="mt-5">Students List</h3>
    <form method="POST" action="enroll.php">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student Name</th>
                    <th>Registration No</th>
                    <th>Select Subjects</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $index => $student): ?>
                    <tr>
                        <td><?= $index + 1; ?></td>
                        <td><?= $student['name']; ?></td>
                        <td><?= $student['reg_no']; ?></td>
                        <td>
                            <select class="form-control" name="enrollments[<?= $student['reg_no']; ?>][]">
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?= $subject['subject_code']; ?>"><?= $subject['subject_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Button to Enroll Selected Subjects to All Students -->
        <button type="submit" class="btn btn-success">Enroll Selected Subjects</button>
    </form>

</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
