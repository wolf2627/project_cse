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

<div class="container mt-4" id="summary-attendance">

    <input type="hidden" id="studentId" value="<?= htmlspecialchars($studentId) ?>">
    <input type="hidden" id="summ-subjects" value="<?= htmlspecialchars(json_encode($subjects)) ?>">
    <input type="hidden" id="summ-percentages" value="<?= htmlspecialchars(json_encode($percentages)) ?>">

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