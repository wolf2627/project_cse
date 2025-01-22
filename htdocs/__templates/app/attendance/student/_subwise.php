<?

use Carbon\Carbon;

// Backend: Fetch subject-wise attendance data
$studentId = $data['student_id'] ?? null;

$att = new Attendance();
$attendanceData = $att->calculateAttendanceSubjectWise($studentId);

// Extract overall attendance percentage and subject attendance
$overallAttendancePercentage = $attendanceData['overallAttendancePercentage'] ?? 0;
$subjectAttendance = $attendanceData['subjectAttendance'] ?? [];

// Prepare data for Chart.js
$subjects = [];
$percentages = [];
foreach ($subjectAttendance as $subjectCode => $subject) {
    $subjects[] = $subjectCode;
    $percentages[] = $subject['percentage'] ?? 0;
}
?>


<style>
    #summ-att-stud {
        cursor: pointer;
    }

    #detailsTable-sum-att-stud th,
    #detailsTable-sum-att-stud td {
        text-align: center;
    }

    #attendanceBarChart {
        max-height: 300px;
    }

    #attendancePieChart {
        max-height: 250px;
        max-width: 250px;
        margin: 0 auto;
    }
</style>

<div class="container mt-4">
    <h2 class="text-center">Attendance Summary</h2>

    <!-- Overall Attendance -->
    <div class="alert alert-info text-center" role="alert">
        <h4>Overall Attendance Percentage: <?= htmlspecialchars($overallAttendancePercentage) ?>%</h4>
    </div>

    <!-- Summary Table -->
    <div class="table-responsive">
        <table class="table table-bordered" id="detailsTable-sum-att-stud">
            <thead>
                <tr>
                    <th>+</th>
                    <th>Subject Code</th>
                    <th>Course Name</th>
                    <th>No. of Periods Conducted</th>
                    <th>Periods Attended</th>
                    <th>On-Duty</th>
                    <!-- <th>Medical Leave</th>
                    <th>Restricted Holiday</th>
                    <th>Extra Hours</th> -->
                    <th>Attendance Percentage</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subjectAttendance as $subjectCode => $subject): ?>
                    <tr class="collapse-header" id="summ-att-stud" data-bs-toggle="collapse" data-bs-target="#details-<?= htmlspecialchars($subjectCode) ?>">
                        <td><button class="btn btn-sm btn-outline-primary">+</button></td>
                        <td><?= htmlspecialchars($subjectCode) ?></td>
                        <td><?= htmlspecialchars($subject['name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($subject['total'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($subject['attended'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($subject['on_duty'] ?? 0) ?></td>
                        <!-- <td>0</td>  -->
                        <!-- <td>0</td>  -->
                        <!-- <td><?= htmlspecialchars($subject['extra_hours'] ?? 0) ?></td>  -->
                        <td><?= htmlspecialchars($subject['percentage'] ?? 0) ?>%</td>
                    </tr>
                    <tr class="collapse bg-light" id="details-<?= htmlspecialchars($subjectCode) ?>">
                        <td colspan="10">
                            <h6>Detailed View</h6>
                            <table class="table table-bordered" id="detailsTable-sum-att-stud">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time Slot</th>
                                        <th>Status</th>
                                        <!-- <th>Session ID</th>
                                        <th>Marked At</th> -->
                                    </tr>
                                </thead>
                                <tbody id="detailsBody-<?= htmlspecialchars($subjectCode) ?>">
                                    <?php foreach ($subject['sessions'] as $date => $sessions): ?>
                                        <?php foreach ($sessions as $timeSlot => $session): ?>
                                            <tr>
                                                <td><?= htmlspecialchars(Carbon::parse($date)->format('d-m-Y')) ?></td>
                                                <td><?= htmlspecialchars($timeSlot) ?></td>
                                                <td><?= htmlspecialchars($session['status'] ?? 'N/A') ?></td>
                                                <!-- <td><?= htmlspecialchars($session['session_id'] ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($session['marked_at'] ?? 'N/A') ?></td> -->
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center">
                                <nav>
                                    <ul class="pagination" id="pagination-<?= htmlspecialchars($subjectCode) ?>">
                                        <!-- Pagination buttons added dynamically -->
                                    </ul>
                                </nav>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Bar and Pie Charts -->
    <div class="row mt-5">
        <div class="col-md-6">
            <h4 class="text-center">Subject-wise Attendance Percentage</h4>
            <canvas id="attendanceBarChart"></canvas>
        </div>
        <div class="col-md-6">
            <h4 class="text-center">Subject Contributions to Overall Attendance</h4>
            <canvas id="attendancePieChart"></canvas>
        </div>
    </div>
</div>

<script>
    // Detailed View Pagination
    const rowsPerPage = 5;

    document.querySelectorAll('tbody[id^="detailsBody-"]').forEach(body => {
        const rows = Array.from(body.children);
        const subjectCode = body.id.split('-')[1];
        const pagination = document.getElementById(`pagination-${subjectCode}`);

        function renderTable(page) {
            const start = (page - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            rows.forEach((row, index) => {
                row.style.display = index >= start && index < end ? '' : 'none';
            });
        }

        function renderPagination() {
            const totalPages = Math.ceil(rows.length / rowsPerPage);
            pagination.innerHTML = '';
            for (let i = 1; i <= totalPages; i++) {
                const li = document.createElement('li');
                li.className = 'page-item';
                li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                li.addEventListener('click', () => {
                    renderTable(i);
                    pagination.querySelectorAll('.page-item').forEach(el => el.classList.remove('active'));
                    li.classList.add('active');
                });
                pagination.appendChild(li);
            }
            if (pagination.firstChild) pagination.firstChild.classList.add('active');
            renderTable(1);
        }

        renderPagination();
    });

    // Bar Chart Data
    const barChartData = {
        labels: <?= json_encode($subjects) ?>,
        datasets: [{
            label: 'Attendance Percentage',
            data: <?= json_encode($percentages) ?>,
            backgroundColor: '#4e73df',
            borderColor: '#375a7f',
            borderWidth: 1
        }]
    };

    // Bar Chart Config
    const barChartConfig = {
        type: 'bar',
        data: barChartData,
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Subjects'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Attendance (%)'
                    },
                    max: 100
                }
            }
        }
    };

    // Pie Chart Data
    const pieChartData = {
        labels: <?= json_encode($subjects) ?>,
        datasets: [{
            label: 'Subject Contribution',
            data: <?= json_encode($percentages) ?>,
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69']
        }]
    };

    // Pie Chart Config
    const pieChartConfig = {
        type: 'pie',
        data: pieChartData,
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: ctx => `${ctx.raw}%`
                    }
                }
            }
        }
    };

    // Render Charts
    new Chart(document.getElementById('attendanceBarChart'), barChartConfig);
    new Chart(document.getElementById('attendancePieChart'), pieChartConfig);
</script>