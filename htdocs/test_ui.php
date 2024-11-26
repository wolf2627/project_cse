<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Test Selection</title>
  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >
</head>
<body>
  <div class="container mt-5">
    <h2 class="mb-4">Select a Test</h2>
    <form>
      <!-- Testname Dropdown -->
      <div class="mb-3">
        <label for="testname" class="form-label">Test Name</label>
        <select class="form-select" id="testname" required>
          <option value="673b3494bf7730ef8c0612b3" selected>Serial Test 1</option>
          <!-- Add more test options here -->
        </select>
      </div>

      <!-- Batch Year Dropdown -->
      <div class="mb-3">
        <label for="batch" class="form-label">Batch Year</label>
        <select class="form-select" id="batch" required>
          <option value="2024-2028" selected>2024-2028</option>
          <!-- Add more batch options here -->
        </select>
      </div>

      <!-- Semester Dropdown -->
      <div class="mb-3">
        <label for="semester" class="form-label">Semester</label>
        <select class="form-select" id="semester" required>
          <option value="1" selected>1</option>
          <!-- Add more semester options here -->
        </select>
      </div>

      <!-- Submit Button -->
      <button type="submit" class="btn btn-primary">Submit</button>
    </form>
  </div>

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
