<?

use Carbon\Carbon;

// Backend: Fetch attendance data
$studentId = $data['student_id'] ?? null; // Default student ID
$date = $_POST['date'] ?? Carbon::yesterday()->format('Y-m-d'); // Default to yesterday if date is not provided
$att = new Attendance();
$attendanceData = $att->calculateAttendanceByDate($studentId, $date); // Fetch attendance data

?>

<div class="container mt-4" id="date-wise-stud-atten">
    <h2 class="text-center">Date Wise Attendance</h2>

    <!-- Date Filter -->
    <form method="POST" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <input type="hidden" name="student_id" value="<?= htmlspecialchars($studentId) ?>">
                <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($date) ?>" placeholder="Select Date">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    <?php
    if (empty($attendanceData)) {
        echo "<div class='alert alert-danger'>No attendance data available</div>";
        return;
    }
    ?>

    <input type="hidden" id="attendanceData" value='<?= json_encode($attendanceData) ?>'>

    <!-- Attendance Summary -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h4>Attendance Summary</h4>
            <ul class="list-group">
                <li class="list-group-item">Total Present: <?= $attendanceData['summary']['total_present'] ?? 0 ?></li>
                <li class="list-group-item">Total Absent: <?= $attendanceData['summary']['total_absent'] ?? 0 ?></li>
                <li class="list-group-item">Total On-Duty: <?= $attendanceData['summary']['total_on_duty'] ?? 0 ?></li>
                <li class="list-group-item">Not Marked: <?= $attendanceData['summary']['total_not_marked'] ?? 0 ?></li>
            </ul>
        </div>
        <div class="col-md-6">
            <!-- Chart.js Graph -->
            <h4 class="text-center">Attendance Graph</h4>
            <div style="width: 350px; height: 250px; margin: auto;">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Session-wise Attendance -->
    <div class="table-responsive">
        <h4>Session-wise Attendance</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Time Slot</th>
                    <th>Subject Code</th>
                    <th>Status</th>
                    <th>Marked</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $slots = Essentials::loadtimeTableSlots();
                if (!empty($attendanceData['sessions'])):
                    // Sort sessions based on the predefined slots
                    usort($attendanceData['sessions'], function ($a, $b) use ($slots) {
                        $slotA = array_search($a['time_slot'], $slots);
                        $slotB = array_search($b['time_slot'], $slots);
                        return $slotA <=> $slotB;
                    });
                ?>
                    <?php foreach ($attendanceData['sessions'] as $session): ?>
                        <tr>
                            <td><?= htmlspecialchars($session['time_slot']) ?></td>
                            <td><?= htmlspecialchars($session['subject_code'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($session['status']) ?></td>
                            <td><?= htmlspecialchars($session['marked_at'] && ($session['status'] != "Not Marked") ? htmlspecialchars(Carbon::parse($session['marked_at'])->format('d-m-Y')) : 'N/A') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No attendance data available</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    
</script>