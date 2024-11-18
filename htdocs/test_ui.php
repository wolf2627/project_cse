<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Class Information Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <main class="container mt-5">
    <h2>Create New Test Record</h2>
    <form id="classTestForm" method="POST">
      <!-- Faculty ID -->
      <div class="mb-3">
        <label for="facultyId" class="form-label">Faculty</label>
        <select class="form-select" id="facultyId" name="faculty_id" required>
          <option value="">Select Faculty</option>
          <!-- Dynamically populate -->
        </select>
      </div>

      <!-- Subject Code -->
      <div class="mb-3">
        <label for="subjectCode" class="form-label">Subject Code</label>
        <select class="form-select" id="subjectCode" name="subject_code" required>
          <option value="">Select Subject</option>
          <!-- Dynamically populate -->
        </select>
      </div>

      <!-- Batch -->
      <div class="mb-3">
        <label for="batch" class="form-label">Batch</label>
        <select class="form-select" id="batch" name="batch" required>
          <option value="">Select Batch</option>
          <!-- Dynamically populate -->
        </select>
      </div>

      <!-- Semester -->
      <div class="mb-3">
        <label for="semester" class="form-label">Semester</label>
        <select class="form-select" id="semester" name="semester" required>
          <option value="">Select Semester</option>
          <!-- Dynamically populate -->
        </select>
      </div>

      <!-- Section -->
      <div class="mb-3">
        <label for="section" class="form-label">Section</label>
        <select class="form-select" id="section" name="section" required>
          <option value="">Select Section</option>
          <!-- Dynamically populate -->
        </select>
      </div>

      <!-- Test Name -->
      <div class="mb-3">
        <label for="testName" class="form-label">Test Name</label>
        <input type="text" class="form-control" id="testName" name="test_name" placeholder="e.g., Midterm Exam" required>
      </div>

      <!-- Date -->
      <div class="mb-3">
        <label for="date" class="form-label">Date</label>
        <input type="date" class="form-control" id="date" name="date" required>
      </div>

      <!-- Student Marks -->
      <div class="mb-3">
        <label for="studentMarks" class="form-label">Student Marks</label>
        <div id="studentMarks">
          <div class="input-group mb-2 student-mark-row">
            <input type="text" class="form-control" name="student_marks[0][student_reg]" placeholder="Student Registration Number" required>
            <input type="number" class="form-control" name="student_marks[0][score]" placeholder="Score" min="0" max="100" required>
            <button type="button" class="btn btn-danger remove-mark-row">Remove</button>
          </div>
          <div class="input-group mb-2 student-mark-row">
            <input type="text" class="form-control" name="student_marks[0][student_reg]" placeholder="Student Registration Number" required>
            <input type="number" class="form-control" name="student_marks[0][score]" placeholder="Score" min="0" max="100" required>
            <button type="button" class="btn btn-danger remove-mark-row">Remove</button>
          </div>
        </div>
        <button type="button" class="btn btn-primary" id="addStudentRow">Add Student</button>
      </div>

      <!-- Submit Button -->
      <button type="submit" class="btn btn-success">Submit</button>
    </form>
  </main>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    $(document).ready(function() {
      let studentCount = 1;

      // Add new student mark row
      $('#addStudentRow').click(function() {
        const newRow = `
            <div class="input-group mb-2 student-mark-row">
                <input type="text" class="form-control" name="student_marks[${studentCount}][student_reg]" placeholder="Student Registration Number" required>
                <input type="number" class="form-control" name="student_marks[${studentCount}][score]" placeholder="Score" min="0" max="100" required>
                <button type="button" class="btn btn-danger remove-mark-row">Remove</button>
            </div>`;
        $('#studentMarks').append(newRow);
        studentCount++;
      });

      // Remove student mark row
      $(document).on('click', '.remove-mark-row', function() {
        $(this).closest('.student-mark-row').remove();
      });
    });
  </script>

</body>

</html>