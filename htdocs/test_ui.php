<?php
include 'libs/load.php';

$upcomingContests = Contest::showContests('upcoming');
$ongoingContests = Contest::showContests('ongoing');
$pendingParticipants = [];

foreach ($upcomingContests as $contest) {
    $pendingParticipants = array_merge($pendingParticipants, ContestRegistration::showRegistrations($contest['_id']));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard - Contest Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h2 class="mb-3">Faculty Dashboard</h2>

        <!-- Upcoming Contests -->
        <div class="card border-primary mb-3">
            <div class="card-header bg-primary text-white"><strong>Upcoming Contests</strong></div>
            <div class="card-body">
                <?php if (count($upcomingContests) > 0): ?>
                    <ul class="list-group">
                        <?php foreach ($upcomingContests as $contest): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= htmlspecialchars($contest['title']) ?></strong>
                                    <p class="text-muted small"><?= htmlspecialchars($contest['description']) ?></p>
                                </div>
                                <span class="badge bg-info">Upcoming</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No upcoming contests.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Ongoing Contests -->
        <div class="card border-success mb-3">
            <div class="card-header bg-success text-white"><strong>Ongoing Contests</strong></div>
            <div class="card-body">
                <?php if (count($ongoingContests) > 0): ?>
                    <ul class="list-group">
                        <?php foreach ($ongoingContests as $contest): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= htmlspecialchars($contest['title']) ?></strong>
                                    <p class="text-muted small"><?= htmlspecialchars($contest['description']) ?></p>
                                </div>
                                <span class="badge bg-warning">Ongoing</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No ongoing contests.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Approve Registered Participants -->
        <div class="card border-danger mb-3">
            <div class="card-header bg-danger text-white"><strong>Approve Registered Participants</strong></div>
            <div class="card-body">
                <?php if (count($pendingParticipants) > 0): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Participant</th>
                                <th>Contest</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingParticipants as $participant): ?>
                                <tr>
                                    <td><?= htmlspecialchars($participant['participant_name']) ?></td>
                                    <td><?= htmlspecialchars($participant['title']) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-success approve-btn" 
                                                data-participant-id="<?= htmlspecialchars($participant['_id']) ?>">
                                            Approve
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No pending approvals.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="approve_participant.js"></script> <!-- Separate JS file for AJAX -->
</body>
</html>
