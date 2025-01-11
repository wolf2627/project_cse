<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Mark Attendance</h2>
    
    <form id="attendanceForm" class="mb-5">
        <!-- Table for Attendance -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>S.No</th>
                        <th>Reg No</th>
                        <th>Name</th>
                        <th>Attendance</th>
                    </tr>
                </thead>
                <tbody id="attendanceTableBody">
                    <!-- Dynamically populated rows -->
                    <!-- Example row -->
                    <!-- 
                    <tr>
                        <td>1</td>
                        <td>92132213026</td>
                        <td>John Doe</td>
                        <td>
                            <select name="attendance[92132213026]" class="form-select">
                                <option value="Present">Present</option>
                                <option value="Absent">Absent</option>
                                <option value="On Duty">On Duty</option>
                            </select>
                        </td>
                    </tr>
                    -->
                </tbody>
            </table>
        </div>

        <!-- Submit Button -->
        <div class="text-center">
            <button type="submit" class="btn btn-primary">Submit Attendance</button>
        </div>
    </form>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // Example data - Replace with actual data from the API
    const students = [
        { reg_no: '92132213026', name: 'John Doe' },
        { reg_no: '92132213027', name: 'Jane Smith' },
        { reg_no: '92132213028', name: 'Alice Johnson' }
    ];

    $(document).ready(function () {
        const attendanceTableBody = $('#attendanceTableBody');
        let serialNo = 1;

        // Populate the table with student data
        students.forEach(student => {
            const row = `
                <tr>
                    <td>${serialNo}</td>
                    <td>${student.reg_no}</td>
                    <td>${student.name}</td>
                    <td>
                        <select name="attendance[${student.reg_no}]" class="form-select">
                            <option value="Present">Present</option>
                            <option value="Absent">Absent</option>
                            <option value="On Duty">On Duty</option>
                        </select>
                    </td>
                </tr>
            `;
            attendanceTableBody.append(row);
            serialNo++;
        });

        // Handle form submission
        $('#attendanceForm').submit(function (e) {
            e.preventDefault();

            const attendanceData = $(this).serializeArray();
            console.log(attendanceData); // Send this data to the API
            
            alert('Attendance submitted successfully!');
        });
    });
</script>
</body>
</html>
