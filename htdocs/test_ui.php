<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Class Information Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-5">
    <h2 class="mb-4">Create New Class Collection</h2>
    <form id="classForm">
      <!-- Faculty ID -->
      <div class="mb-3">
        <label for="facultyId" class="form-label">Faculty ID</label>
        <select class="form-select" id="facultyId" name="faculty_id" required>
          <option value="">Select Faculty ID</option>
          <option value="F123">F123</option>
          <option value="F124">F124</option>
          <option value="F125">F125</option>
        </select>
      </div>

      <!-- Subject Code -->
      <div class="mb-3">
        <label for="subjectCode" class="form-label">Subject Code</label>
        <select class="form-select" id="subjectCode" name="subject_code" required>
          <option value="">Select Subject Code</option>
          <option value="GE2C25">GE2C25</option>
          <option value="CS101">CS101</option>
          <option value="MA102">MA102</option>
        </select>
      </div>

      <!-- Batch -->
      <div class="mb-3">
        <label for="batch" class="form-label">Batch</label>
        <select class="form-select" id="batch" name="batch" required>
          <option value="">Select Batch</option>
          <option value="2022-2026">2022-2026</option>
          <option value="2021-2025">2021-2025</option>
          <option value="2020-2024">2020-2024</option>
        </select>
      </div>

      <!-- Semester -->
      <div class="mb-3">
        <label for="semester" class="form-label">Semester</label>
        <select class="form-select" id="semester" name="semester" required>
          <option value="">Select Semester</option>
          <option value="1">1</option>
          <option value="2">2</option>
          <option value="3">3</option>
          <option value="4">4</option>
          <option value="5">5</option>
          <option value="6">6</option>
          <option value="7">7</option>
          <option value="8">8</option>
        </select>
      </div>

      <!-- Section -->
      <div class="mb-3">
        <label for="section" class="form-label">Section</label>
        <select class="form-select" id="section" name="section" required>
          <option value="">Select Section</option>
          <option value="A">A</option>
          <option value="B">B</option>
          <option value="C">C</option>
          <option value="D">D</option>
        </select>
      </div>

      <!-- Student Sections -->
      <div class="mb-3">
        <label for="studentSections" class="form-label">Student Sections</label>
        <select class="form-select" id="studentSections" name="student_sections" multiple required>
          <option value="A">A</option>
          <option value="B">B</option>
          <option value="C">C</option>
          <option value="D">D</option>
        </select>
        <small class="form-text text-muted">Hold Ctrl (or Command on Mac) to select multiple sections.</small>
      </div>

      <!-- Year -->
      <div class="mb-3">
        <label for="year" class="form-label">Year</label>
        <select class="form-select" id="year" name="year" required>
          <option value="">Select Year</option>
          <option value="2024">2024</option>
          <option value="2025">2025</option>
          <option value="2026">2026</option>
        </select>
      </div>

      <!-- Submit Button -->
      <button type="submit" class="btn btn-primary">Create Collection</button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
